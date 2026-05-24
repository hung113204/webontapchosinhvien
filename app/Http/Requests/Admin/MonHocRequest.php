<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MonHocRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Lấy ID từ input hidden (null nếu là thêm mới)
        $id = $this->input('id') ?: null;

        return [
            'ten_mon_hoc'        => [
                'required', 'string', 'max:255',
                // Dùng Rule::unique để tránh lỗi dấu phẩy thừa khi $id = null
                Rule::unique('mon_hoc', 'ten_mon_hoc')->ignore($id),
            ],
            'ma_mon_hoc'         => [
                'nullable', 'string', 'max:50',
                Rule::unique('mon_hoc', 'ma_mon_hoc')->ignore($id),
            ],
            'mo_ta_ngan'         => 'nullable|string|max:500',
            'mo_ta_chi_tiet'     => 'nullable|string',
            'muc_do_mon_hoc'     => 'required|in:1,2,3,4',
            'icon_class'         => 'nullable|string|max:100',
            'hinh_anh'           => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'mau_sac'            => 'nullable|string|max:7',
            'so_tin_chi'         => 'required|integer|min:1',
            'thu_tu'             => 'nullable|integer',
            'is_popular'         => 'nullable|boolean',
            'is_featured'        => 'nullable|boolean',
            'trang_thai'         => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'ten_mon_hoc.required' => 'Vui lòng nhập tên môn học',
            'ten_mon_hoc.max'      => 'Tên môn học không được vượt quá 255 ký tự',
            'ten_mon_hoc.unique'   => 'Tên môn học này đã tồn tại trong hệ thống',

            'ma_mon_hoc.unique' => 'Mã môn học này đã tồn tại, vui lòng chọn mã khác hoặc để trống để tự tạo',
            'ma_mon_hoc.max'    => 'Mã môn học không được vượt quá 50 ký tự',

            'so_tin_chi.required' => 'Vui lòng nhập số tín chỉ',
            'so_tin_chi.integer'  => 'Số tín chỉ phải là số nguyên',
            'so_tin_chi.min'      => 'Số tín chỉ phải lớn hơn 0',

            'muc_do_mon_hoc.required' => 'Vui lòng chọn mức độ môn học',
            'muc_do_mon_hoc.in'       => 'Mức độ môn học không hợp lệ',

            'hinh_anh.image' => 'File phải là hình ảnh',
            'hinh_anh.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif',
            'hinh_anh.max'   => 'Kích thước hình ảnh không được vượt quá 2MB',

            'mo_ta_ngan.max' => 'Mô tả ngắn không được vượt quá 500 ký tự',
        ];
    }
}
