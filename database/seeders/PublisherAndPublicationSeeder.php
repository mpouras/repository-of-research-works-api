<?php

namespace Database\Seeders;

use App\Models\Publication;
use App\Models\Publisher;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PublisherAndPublicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $publishers = Publisher::factory()->count(3)->create();

        Publication::factory()
            ->count(3)
            ->withVolumesForAllYears()
            ->withIssuesForVolumes()
            ->withArticlesForIssues()
            ->create()
            ->each(function ($publication) use ($publishers) {
                $publication->publishers()->attach(
                    $publishers->random(rand(1, 2))->pluck('id')
                );
            });
    }
}
