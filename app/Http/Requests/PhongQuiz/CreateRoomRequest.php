<?php

namespace App\Http\Requests\PhongQuiz;

use Illuminate\Foundation\Http\FormRequest;

class CreateRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Bật true để cho phép thực hiện request
    }

    public function rules(): array
    {
        return [
            'ten_phong' => 'nullable|string|max:255',
            'mon_hoc_id' => 'required|integer|exists:mon_hoc,id', // Bắt buộc và phải tồn tại trong bảng mon_hoc
            'muc_do_cau_hoi' => 'nullable|integer|in:1,2,3', // Chỉ chấp nhận 1 (Dễ), 2 (Trung bình), 3 (Khó) hoặc để trống (Hỗn hợp)
            'thoi_gian_tra_loi_cau_hoi' => 'nullable|integer|min:10|max:120', // Giới hạn từ 10s đến 120s
            'tong_so_cau' => 'nullable|integer|min:5|max:50', // Cho phép chơi từ 5 đến 50 câu
        ];
    }

    public function messages(): array
    {
        return [
            'mon_hoc_id.required' => 'Vui lòng chọn môn học để tạo phòng chơi.',
            'mon_hoc_id.exists' => 'Môn học được chọn không tồn tại trên hệ thống.',
            'muc_do_cau_hoi.in' => 'Mức độ câu hỏi không hợp lệ.',
            'thoi_gian_tra_loi_cau_hoi.min' => 'Thời gian trả lời tối thiểu là 10 giây.',
            'thoi_gian_tra_loi_cau_hoi.max' => 'Thời gian trả lời tối đa là 120 giây.',
            'tong_so_cau.min' => 'Số lượng câu hỏi tối thiểu của một phòng là 5 câu.',
        ];
    }
}