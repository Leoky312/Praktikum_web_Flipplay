<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\Viewer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlipPlayApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_list_search_show_update_and_delete_media(): void
    {
        // Store (happy path) -> 201
        $createResponse = $this->postJson('/api/media', [
            'title' => 'Attack on Titan',
            'type' => 'anime',
            'release_year' => 2013,
            'episode_count' => 87,
            'genre' => 'Action, Dark Fantasy',
        ]);

        $createResponse->assertStatus(201)
            ->assertJsonPath('data.title', 'Attack on Titan')
            ->assertJsonPath('data.type', 'anime')
            ->assertJsonPath('data.release_year', 2013)
            ->assertJsonPath('data.episode_count', 87)
            ->assertJsonPath('data.genre', 'Action, Dark Fantasy')
            ->assertJsonMissingPath('data.viewers');

        $mediaId = $createResponse->json('data.id');

        // Store another media
        $this->postJson('/api/media', [
            'title' => 'Spirited Away',
            'type' => 'movie',
            'release_year' => 2001,
            'genre' => 'Fantasy',
        ])->assertStatus(201);

        // Index (all) -> 200
        $this->getJson('/api/media')
            ->assertStatus(200)
            ->assertJsonCount(2, 'data');

        // Index with search query filter -> 200
        $this->getJson('/api/media?search=Titan')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Attack on Titan');

        // Show with whenLoaded('viewers') -> 200
        $this->getJson("/api/media/{$mediaId}")
            ->assertStatus(200)
            ->assertJsonPath('data.id', $mediaId)
            ->assertJsonStructure(['data' => ['id', 'title', 'type', 'release_year', 'episode_count', 'genre', 'viewers']]);

        // Update (PATCH) -> 200
        $this->patchJson("/api/media/{$mediaId}", [
            'episode_count' => 89,
            'genre' => 'Action, Drama',
        ])->assertStatus(200)
            ->assertJsonPath('data.episode_count', 89)
            ->assertJsonPath('data.genre', 'Action, Drama');

        // Delete -> 200
        $this->deleteJson("/api/media/{$mediaId}")
            ->assertStatus(200)
            ->assertJsonPath('message', 'Media berhasil dihapus.');

        $this->assertDatabaseMissing('media', ['id' => $mediaId]);
    }

    public function test_media_validation_fails_on_empty_or_duplicate_title(): void
    {
        Media::create([
            'title' => 'Solo Leveling',
            'type' => 'anime',
            'release_year' => 2024,
            'episode_count' => 12,
        ]);

        // Empty title -> 422
        $this->postJson('/api/media', [
            'title' => '',
            'type' => 'anime',
            'release_year' => 2024,
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['title']);

        // Duplicate title -> 422
        $this->postJson('/api/media', [
            'title' => 'Solo Leveling',
            'type' => 'anime',
            'release_year' => 2024,
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    public function test_viewer_can_attach_view_and_detach_media_in_watchlist(): void
    {
        $viewer = Viewer::create(['name' => 'Vicky', 'email' => 'vicky@flipplay.test']);
        $media = Media::create([
            'title' => 'Jujutsu Kaisen',
            'type' => 'anime',
            'release_year' => 2020,
            'episode_count' => 24,
            'genre' => 'Action',
        ]);

        // Attach media to viewer watchlist -> 201
        $this->postJson("/api/viewers/{$viewer->id}/watchlist", [
            'media_id' => $media->id,
        ])->assertStatus(201)
            ->assertJsonPath('message', 'Media berhasil ditambahkan ke koleksi pengguna.')
            ->assertJsonCount(1, 'data.watchlist')
            ->assertJsonPath('data.watchlist.0.title', 'Jujutsu Kaisen');

        // Duplicate attach -> 422
        $this->postJson("/api/viewers/{$viewer->id}/watchlist", [
            'media_id' => $media->id,
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['media_id']);

        // Show media includes loaded viewers
        $this->getJson("/api/media/{$media->id}")
            ->assertStatus(200)
            ->assertJsonCount(1, 'data.viewers')
            ->assertJsonPath('data.viewers.0.name', 'Vicky');

        // Get viewer watchlist -> 200
        $this->getJson("/api/viewers/{$viewer->id}/watchlist")
            ->assertStatus(200)
            ->assertJsonCount(1, 'data.watchlist');

        // Detach media from watchlist -> 200
        $this->deleteJson("/api/viewers/{$viewer->id}/watchlist/{$media->id}")
            ->assertStatus(200)
            ->assertJsonPath('message', 'Media berhasil dihapus dari koleksi pengguna.')
            ->assertJsonCount(0, 'data.watchlist');

        // Detach again (not in watchlist) -> 404
        $this->deleteJson("/api/viewers/{$viewer->id}/watchlist/{$media->id}")
            ->assertStatus(404);
    }
}

