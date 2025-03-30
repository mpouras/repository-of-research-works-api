<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->role === 'user' && !$this->relationLoaded('info')) {
            $this->load('info');
        }

        return [
            'id' => $this->id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'username' => $this->username,
            'email' => $this->email,
            'email_verified' => $this->hasVerifiedEmail(),
            'email_verified_at' => $this->email_verified_at,
            'role' => $this->role,
            'status' => $this->status,
            'birthday' => optional($this->info)->birthday,
            'gender' => optional($this->info)->gender,
            'country' => optional($this->info)->country,
            'bio' => optional($this->info)->bio,
            'linkedin_url' => optional($this->info)->linkedin_url,
            'photo' => $this->info && $this->info->photo ? Storage::url($this->info->photo) : null,
            'unread_notifications_count' => $this->notifications()->whereNull('read_at')->count(),
        ];
    }
}
