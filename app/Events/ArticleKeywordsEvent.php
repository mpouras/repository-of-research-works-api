<?php

namespace App\Events;

use App\Models\Article;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ArticleKeywordsEvent
{
    use Dispatchable, SerializesModels;

    public $article;
    public $keywords;
    public $action;

    public function __construct(Article $article, array $keywords, string $action)
    {
        $this->article = $article;
        $this->keywords = $keywords;
        $this->action = $action;
    }
}
