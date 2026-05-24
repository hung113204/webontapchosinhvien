<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CauHoiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mon_hoc_id' => 'nullable', // Chỉ dùng cho lọc, không lưu DB
            'chuong_hoc_id' => 'required|exists:chuong_hoc,id',
            'bai_hoc_id'    => 'nullable|exists:bai_hoc,id',
            'noi_dung' => 'required|string',
            'hinh_anh' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'goi_y' => 'nullable|string',
            'giai_thich' => 'nullable|string',
            'muc_do' => 'required|integer|in:1,2,3', // 1: Dễ, 2: TB, 3: Khó
            'loai_cau_hoi' => 'required|integer|in:1,2,3,4', // 1: Một đáp án, 2: Nhiều đáp án
            'temp_file' => 'sometimes|required|string',
            'selected_questions' => 'nullable|array',
            'trang_thai' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'mon_hoc_id.required' => 'Vui lòng chọn môn học.',
            'mon_hoc_id.exists' => 'Môn học không tồn tại.',
            'chuong_hoc_id.required' => 'Vui lòng chọn chương học.',
            'chuong_hoc_id.exists' => 'Chương học không tồn tại.',
            'noi_dung.required' => 'Nội dung câu hỏi không được để trống.',
            'muc_do.in' => 'Mức độ chọn không hợp lệ.',
            'hinh_anh.image' => 'File tải lên phải là hình ảnh.',
        ];
    }
}