<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BaiKiemTraRequest extends FormRequest
{
    private function normalizeBooleanValue($value): int
    {
        if (is_bool($value)) {
            return $value ? 1 : 0;
        }

        if (is_numeric($value)) {
            return (int) $value === 1 ? 1 : 0;
        }

        $normalized = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        return $normalized ? 1 : 0;
    }

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'mon_hoc_id' => 'required|integer|exists:mon_hoc,id',
            'ten_bai' => 'required|string|max:255',
            'thoi_gian_phut' => 'required|integer|min:1',
            'so_lan_lam_bai' => 'required|integer|min:1',
            'cach_tinh_diem' => 'required|integer|in:0,1,2',
            'thoi_gian_bat_dau' => 'required|date',
            'thoi_gian_ket_thuc' => 'required|date|after:thoi_gian_bat_dau',
        ];

        // ✅ Kiểm tra xem có đang edit hay không
        $isEdit = $this->input('id') !== null;

        // ✅ Xử lý validation theo chế độ
        if ($this->input('tu_dong_lay_de') == 1) {
            // Chế độ tự động: Bắt buộc có chương học
            $rules['chuong_hoc_ids'] = 'required|string';
            
            // Không bắt buộc phải có câu hỏi ngay, vì sẽ random sau
            $rules['so_cau_de'] = 'nullable|integer|min:0';
            $rules['so_cau_tb'] = 'nullable|integer|min:0';
            $rules['so_cau_kho'] = 'nullable|integer|min:0';
        } else {
            // Chế độ thủ công
            if ($this->input('action') === 'draft' || $isEdit) {
                // ✅ Lưu nháp HOẶC đang edit: Không bắt buộc có câu hỏi
                $rules['cau_hoi_ids'] = 'nullable|string';
                $rules['chuong_hoc_ids'] = 'nullable|string';
            } else {
                // Lưu chính thức (tạo mới): Bắt buộc có câu hỏi
                $rules['cau_hoi_ids'] = 'required|string';
                $rules['chuong_hoc_ids'] = 'required|string';
            }
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'mon_hoc_id.required' => 'Chưa chọn môn học.',
            'mon_hoc_id.exists' => 'Môn học không tồn tại.',
            'ten_bai.required' => 'Chưa nhập tên bài kiểm tra.',
            'ten_bai.max' => 'Tên bài kiểm tra không được quá 255 ký tự.',
            'thoi_gian_phut.required' => 'Chưa nhập thời gian làm bài.',
            'thoi_gian_phut.min' => 'Thời gian tối thiểu 1 phút.',
            'so_lan_lam_bai.required' => 'Chưa nhập số lần thi.',
            'so_lan_lam_bai.min' => 'Số lần thi tối thiểu là 1.',
            'cach_tinh_diem.required' => 'Chưa nhập cách tính điểm.',
            'cach_tinh_diem.in' => 'Cách tính điểm không hợp lệ.',
            'thoi_gian_bat_dau.required' => 'Chưa chọn thời gian bắt đầu.',
            'thoi_gian_ket_thuc.required' => 'Chưa chọn thời gian kết thúc.',
            'thoi_gian_ket_thuc.after' => 'Thời gian kết thúc phải sau thời gian bắt đầu.',
            
            'chuong_hoc_ids.required' => 'Chưa chọn chương học.',
            'cau_hoi_ids.required' => 'Chưa chọn câu hỏi nào.',
            
            'so_cau_de.min' => 'Số câu dễ phải lớn hơn hoặc bằng 0.',
            'so_cau_tb.min' => 'Số câu trung bình phải lớn hơn hoặc bằng 0.',
            'so_cau_kho.min' => 'Số câu khó phải lớn hơn hoặc bằng 0.',
        ];
    }

    protected function prepareForValidation()
    {
        // Xác định chế độ tự động hay thủ công
        $tuDong = $this->input('question-mode') === 'auto' || $this->input('tu_dong_lay_de') == 1 ? 1 : 0;

        // Chuẩn hóa các trường boolean
        $this->merge([
            'tu_dong_lay_de' => $tuDong,
            'xem_diem' => $this->normalizeBooleanValue($this->input('xem_diem', 0)),
            'xem_bai_lam' => $this->normalizeBooleanValue($this->input('xem_bai_lam', 0)),
            'dao_cau_hoi' => $this->normalizeBooleanValue($this->input('dao_cau_hoi', 0)),
            'dao_dap_an' => $this->normalizeBooleanValue($this->input('dao_dap_an', 0)),
            'nop_khi_chuyen_tab' => $this->normalizeBooleanValue($this->input('nop_khi_chuyen_tab', 0)),
        ]);

        // Chuẩn hóa số câu hỏi (để tránh lỗi khi gửi chuỗi rỗng)
        $this->merge([
            'so_cau_de' => (int) $this->input('so_cau_de', 0),
            'so_cau_tb' => (int) $this->input('so_cau_tb', 0),
            'so_cau_kho' => (int) $this->input('so_cau_kho', 0),
        ]);
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $isEdit = $this->input('id') !== null;

            // Kiểm tra thời gian bắt đầu không được ở quá khứ (chỉ áp dụng khi tạo mới)
            // Cho phép độ trễ 5 phút để tránh lỗi khi người dùng điền form quá lâu
            if (!$isEdit && $this->filled('thoi_gian_bat_dau')) {
                if (\Carbon\Carbon::parse($this->thoi_gian_bat_dau)->addMinutes(5)->isPast()) {
                    $validator->errors()->add('thoi_gian_bat_dau', 'Thời gian bắt đầu không được nằm trong quá khứ.');
                }
            }

            // Nếu chế độ tự động, kiểm tra tổng số câu
            if ($this->tu_dong_lay_de == 1) {
                $tong = $this->so_cau_de + $this->so_cau_tb + $this->so_cau_kho;

                if ($tong <= 0) {
                    $validator->errors()->add('so_cau_de', 'Tổng số câu hỏi phải lớn hơn 0.');
                }
            }
            if ($this->tu_dong_lay_de == 0 && !$isEdit && $this->input('action') !== 'draft') {
                if (empty($this->input('cau_hoi_ids'))) {
                    $validator->errors()->add('cau_hoi_ids', 'Vui lòng chọn ít nhất một câu hỏi.');
                }
            }
        });
    }
}
