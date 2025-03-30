<?php

namespace App\Models;

use App\Helpers\Paginatable;
use App\Helpers\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Keyword extends Model
{
    use HasFactory, Sortable, Paginatable;

    protected $fillable = [
        'name'
    ];

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_keyword')->withTimestamps();
    }
}
