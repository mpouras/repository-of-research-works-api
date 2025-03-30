<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Keyword;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KeywordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $keywords = [
            'Technology', 'Science', 'Innovation', 'Health', 'Business', 'Education', 'Research',
            'Engineering', 'Mathematics', 'Artificial', 'Intelligence', 'Data', 'Cloud', 'Sustainability',
            'Quantum', 'Robotics', 'AI', 'Blockchain', 'Energy', 'Environment'
        ];

        foreach ($keywords as $keyword) {
            Keyword::firstOrCreate(['name' => $keyword]);
        }

        $articles = Article::all();

        foreach ($articles as $article) {
            $randomKeywords = Keyword::inRandomOrder()->take(3)->pluck('id');
            $article->keywords()->attach($randomKeywords);
        }
    }
}
