<?php

namespace App\Models;

use App\Helpers\Paginatable;
use App\Helpers\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Publication extends Model
{
    use HasFactory, Sortable, Paginatable;

    protected $fillable = [
        'type',
        'title',
        'issn',
        'description',
        'link',
        'year_published',
    ];

    protected $touches = ['publishers'];

    public function publishers(): BelongsToMany
    {
        return $this->belongsToMany(Publisher::class, 'publication_publisher')->withTimestamps();
    }

    public function volumes(): HasMany
    {
        return $this->hasMany(Volume::class);
    }
}
