<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Viewer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
    ];

    public function watchlist(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'media_viewer')->withTimestamps();
    }
}

