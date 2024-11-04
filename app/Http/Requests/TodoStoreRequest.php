<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TodoStoreRequest extends FormRequest
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
            'title' => 'required|string|max:50',
            'description' => 'nullable|string|max:500',
            'due' => 'nullable|required_if:addToCalendar,true|date|after_or_equal:today',
            'when_completed' => 'nullable|date',
            'progress_rate' => 'nullable|integer',
            'priority' => 'nullable|string',
            'addToCalendar' => 'nullable|boolean',
            // 'label' => 'nullable|string|max:25'
        ];

    }
    public function messages(): array
    {
        return [
            'title.required' => 'タイトルは必須です',
            'title.string' => 'タイトルは文字列で入力してください',
            'title.max' => 'タイトルは50文字以内で入力してください',
            'description.string' => '説明は文字で入力してください',
            'description.max' => '説明は500文字以内で入力してください',
            'due.date' => '期日は有効な日付で入力してください',
            'due.required_if'=>'カレンダーに追加する場合、期日は必須です',
            'due.after_or_equal' => '期日は今日以降の日付で入力してください',
            'progress_rate.integer' => '進捗率は整数で入力してください',
        ];
    }
}
