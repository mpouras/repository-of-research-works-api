<?php

namespace App\Models;

use App\Helpers\Paginatable;
use App\Helpers\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Publisher extends Model
{
    use HasFactory, Sortable, Paginatable;

    protected $fillable = [
        'name',
        'scraper'
    ];

    public function publications() : BelongsToMany
    {
        return $this->belongsToMany(Publication::class, 'publication_publisher')->withTimestamps();
    }
}
