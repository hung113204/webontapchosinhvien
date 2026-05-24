<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class KetQuaThiRequest extends FormRequest
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
            'diem' => 'required|numeric|min:0|max:10',
            'trang_thai' => 'required|in:1,2,3',
            'ai_feedback' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'diem.max' => 'Điểm số không được vượt quá 10.',
            'trang_thai.in' => 'Trạng thái bài thi không hợp lệ.',
        ];
    }
}