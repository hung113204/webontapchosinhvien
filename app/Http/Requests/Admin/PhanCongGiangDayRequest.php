<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PhanCongGiangDayRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id',
            'mon_hoc_id' => 'required|exists:mon_hoc,id',
            'lop_hoc_id' => 'required|exists:lop_hoc,id',
            'nam_hoc_id' => 'required|exists:nam_hoc,id',
        ];
    }
    public function messages(): array
    {
        return [
            'user_id.required' => 'Vui lòng chọn giáo viên.',
            'user_id.exists' => 'Giáo viên không tồn tại.',
            'mon_hoc_id.required' => 'Vui lòng chọn môn học.',
            'mon_hoc_id.exists' => 'Môn học không tồn tại.',
            'lop_hoc_id.required' => 'Vui lòng chọn lớp học.',
            'lop_hoc_id.exists' => 'Lớp học không tồn tại.',
            'nam_hoc_id.required' => 'Vui lòng chọn năm học.',
            'nam_hoc_id.exists' => 'Năm học không tồn tại.',
        ];
    }
}