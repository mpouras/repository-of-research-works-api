<?php

namespace App\Models;

use App\Helpers\Paginatable;
use App\Helpers\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model
{
    use HasFactory, Sortable, Paginatable;

    protected $fillable = [
        'issue_id',
        'title',
        'description',
        'published_date',
        'link',
        'doi',
        'pdf_link'
    ];

    protected $touches = ['issue'];

    public function issue(): BelongsTo
    {
        return $this->belongsTo(Issue::class);
    }

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class, 'article_author')->withTimestamps();
    }

    public function keywords(): BelongsToMany
    {
        return $this->belongsToMany(Keyword::class, 'article_keyword')->withTimestamps();
    }

    public function savedByUsers(): HasMany
    {
        return $this->hasMany(UserLibrary::class);
    }
}
