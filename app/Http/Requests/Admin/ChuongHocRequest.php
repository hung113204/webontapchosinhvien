<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // Quan trọng: Import class này để dùng unique có điều kiện

class ChuongHocRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mon_hoc_id' => 'required|exists:mon_hoc,id',
            'ten_chuong' => [
                'required',
                'string',
                'max:255',
                // Kiểm tra trùng tên chương trong cùng 1 môn học
                Rule::unique('chuong_hoc', 'ten_chuong')
                    ->where(function ($query) {
                        return $query->where('mon_hoc_id', $this->mon_hoc_id);
                    })
                    ->ignore($this->id) // Bỏ qua chính nó khi cập nhật
            ],
            'thu_tu'     => 'nullable|integer|min:0',
            'trang_thai' => 'nullable', 
        ];
    }

    public function messages(): array
    {
        return [
            'mon_hoc_id.required' => 'Chương học phải thuộc về một môn học cụ thể.',
            'mon_hoc_id.exists'   => 'Môn học được chọn không tồn tại.',
            'ten_chuong.required' => 'Vui lòng nhập tên chương học.',
            'ten_chuong.unique'   => 'Tên chương này đã tồn tại trong môn học này.',
            'thu_tu.integer'      => 'Thứ tự hiển thị phải là một số nguyên.',
        ];
    }
}