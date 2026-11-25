<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Media extends Model
{
    use HasFactory;

    protected $table = 'media';

    protected $fillable = [
        'title',
        'type',
        'release_year',
        'episode_count',
        'genre',
    ];

    protected function casts(): array
    {
        return [
            'release_year' => 'integer',
            'episode_count' => 'integer',
        ];
    }

    public function viewers(): BelongsToMany
    {
        return $this->belongsToMany(Viewer::class, 'media_viewer')->withTimestamps();
    }
}

