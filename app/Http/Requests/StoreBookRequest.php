<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
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
    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'required|string|size:13|unique:books,isbn',
            'published_date' => 'required|date',
            'description' => 'nullable|string|max:2000',
            'image_url' => 'nullable|url',
            'genres' => 'required|array|min:1',
            'genres.*' => 'exists:genres,id',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'タイトルを入力してください',
            'author.required' => '著者名を入力してください',
            'isbn.required' => 'ISBN を入力してください',
            'isbn.size' => 'ISBN は13桁で入力してください',
            'isbn.unique' => '入力されたISBN が既に登録されています',
            'published_date.required' => '出版日を入力してください',
            'published_date.date' => '出版日は有効な日付形式で入力してください',
            'description.max' => '説明は2000文字以内で入力してください',
            'image_url.url' => '画像URLは正しい形式で入力してください',
            'genres.required' => 'ジャンルは1つ以上選択してください',
        ];
    }
}
