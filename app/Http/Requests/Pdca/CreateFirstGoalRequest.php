<?php

namespace App\Http\Requests\Pdca;

use Illuminate\Foundation\Http\FormRequest;

class CreateFirstGoalRequest extends FormRequest
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
        return [
            'weekly-goal' => 'required|string|max:250',
            'monthly-goal' => 'required|string|max:250',
        ];
    }
    public function messages(): array
    {
        return [
            'weekly-goal.required' => '週間目標は必須です',
            'weekly-goal.string' => '週間目標は文字列で入力してください',
            'weekly-goal.max' => '週間目標は250文字以内で入力してください',
            'monthly-goal.required' => '月間目標は必須です',
            'monthly-goal.string' => '月間目標は文字列で入力してください',
            'monthly-goal.max' => '月間目標は250文字以内で入力してください',
        ];
    }
}
