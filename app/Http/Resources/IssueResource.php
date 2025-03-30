<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IssueResource extends JsonResource
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
            'name' => $this->name,
            'month_published' => $this->month_published,
            'volume_id' => $this->volume->id,
            'volume_number' => $this->volume->number,
            'publication_id' => $this->volume->publication->id
        ];
    }
}
