<?php

namespace App\Http\Requests\Pdca;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class CheckActionRequest extends FormRequest
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

        if ($this->has('weeklyGoal_id')) {
            $rules['weekly_check_rating'] = 'required|integer';
            $rules['weekly_check_description'] = 'required|string|max:500';
            $rules['weekly_action_description'] = 'required|string|max:500';
        }
        if ($this->has('monthlyGoal_id')) {
            $rules['monthly_check_rating'] = 'required|integer';
            $rules['monthly_check_description'] = 'required|string|max:500';
            $rules['monthly_action_description'] = 'required|string|max:500';
        }
        return $rules;
    }
    public function messages(): array
    {
        return [
            'weekly_check_rating.required' =>'星評価は必須です',
            'weekly_check_description.required' =>'週間目標の振り返りは必須です',
            'weekly_check_description.string' => '週間目標の振り返りは文字で入力してください',
            'weekly_check_description.max' => '週間目標の振り返りは500文字以内で入力してください',
            'weekly_action_description.required' =>'週間目標の改善点は必須です',
            'weekly_action_description.string' => '週間目標の改善点は文字で入力してください',
            'weekly_action_description.max' => '週間目標の改善点は500文字以内で入力してください',
            'monthly_check_rating.required' =>'星評価は必須です',
            'monthly_check_description.required' =>'月間目標の振り返りは必須です',
            'monthly_check_description.string' => '月間目標の振り返りは文字で入力してください',
            'monthly_check_description.max' => '月間目標の振り返りは500文字以内で入力してください',
            'monthly_action_description.required' =>'月間目標の改善点は必須です',
            'monthly_action_description.string' => '月間目標の改善点は文字で入力してください',
            'monthly_action_description.max' => '月間目標の改善点は500文字以内で入力してください',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $activeTab = $this->has('weeklyGoal_id') ? 'weekly' : 'monthly';
        // セッションにアクティブタブ情報を保存
        session()->flash('activeTab', $activeTab);
        throw new ValidationException($validator);
    }
}
