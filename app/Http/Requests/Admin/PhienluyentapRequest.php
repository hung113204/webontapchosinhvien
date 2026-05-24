<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PhienluyentapRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->filled('id');

        return [
            'id' => 'nullable|integer',
            'user_id' => [$isUpdate ? 'sometimes' : 'required', 'exists:users,id'],
            'mon_hoc_id' => [$isUpdate ? 'sometimes' : 'required', 'exists:mon_hoc,id'],
            'chuong_hoc_id' => 'nullable|exists:chuong_hoc,id',
            'bai_kiem_tra_id' => 'nullable|exists:bai_kiem_tra,id', // Đã kiểm tra tồn tại
            'so_cau_hoi' => [$isUpdate ? 'sometimes' : 'required', 'integer', 'min:1'],
            'danh_sach_cau_hoi' => 'nullable|array', // Phải là định dạng mảng
            'ket_qua_chi_tiet' => 'nullable|array', // Phải là định dạng mảng
            'che_do' => [$isUpdate ? 'sometimes' : 'required', 'in:1,2,3'],
            'gioi_han_thoi_gian' => 'nullable|boolean',
            'thoi_gian_phut' => 'nullable|required_if:gioi_han_thoi_gian,1|integer|min:1',
            'thoi_gian_bat_dau' => [$isUpdate ? 'sometimes' : 'required', 'date'],
            'thoi_gian_ket_thuc' => 'nullable|date|after_or_equal:thoi_gian_bat_dau',
            'so_cau_dung' => 'nullable|integer|min:0|lte:so_cau_hoi', // Không được lớn hơn tổng số câu
            'diem_so' => 'nullable|numeric|min:0|max:10',
            'phan_tram_dung' => 'nullable|numeric|min:0|max:100',
            'trang_thai' => 'required|in:0,1,2',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Người dùng không được để trống.',
            'mon_hoc_id.required' => 'Môn học không được để trống.',
            'so_cau_hoi.required' => 'Số câu hỏi không được để trống.',
            'so_cau_dung.lte' => 'Số câu đúng không được vượt quá tổng số câu hỏi.', //
            'thoi_gian_ket_thuc.after_or_equal' => 'Thời gian kết thúc phải sau thời gian bắt đầu.', //
            'che_do.in' => 'Chế độ luyện tập không hợp lệ.',
            'thoi_gian_phut.required_if' => 'Vui lòng nhập số phút khi chọn giới hạn thời gian.', //
        ];
    }
    protected function prepareForValidation()
    {
        // Giải mã chuỗi JSON từ textarea thành mảng PHP trước khi Laravel kiểm tra Validation
        if (is_string($this->danh_sach_cau_hoi)) {
            $this->merge([
                'danh_sach_cau_hoi' => json_decode($this->danh_sach_cau_hoi, true),
            ]);
        }

        if (is_string($this->ket_qua_chi_tiet)) {
            $this->merge([
                'ket_qua_chi_tiet' => json_decode($this->ket_qua_chi_tiet, true),
            ]);
        }
    }
}