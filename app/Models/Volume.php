<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Volume extends Model
{
    use HasFactory;

    protected $fillable = [
        'publication_id',
        'number',
        'year_published',
    ];

    protected $touches = ['publication'];

    public function publication() : BelongsTo
    {
        return $this->belongsTo(Publication::class);
    }

    public function issues() : HasMany
    {
        return $this->hasMany(Issue::class);
    }
}
