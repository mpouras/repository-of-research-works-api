<?php

namespace App\Listeners;

use App\Events\ArticleAuthorsEvent;
use App\Models\Author;

class ArticleAuthorsListener
{
    public function handle(ArticleAuthorsEvent $event)
    {
        if ($event->action === 'attach') {
            $this->attachAuthor($event);
        } elseif ($event->action === 'detach') {
            $this->detachAuthor($event);
        }
    }

    private function attachAuthor(ArticleAuthorsEvent $event)
    {
        foreach ($event->authors as $authorData) {
            $author = Author::where('name', $authorData['name'])->first();

            if (!$author) {
                $author = Author::create([
                    'name' => $authorData['name'],
                    'university' => $authorData['university'] ?? null,
                    'profile_link' => $authorData['profile_link'],
                    'orcid_link' => $authorData['orcid_link'] ?? null,
                ]);
            }

            if (!$event->article->authors()->where('author_id', $author->id)->exists()) {
                $event->article->authors()->attach($author->id);
            }
        }
    }

    private function detachAuthor(ArticleAuthorsEvent $event)
    {
        foreach ($event->authors as $authorData) {
            $author = Author::where('name', $authorData['name'])->first();

            if ($author) {
                $event->article->authors()->detach($author->id);

                if ($author->articles()->count() === 0) {
                    $author->delete();
                }
            }
        }
    }
}
