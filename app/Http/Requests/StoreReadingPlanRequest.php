<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReadingPlanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'book_id' => [
                'required',
                'integer',
                'exists:books,id',
                // 同一ユーザーは同じ書籍の計画を1つだけ
                Rule::unique('reading_plans', 'book_id')
                    ->where('user_id', auth()->id()),
            ],
            'target_date' => [
                'required',
                'date',
                'after_or_equal:today',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'book_id.required' => '書籍を選択してください',
            'book_id.exists' => '選択した書籍が存在しません',
            'book_id.unique' => '同じ書籍の読書計画は1つしか作成できません',
            'target_date.required' => '期日を入力してください',
            'target_date.after_or_equal' => '期日は今日以降の日付を入力してください',
        ];
    }
}
