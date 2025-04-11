<?php

namespace App\Listeners;

use App\Events\ArticleKeywordsEvent;
use App\Models\Keyword;

class ArticleKeywordsListener
{
    public function handle(ArticleKeywordsEvent $event)
    {
        if ($event->action === 'attach') {
            $this->attachKeyword($event);
        } elseif ($event->action === 'detach') {
            $this->detachKeyword($event);
        }
    }

    private function attachKeyword(ArticleKeywordsEvent $event)
    {
        foreach ($event->keywords as $keywordData) {
            $keywordName = $keywordData['name'] ?? '';

            if (mb_strlen($keywordName) > 255) {
                continue;
            }

            $keyword = Keyword::firstOrCreate([
                'name' => $keywordName,
            ]);

            if (!$event->article->keywords()->where('keyword_id', $keyword->id)->exists()) {
                $event->article->keywords()->attach($keyword->id);
            }
        }
    }

    private function detachKeyword(ArticleKeywordsEvent $event)
    {
        foreach ($event->keywords as $keywordData) {
            $keyword = Keyword::where('name', $keywordData['name'])->first();

            if ($keyword) {
                $event->article->keywords()->detach($keyword->id);

                if ($keyword->articles()->count() === 0) {
                    $keyword->delete();
                }
            }
        }
    }
}
