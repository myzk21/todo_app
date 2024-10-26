<?php

namespace App\Http\Requests\Pdca;

use Illuminate\Foundation\Http\FormRequest;

class GoalRequest extends FormRequest
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
        $rules = [];

        if ($this->has('weekly_goal')) {
            $rules['weekly_goal'] = 'required|string|max:250';
        }

        if ($this->has('monthly_goal')) {
            $rules['monthly_goal'] = 'required|string|max:250';
        }
        return $rules;
    }
    public function messages(): array
    {
        return [
            'weekly_goal.required' => '週間目標は必須です',
            'weekly_goal.string' => '週間目標は文字列で入力してください',
            'weekly_goal.max' => '週間目標は250文字以内で入力してください',
            'monthly_goal.required' => '月間目標は必須です',
            'monthly_goal.string' => '月間目標は文字列で入力してください',
            'monthly_goal.max' => '月間目標は250文字以内で入力してください',
        ];
    }
}
