<?php

namespace App\Http\Requests\Pdca;

use Illuminate\Foundation\Http\FormRequest;

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
        return [
            'check-rating' => 'required|integer',
            'check-description' => 'required|string|max:500',
            'action-description' => 'required|string|max:500',
        ];
    }
    public function messages(): array
    {
        return [
            'check-rating.required' =>'星評価は必須です',
            'check-description.required' =>'振り返りは必須です',
            'check-description.string' => '振り返りは文字で入力してください',
            'check-description.max' => '振り返りは500文字以内で入力してください',
            'action-description.required' =>'改善点は必須です',
            'action-description.string' => '改善点は文字で入力してください',
            'action-description.max' => '改善点は500文字以内で入力してください',
        ];
    }
}
