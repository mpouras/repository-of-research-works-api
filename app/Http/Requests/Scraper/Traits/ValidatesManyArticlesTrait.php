<?php

namespace App\Http\Requests\Scraper\Traits;

use Illuminate\Support\Facades\Validator;

trait ValidatesManyArticlesTrait
{
    protected array $validArticles = [];
    protected array $skippedArticles = [];

    public function validateResolved(): void
    {
        $rules = $this->rules();

        foreach ($this->all() as $article) {
            $validator = Validator::make($article, $rules);

            if ($validator->fails()) {
                $this->skippedArticles[] = $article['title'];
            } else {
                $this->validArticles[] = $article;
            }
        }
    }

    public function validArticles(): array
    {
        return $this->validArticles;
    }

    public function skippedArticles(): array
    {
        return $this->skippedArticles;
    }
}
