<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Issue extends Model
{
    use HasFactory;

    protected $fillable = [
        'volume_id',
        'name',
        'month_published',
    ];

    protected $touches = ['volume'];

    public function volume() : BelongsTo
    {
        return $this->belongsTo(Volume::class);
    }

    public function articles() : HasMany
    {
        return $this->hasMany(Article::class);
    }
}
