<?php

namespace Database\Seeders;

use App\Models\Publication;
use App\Models\Publisher;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class PublicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @throws \Exception
     */
    public function run(): void
    {
        $publishers = Publisher::all();

        if ($publishers->isEmpty()) {
            Log::error('No publishers found. Please seed publishers first.');
            throw new \Exception('No publishers found. Seed publishers before running PublicationSeeder.');
        }

        Publication::factory()
            ->count(25)
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
