<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DapAnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cau_hoi_id'   => 'required|exists:cau_hoi,id',
            'noi_dung'     => 'required|string',
            'hinh_anh'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_dung'      => 'nullable|boolean',
            'thu_tu'       => 'nullable|integer|min:0',
            'ky_hieu'      => 'nullable|string|max:1', // A, B, C, D...
            'trang_thai'   => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'cau_hoi_id.required' => 'Đáp án phải thuộc về một câu hỏi.',
            'noi_dung.required'   => 'Nội dung đáp án không được để trống.',
            'ky_hieu.max'         => 'Ký hiệu chỉ gồm 1 ký tự (A, B, C...).',
        ];
    }
}