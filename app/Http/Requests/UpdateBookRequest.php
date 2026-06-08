<?php

namespace App\Http\Requests;

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
        return [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',

            'isbn' => [
                'nullable',
                'string',
                'size:13',
                Rule::unique('books', 'isbn')->ignore($this->route('book')->id),
            ],

            'published_date' => 'nullable|date',
            'description' => 'nullable|string|max:2000',
            'image_url' => 'nullable|url',

            'genres' => 'required|array|min:1',
            'genres.*' => 'exists:genres,id',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'タイトルを入力してください',
            'author.required' => '著者名を入力してください',

            'isbn.size' => 'ISBN は 13 桁で入力してください',
            'isbn.unique' => '入力された ISBN は既に登録されています',

            'published_date.date' => '出版日は有効な日付形式で入力してください',

            'description.max' => '説明は 2000 文字以内で入力してください',

            'image_url.url' => '画像 URL は正しい形式で入力してください',

            'genres.required' => 'ジャンルは 1 つ以上選択してください',
        ];
    }
}
