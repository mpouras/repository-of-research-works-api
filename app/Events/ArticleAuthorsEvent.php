<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Article;

class ArticleAuthorsEvent
{
    use Dispatchable, SerializesModels;

    public $article;
    public $authors;
    public $action;

    public function __construct(Article $article, array $authors, string $action)
    {
        $this->article = $article;
        $this->authors = $authors;
        $this->action = $action;
    }
}
