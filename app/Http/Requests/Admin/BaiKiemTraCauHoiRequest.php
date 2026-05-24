<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BaiKiemTraCauHoiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // hoặc check quyền admin/giáo viên
    }

    public function rules(): array
    {
        $rules = [
            // Trường cơ bản của bài kiểm tra (nếu cần validate chung)
            'bai_kiem_tra_id' => 'required|exists:bai_kiem_tra,id',

            // Trường chính: danh sách câu hỏi
            'cau_hoi_ids' => 'required|string', // chuỗi comma-separated từ frontend

            // Nếu frontend gửi mảng chi tiết (khuyến khích cho tương lai)
            'cau_hoi' => 'nullable|array',
            'cau_hoi.*.cau_hoi_id' => [
                'required',
                'integer',
                'exists:cau_hoi,id',
                // Không cho trùng lặp trong cùng bài kiểm tra
                Rule::unique('bai_kiem_tra_cau_hoi', 'cau_hoi_id')
                    ->where('bai_kiem_tra_id', $this->input('bai_kiem_tra_id', 0))
                    ->ignore($this->route('id')), // nếu update
            ],
            'cau_hoi.*.thu_tu' => 'nullable|integer|min:0',
            'cau_hoi.*.diem'   => 'nullable|numeric|min:0|max:10',
        ];

        // Nếu dùng chuỗi cau_hoi_ids đơn giản (như hiện tại của anh)
        $rules['cau_hoi_ids'] = 'required|string|regex:/^(\d+(,\d+)*)?$/'; // chỉ số và dấu phẩy

        return $rules;
    }

    public function messages(): array
    {
        return [
            'bai_kiem_tra_id.required' => 'Không tìm thấy bài kiểm tra.',
            'bai_kiem_tra_id.exists'   => 'Bài kiểm tra không tồn tại.',

            'cau_hoi_ids.required'     => 'Vui lòng chọn ít nhất một câu hỏi.',
            'cau_hoi_ids.regex'        => 'Danh sách câu hỏi không hợp lệ (chỉ chấp nhận số và dấu phẩy).',

            'cau_hoi.*.cau_hoi_id.required' => 'Mỗi câu hỏi phải có ID hợp lệ.',
            'cau_hoi.*.cau_hoi_id.exists'   => 'Có câu hỏi không tồn tại trong ngân hàng câu hỏi.',
            'cau_hoi.*.cau_hoi_id.unique'   => 'Câu hỏi này đã được thêm vào bài kiểm tra rồi.',
            'cau_hoi.*.diem.numeric'        => 'Điểm phải là số.',
            'cau_hoi.*.diem.min'            => 'Điểm không được âm.',
            'cau_hoi.*.diem.max'            => 'Điểm tối đa là 10.',
        ];
    }

    /**
     * Custom validation sau khi rules cơ bản
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Nếu dùng cau_hoi_ids dạng chuỗi
            $cauHoiIdsString = trim($this->input('cau_hoi_ids', ''));

            if (empty($cauHoiIdsString)) {
                $validator->errors()->add('cau_hoi_ids', 'Vui lòng chọn ít nhất một câu hỏi.');
                return;
            }

            $ids = array_filter(explode(',', $cauHoiIdsString), fn($id) => is_numeric(trim($id)));

            if (empty($ids)) {
                $validator->errors()->add('cau_hoi_ids', 'Danh sách câu hỏi không hợp lệ.');
                return;
            }

            // Kiểm tra tồn tại tất cả ID
            $existingCount = \App\Models\CauHoi::whereIn('id', $ids)
                ->where('trang_thai', true)
                ->count();

            if ($existingCount !== count($ids)) {
                $validator->errors()->add('cau_hoi_ids', 'Một số câu hỏi không tồn tại hoặc bị khóa.');
            }

            // Kiểm tra trùng lặp trong danh sách gửi lên (nếu frontend gửi trùng)
            if (count($ids) !== count(array_unique($ids))) {
                $validator->errors()->add('cau_hoi_ids', 'Có câu hỏi bị trùng lặp trong danh sách.');
            }
        });
    }

    /**
     * Helper: Lấy mảng ID sạch sẽ để dùng trong controller
     */
    public function getCauHoiIds(): array
    {
        $idsString = trim($this->input('cau_hoi_ids', ''));
        return array_filter(explode(',', $idsString), fn($id) => is_numeric(trim($id)));
    }
}