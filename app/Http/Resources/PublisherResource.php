<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublisherResource extends JsonResource
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
            'name' => $this->name,
            'scraper' => $this->scraper,
            'publications' => $this->publications->map(function ($publication) {
                return [
                    'id' => $publication->id,
                    'title' => $publication->title,
                ];
            })->values()
        ];
    }
}
