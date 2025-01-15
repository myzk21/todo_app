<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TodoUpdateRequest extends FormRequest
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
            'updateTitle' => 'required|string|max:50',
            'updateDescription' => 'nullable|string|max:500',
            'updateDue' => 'nullable|required_if:updateToCalendar,true|date',
            'when_completed' => 'nullable|date',
            'updateProgress_rate' => 'nullable|integer',
            'updatePriority' => 'nullable|string',
            'updateToCalendar' => 'nullable|boolean',
            // 'label' => 'nullable|string|max:25'
        ];
    }
    public function messages(): array
    {
        return [
            'updateTitle.required' => 'タイトルは必須です',
            'updateTitle.string' => 'タイトルは文字列で入力してください',
            'updateTitle.max' => 'タイトルは50文字以内で入力してください',
            'updateDescription.string' => '説明は文字で入力してください',
            'updateDescription.max' => '説明は500文字以内で入力してください',
            'updateDue.required_if'=>'カレンダーに追加する場合、期日は必須です',
            'updateDue.date' => '期日は有効な日付で入力してください',
            'updateProgress_rate.integer' => '進捗率は整数で入力してください',
        ];
    }
}
