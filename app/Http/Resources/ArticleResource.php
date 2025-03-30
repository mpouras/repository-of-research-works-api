<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
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
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'title' => $this->title,
            'description' => $this->description,
            'published_date' => $this->published_date,
            'link' => $this->link,
            'doi' => $this->doi,
            'pdf_link' => $this->pdf_link,
            'authors' => $this->authors->map(function ($author) {
                return [
                    'id' => $author->id,
                    'name' => $author->name,
                    'university' => $author->university,
                    'profile_link' => $author->profile_link,
                    'orcid_link' => $author->orcid_link,
                ];
            }),
            'keywords' => $this->keywords->map(function ($keyword) {
                return [
                    'id' => $keyword->id,
                    'name' => $keyword->name,
                ];
            }),
            'issue' => [
                'id' => $this->issue->id,
                'name' => $this->issue->name,
            ],
            'volume' => [
                'id' => $this->issue->volume->id,
                'number' => $this->issue->volume->number,
            ],
            'publication' => [
                'id' => $this->issue->volume->publication->id,
                'title' => $this->issue->volume->publication->title,
            ],
        ];
    }
}
