<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserInformation extends Model
{
    protected $fillable = [
        'user_id',
        'birthday',
        'gender',
        'country',
        'bio',
        'linkedin_url',
        'photo',
    ];

    protected $touches = ['user'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
