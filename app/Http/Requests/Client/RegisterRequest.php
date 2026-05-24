<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // QUAN TRỌNG: Sửa false thành true để cho phép mọi người đều được gửi form này
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'fullname' => 'required|string|max:255',
            // Validate vào cột ma_sv thay vì student_id
         //   'student_id' => 'nullable|string|max:20|unique:users,ma_sv',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'fullname.required' => 'Vui lòng nhập Họ và tên.',
            'email.required' => 'Vui lòng nhập Email.',
            'email.email' => 'Định dạng Email không hợp lệ.',
            'email.unique' => 'Email này đã được sử dụng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
        ];
    }
}