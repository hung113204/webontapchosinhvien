<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // ✅ FIX QUAN TRỌNG: Dùng filled() thay vì $this->id
        // Bug cũ: $id = $this->id → khi thêm mới, $this->id = "" (rỗng)
        // → unique rule bị thành: unique:users,email, (thiếu ID) → đúng tình cờ
        // → nhưng mat_khau rule: $id ? ... : 'required' → "" truthy → không bắt required
        // → người dùng thêm mới không cần nhập mật khẩu vẫn pass validate!
        $id = $this->filled('id') ? $this->input('id') : null;

        return [
            'ho_ten'      => 'required|string|max:255',

            // ✅ FIX: Khi $id = null thì ignore clause bị bỏ qua đúng cách
            'email'       => 'required|email|unique:users,email,' . ($id ?? 'NULL'),
            'ma_sv'       => 'nullable|string|unique:users,ma_sv,' . ($id ?? 'NULL'),

            'so_dien_thoai' => 'nullable|string|max:20',
            'vai_tro_id'    => 'required|exists:roles,id',
            'gioi_tinh'     => 'nullable|in:MALE,FEMALE',
            'ngay_sinh'     => 'nullable|date',
            'dia_chi'       => 'nullable|string',

            // ✅ FIX: Khi thêm mới ($id = null) → required
            //         Khi cập nhật ($id có giá trị) → nullable (để trống = không đổi mật khẩu)
            'mat_khau'              => $id ? 'nullable|min:6|confirmed' : 'required|min:6|confirmed',
            'mat_khau_confirmation' => $id ? 'nullable|min:6'           : 'required|min:6',

            'avatar_url'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'trang_thai'  => 'nullable|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'ho_ten.required'       => 'Họ tên là bắt buộc.',
            'email.required'        => 'Email là bắt buộc.',
            'email.unique'          => 'Email này đã được sử dụng.',
            'ma_sv.unique'          => 'Mã sinh viên này đã tồn tại.',
            'vai_tro_id.required'   => 'Vui lòng chọn vai trò.',
            'vai_tro_id.exists'     => 'Vai trò không hợp lệ.',
            'mat_khau.required'     => 'Mật khẩu là bắt buộc khi tạo mới.',
            'mat_khau.min'          => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'mat_khau.confirmed'    => 'Xác nhận mật khẩu không khớp.',
            'avatar_url.image'      => 'File tải lên phải là ảnh.',
            'avatar_url.mimes'      => 'Ảnh phải có định dạng: jpeg, png, jpg, gif.',
            'avatar_url.max'        => 'Ảnh không được vượt quá 2MB.',
        ];
    }
}