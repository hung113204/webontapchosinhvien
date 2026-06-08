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
                function ($attribute, $value, $fail) {
                    $normalizedValue = \Illuminate\Support\Str::slug($value);
                    $existingChapters = \App\Models\ChuongHoc::where('mon_hoc_id', $this->mon_hoc_id)
                        ->when($this->id, function ($query) {
                            $query->where('id', '!=', $this->id);
                        })
                        ->get(['id', 'ten_chuong']);
                    
                    foreach ($existingChapters as $chapter) {
                        if (\Illuminate\Support\Str::slug($chapter->ten_chuong) === $normalizedValue) {
                            $fail('Tên chương này (hoặc tương tự) đã tồn tại trong môn học (bỏ qua dấu câu/viết hoa).');
                            return;
                        }
                    }
                }
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