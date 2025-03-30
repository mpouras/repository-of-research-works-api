<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Issue;
use App\Models\Volume;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Publication>
 */
class PublicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement(['Journal', 'Magazine', 'Book']),
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'link' => $this->faker->url,
            'issn' => $this->faker->regexify('[0-9]{4}-[0-9]{3}[0-9X]'),
            'year_published' => $this->faker->numberBetween(1990, now()->year),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function withVolumesForAllYears()
    {
        return $this->afterCreating(function ($publication) {
            $startYear = max(2010, $publication->year_published);

            // If year_published is 2008, the first volume will have number 3 for 2010
            // If year_published is 2000, the first volume will have number 11 for 2010
            // If year_published is 2015, the first volume will have number 1 for 2015
            $startVolumeNumber = max(1, 2010 - $publication->year_published + 1);

            $years = range($startYear, now()->year);

            foreach ($years as $index => $year) {
                Volume::factory()->create([
                    'publication_id' => $publication->id,
                    'number' => $startVolumeNumber + $index,
                    'year_published' => $year,
                ]);
            }
        });
    }

    public function withIssuesForVolumes()
    {
        return $this->afterCreating(function ($publication) {
            $volumes = $publication->volumes;

            foreach ($volumes as $volume) {
                $numberOfIssues = rand(3, 6);

                for ($i = 1; $i <= $numberOfIssues; $i++) {
                    Issue::factory()->create([
                        'volume_id' => $volume->id,
                        'name' => rand(0, 1)
                            ? (string)$i
                            : substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 5),
                        'month_published' => rand(1, 12),
                    ]);
                }
            }
        });
    }

    public function withArticlesForIssues()
    {
        return $this->afterCreating(function ($publication) {
            $volumes = $publication->volumes;

            foreach ($volumes as $volume) {
                $issues = $volume->issues;

                foreach ($issues as $issue) {
                    $numberOfArticles = rand(15, 90);

                    for ($i = 1; $i <= $numberOfArticles; $i++) {
                        $month = str_pad($issue->month_published, 2, '0', STR_PAD_LEFT);

                        $publishedDate = $this->faker->dateTimeBetween(
                            "{$volume->year_published}-{$month}-01",
                            "{$volume->year_published}-{$month}-31"
                        );

                        Article::factory()->create([
                            'issue_id' => $issue->id,
                            'title' => $this->faker->sentence,
                            'description' => $this->faker->paragraph,
                            'link' => $this->faker->url,
                            'doi' => 'https://doi.gr/10.' . $this->faker->numberBetween(10000, 99999) . '/' . $this->faker->regexify('[0-9]{10}'),
                            'pdf_link' => rand(1, 5) > 1 ? 'https://pdf.example/' . $this->faker->uuid : null,
                            'published_date' => $publishedDate,
                        ]);
                    }
                }
            }
        });
    }
}
