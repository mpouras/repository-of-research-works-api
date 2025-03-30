<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthorResource extends JsonResource
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
            'university' => $this->university,
            'profile_link' => $this->profile_link,
            'orcid_link' => $this->orcid_link,
            'articles' => ArticleResource::collection($this->articles_paginated),
            'meta' => [
                'current_page' => $this->articles_paginated->currentPage(),
                'per_page' => $this->articles_paginated->perPage(),
                'total' => $this->articles_paginated->total(),
                'last_page' => $this->articles_paginated->lastPage(),
            ],
        ];
    }
}
