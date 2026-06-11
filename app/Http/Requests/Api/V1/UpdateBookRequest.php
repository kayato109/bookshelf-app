<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $bookId = $this->route('book')?->id;

        return [
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'isbn' => [
                'nullable',
                'string',
                'size:13',
                Rule::unique('books', 'isbn')->ignore($bookId),
            ],
            'published_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string', 'max:2000'],
            'image_url' => ['nullable', 'url'],
            'genres' => ['required', 'array'],
            'genres.*' => ['integer', 'exists:genres,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'タイトル',
            'author' => '著者名',
            'isbn' => 'ISBN',
            'published_date' => '出版日',
            'description' => '説明文',
            'image_url' => '画像URL',
            'genres' => 'ジャンル',
            'genres.*' => 'ジャンルID',
        ];
    }
}
