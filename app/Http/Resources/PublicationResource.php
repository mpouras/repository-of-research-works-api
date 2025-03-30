<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicationResource extends JsonResource
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
            'type' => $this->type,
            'title' => $this->title,
            'issn' => $this->issn,
            'description' => $this->description,
            'link' => $this->link,
            'year_published' => $this->year_published,
            'publishers' => $this->publishers->map(function ($publisher) {
                return [
                    'id' => $publisher->id,
                    'name' => $publisher->name
                ];
            }),
            'volumes' => $this->volumes->map(function ($volume) {
                return [
                    'id' => $volume->id,
                    'number' => $volume->number,
                    'year_published' => $volume->year_published,
                    'issues' => $volume->issues->map(function ($issue) {
                        return [
                            'id' => $issue->id,
                            'name' => $issue->name,
                            'month_published' => $issue->month_published,
                        ];
                    })
                ];
            })
        ];
    }
}
