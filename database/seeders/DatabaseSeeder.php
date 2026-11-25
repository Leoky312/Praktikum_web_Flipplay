<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\Viewer;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $jjk = Media::firstOrCreate(
            ['title' => 'Jujutsu Kaisen'],
            [
                'type' => 'anime',
                'release_year' => 2020,
                'episode_count' => 24,
                'genre' => 'Action, Supernatural',
            ]
        );

        $soloLeveling = Media::firstOrCreate(
            ['title' => 'Solo Leveling'],
            [
                'type' => 'anime',
                'release_year' => 2024,
                'episode_count' => 12,
                'genre' => 'Action, Fantasy',
            ]
        );

        Media::firstOrCreate(
            ['title' => "Frieren: Beyond Journey's End"],
            [
                'type' => 'anime',
                'release_year' => 2023,
                'episode_count' => 28,
                'genre' => 'Adventure, Fantasy',
            ]
        );

        $vicky = Viewer::firstOrCreate(
            ['email' => 'vicky@flipplay.test'],
            ['name' => 'Vicky']
        );

        Viewer::firstOrCreate(
            ['email' => 'dina@flipplay.test'],
            ['name' => 'Dina']
        );

        $vicky->watchlist()->syncWithoutDetaching([$jjk->id, $soloLeveling->id]);
    }
}
