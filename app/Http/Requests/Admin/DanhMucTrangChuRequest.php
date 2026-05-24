<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class DanhMucTrangChuRequest extends FormRequest
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
        // Lấy ID của bản ghi đang được update (nếu có) trên route
        // Giả sử route của bạn là: route('danh-muc.update', $id)
        $danhMucId = $this->input('id');

        return [
            'tieu_de' => ['required', 'string', 'max:255'],

            'slug' => [
                'required',
                'string',
                'max:255',
                // Check unique trong bảng danh_muc_trang_chu, bỏ qua bản ghi hiện tại và bỏ qua các bản ghi đã soft delete
                Rule::unique('danh_muc_trang_chu', 'slug')->ignore($danhMucId)->whereNull('deleted_at'),
            ],

            'mo_ta' => ['nullable', 'string', 'max:500'],

            'loai_danh_muc' => [
                'required',
                'string',
                // Chỉ cho phép các giá trị này theo như comment trong migration của bạn
                Rule::in(['banner', 'course_list', 'exam_list', 'feature_list', 'stats', 'testimonial']),
            ],

            'icon_class' => ['nullable', 'string', 'max:255'],

            // Nếu bạn upload file ảnh, dùng rule 'image'. Nếu chỉ lưu đường dẫn text, đổi thành 'string'
            'hinh_anh' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],

            'thu_tu' => ['nullable', 'integer', 'min:0'],

            'so_luong_hien_thi' => ['nullable', 'integer', 'min:1'],

            'trang_thai' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Tùy chỉnh thông báo lỗi (Tiếng Việt).
     */
    public function messages(): array
    {
        return [
            'tieu_de.required' => 'Vui lòng nhập tiêu đề.',
            'tieu_de.max' => 'Tiêu đề không được vượt quá 255 ký tự.',

            'slug.required' => 'Vui lòng nhập đường dẫn (slug).',
            'slug.unique' => 'Đường dẫn (slug) này đã tồn tại.',

            'loai_danh_muc.required' => 'Vui lòng chọn loại danh mục.',
            'loai_danh_muc.in' => 'Loại danh mục không hợp lệ.',

            'hinh_anh.image' => 'Tệp tải lên phải là định dạng hình ảnh.',
            'hinh_anh.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif, webp.',
            'hinh_anh.max' => 'Dung lượng hình ảnh không được vượt quá 2MB.',

            'thu_tu.integer' => 'Thứ tự phải là một số nguyên.',
            'so_luong_hien_thi.integer' => 'Số lượng hiển thị phải là một số nguyên.',
        ];
    }
}