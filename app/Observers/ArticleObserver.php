<?php

namespace App\Observers;

use App\Events\ArticleAuthorsEvent;
use App\Events\ArticleKeywordsEvent;
use App\Models\Article;

class ArticleObserver
{
    public function deleting(Article $article)
    {
        event(new ArticleAuthorsEvent($article, $article->authors->toArray(), 'detach'));
        event(new ArticleKeywordsEvent($article, $article->keywords->toArray(), 'detach'));
    }
}
