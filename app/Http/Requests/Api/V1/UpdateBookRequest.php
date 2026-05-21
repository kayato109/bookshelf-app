<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $bookId = $this->route('book')->id;

        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'isbn' => ['required', 'string', 'size:13', "unique:books,isbn,{$bookId}"],
            'published_date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:2000'],
            'image_url' => ['nullable', 'url'],
            'genres' => ['required', 'array'],
            'genres.*' => ['integer', 'exists:genres,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id' => 'ユーザーID',
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
