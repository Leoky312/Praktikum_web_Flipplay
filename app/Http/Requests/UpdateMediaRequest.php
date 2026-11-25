<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $media = $this->route('media');
        $mediaId = is_object($media) ? $media->id : $media;

        return [
            'title' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('media', 'title')->ignore($mediaId)],
            'type' => ['sometimes', 'required', 'string', 'in:anime,media,movie,series'],
            'release_year' => ['sometimes', 'required', 'integer', 'min:1900', 'max:2100'],
            'episode_count' => ['nullable', 'integer', 'min:1'],
            'genre' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul media tidak boleh kosong.',
            'title.unique' => 'Judul media sudah digunakan oleh media lain.',
            'type.in' => 'Jenis media harus salah satu dari: anime, media, movie, atau series.',
            'release_year.integer' => 'Tahun rilis harus berupa angka.',
            'episode_count.integer' => 'Jumlah episode harus berupa angka.',
            'episode_count.min' => 'Jumlah episode minimal 1.',
        ];
    }
}

