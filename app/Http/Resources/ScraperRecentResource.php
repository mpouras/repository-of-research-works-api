<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScraperRecentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'issn' => $this->issn,
            'link' => $this->link,
            'recent_volume' => $this->recent_volume ? [
                'number' => $this->recent_volume->number,
                'year_published' => $this->recent_volume->year_published,
            ] : null,
            'recent_issue' => $this->recent_volume?->recent_issue ? [
                'name' => $this->recent_volume->recent_issue->name,
                'month_published' => $this->recent_volume->recent_issue->month_published,
                'articles' => collect($this->recent_volume->recent_issue->recent_articles)->map(function ($article) {
                    return [
                        'link' => $article->link,
                    ];
                }),
            ] : null,
            'recent_article' => $this->recent_volume->recent_issue->recent_article->link ?? null,
        ];
    }
}
