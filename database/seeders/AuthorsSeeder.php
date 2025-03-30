<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Author;
use Illuminate\Database\Seeder;

class AuthorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Author::factory()->count(30)->create();

        $articles = Article::all();

        foreach ($articles as $article) {
            $randomAuthors = Author::inRandomOrder()->take(3)->pluck('id');
            $article->authors()->attach($randomAuthors);
        }
    }
}
