<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BaiHocRequest extends FormRequest
{
    /**
     * Xác định xem người dùng có quyền thực hiện request này không.
     */
    public function authorize(): bool
    {
        // Trả về true vì chúng ta sẽ xử lý phân quyền ở Middleware hoặc Controller
        return true; 
    }

    /**
     * Các quy tắc kiểm tra dữ liệu (Validation Rules)
     */
    public function rules(): array
    {
        return [
            'chuong_hoc_id'      => 'required|exists:chuong_hoc,id',
            'ten_bai_hoc'        => 'required|string|max:255',
            'noi_dung_ly_thuyet' => 'nullable|string',
            
            // Nhóm lưu trữ code
            'ten_file_code'      => 'nullable|string|max:100',
            'ma_nguon_mau'       => 'nullable|string',
            'ngon_ngu_code'      => 'nullable|string|max:50',
            
            // URL & File
            'video_source'       => 'nullable|string|in:url,file',
            'video_url'          => 'nullable|string|max:255',
            'video_file'         => 'nullable|file|mimes:mp4,webm,ogg,avi,mov|max:102400',
            'tai_lieu_dinh_kem'  => 'nullable|array',
            'tai_lieu_dinh_kem.*'=> 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,rar|max:10240', 
            
            // Cấu hình
            'thu_tu'             => 'nullable|integer|min:0',
            'trang_thai'         => 'required|in:0,1',
        ];
    }

    /**
     * Tùy chỉnh thông báo lỗi sang tiếng Việt
     */
    public function messages(): array
    {
        return [
            'chuong_hoc_id.required'    => 'Vui lòng chọn chương học cho bài này.',
            'chuong_hoc_id.exists'      => 'Chương học không tồn tại trong hệ thống.',
            
            'ten_bai_hoc.required'      => 'Vui lòng nhập tên bài học.',
            'ten_bai_hoc.max'           => 'Tên bài học không được vượt quá 255 ký tự.',
            
            'ten_file_code.max'         => 'Tên file code không được vượt quá 100 ký tự.',
            'ngon_ngu_code.max'         => 'Ngôn ngữ code không được vượt quá 50 ký tự.',
            
            'video_url.max'             => 'Link video quá dài.',
            'video_file.file'           => 'Video tải lên phải là một tệp tin hợp lệ.',
            'video_file.mimes'          => 'Định dạng video không hỗ trợ (chỉ nhận mp4, webm, ogg, avi, mov).',
            'video_file.max'            => 'Dung lượng video không được vượt quá 100MB.',
            
            'tai_lieu_dinh_kem'         => 'nullable|array',
            'tai_lieu_dinh_kem.*'       => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,rar|max:10240',
            
            'thu_tu.integer'            => 'Thứ tự phải là một số nguyên.',
            'thu_tu.min'                => 'Thứ tự không được nhỏ hơn 0.',
            
            'trang_thai.required'       => 'Vui lòng chọn trạng thái hiển thị.',
            'trang_thai.in'             => 'Trạng thái không hợp lệ.',
        ];
    }
}