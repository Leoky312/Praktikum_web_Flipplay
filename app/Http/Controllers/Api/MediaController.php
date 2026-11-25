<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMediaRequest;
use App\Http\Requests\UpdateMediaRequest;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MediaController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $keyword = $request->query('search', $request->query('title'));

        $media = Media::query()
            ->when($keyword, function ($query, $keyword) {
                $query->where('title', 'like', "%{$keyword}%");
            })
            ->latest('id')
            ->get();

        return MediaResource::collection($media);
    }

    public function store(StoreMediaRequest $request): JsonResponse
    {
        $media = Media::create($request->validated());

        return (new MediaResource($media))
            ->additional(['message' => 'Media berhasil ditambahkan.'])
            ->response()
            ->setStatusCode(201);
    }

    public function show(Media $media): MediaResource
    {
        $media->load('viewers');

        return new MediaResource($media);
    }

    public function update(UpdateMediaRequest $request, Media $media): MediaResource
    {
        $media->update($request->validated());

        return (new MediaResource($media))
            ->additional(['message' => 'Media berhasil diperbarui.']);
    }

    public function destroy(Media $media): JsonResponse
    {
        $media->delete();

        return response()->json([
            'message' => 'Media berhasil dihapus.',
        ], 200);
    }
}

