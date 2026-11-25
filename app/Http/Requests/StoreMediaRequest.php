<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255', 'unique:media,title'],
            'type' => ['required', 'string', 'in:anime,media,movie,series'],
            'release_year' => ['required', 'integer', 'min:1900', 'max:2100'],
            'episode_count' => ['nullable', 'integer', 'min:1'],
            'genre' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul media wajib diisi.',
            'title.unique' => 'Judul media sudah terdaftar dan tidak boleh duplikat.',
            'type.required' => 'Jenis media wajib diisi.',
            'type.in' => 'Jenis media harus salah satu dari: anime, media, movie, atau series.',
            'release_year.required' => 'Tahun rilis wajib diisi.',
            'release_year.integer' => 'Tahun rilis harus berupa angka.',
            'episode_count.integer' => 'Jumlah episode harus berupa angka.',
            'episode_count.min' => 'Jumlah episode minimal 1.',
        ];
    }
}

