<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddToWatchlistRequest;
use App\Http\Requests\StoreViewerRequest;
use App\Http\Resources\ViewerResource;
use App\Models\Media;
use App\Models\Viewer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ViewerController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $viewers = Viewer::with('watchlist')->get();

        return ViewerResource::collection($viewers);
    }

    public function store(StoreViewerRequest $request): JsonResponse
    {
        $viewer = Viewer::create($request->validated());

        return (new ViewerResource($viewer))
            ->additional(['message' => 'Pengguna berhasil dibuat.'])
            ->response()
            ->setStatusCode(201);
    }

    public function show(Viewer $viewer): ViewerResource
    {
        $viewer->load('watchlist');

        return new ViewerResource($viewer);
    }

    public function watchlist(Viewer $viewer): ViewerResource
    {
        $viewer->load('watchlist');

        return new ViewerResource($viewer);
    }

    public function addToWatchlist(AddToWatchlistRequest $request, Viewer $viewer): JsonResponse
    {
        $mediaId = (int) $request->validated('media_id');

        if ($viewer->watchlist()->where('media_id', $mediaId)->exists()) {
            return response()->json([
                'message' => 'Media sudah ada di dalam koleksi pengguna.',
                'errors' => [
                    'media_id' => ['Media sudah ada di dalam koleksi pengguna.'],
                ],
            ], 422);
        }

        $viewer->watchlist()->attach($mediaId);
        $viewer->load('watchlist');

        return (new ViewerResource($viewer))
            ->additional(['message' => 'Media berhasil ditambahkan ke koleksi pengguna.'])
            ->response()
            ->setStatusCode(201);
    }

    public function removeFromWatchlist(Viewer $viewer, Media $media): JsonResponse
    {
        if (! $viewer->watchlist()->where('media_id', $media->id)->exists()) {
            return response()->json([
                'message' => 'Media tidak ditemukan di dalam koleksi pengguna.',
            ], 404);
        }

        $viewer->watchlist()->detach($media->id);
        $viewer->load('watchlist');

        return (new ViewerResource($viewer))
            ->additional(['message' => 'Media berhasil dihapus dari koleksi pengguna.'])
            ->response()
            ->setStatusCode(200);
    }
}

