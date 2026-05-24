<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ImportWordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // hoặc check quyền admin/teacher
    }

    public function rules(): array
    {
        return [
            'chuong_hoc_id'       => 'required|exists:chuong_hoc,id',
            'loai_cau_hoi'        => 'required|integer|in:1,2,3,4',
            'muc_do'              => 'required|integer|in:1,2,3',
            'temp_file'           => 'required|string',           // đường dẫn file tạm
            'selected_questions'  => 'nullable|array',
            'selected_questions.*'=> 'integer',                   // mỗi phần tử là index
            'file'                => 'sometimes|required_if:temp_file,null|file|mimes:doc,docx|max:10240', // fallback nếu cần
        ];
    }

    public function messages(): array
    {
        return [
            'chuong_hoc_id.required' => 'Vui lòng chọn chương học.',
            'loai_cau_hoi.in'        => 'Loại câu hỏi không hợp lệ (chỉ hỗ trợ 1-4).',
            'temp_file.required'     => 'File tạm không tồn tại hoặc đã hết hạn.',
        ];
    }
}