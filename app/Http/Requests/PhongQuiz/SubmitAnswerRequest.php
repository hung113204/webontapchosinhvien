<?php

namespace App\Http\Requests\PhongQuiz;

use Illuminate\Foundation\Http\FormRequest;

class SubmitAnswerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phong_quiz_id' => 'required|integer|exists:phong_quiz,id,trang_thai,2', // Chỉ cho nộp khi phòng đang thi đấu (trang_thai = 2)
            'cau_hoi_id' => 'required|integer|exists:cau_hoi,id',
            'dap_an_id' => [
                'nullable', // nullable đề phòng trường hợp hết giờ mà họ không bấm gì (hệ thống tự nộp trống)
                'integer',
                'exists:dap_an,id,cau_hoi_id,' . $this->cau_hoi_id // Đáp án này phải thuộc về câu hỏi đang trả lời
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'phong_quiz_id.exists' => 'Phòng chơi không hợp lệ hoặc trận đấu đã kết thúc.',
            'cau_hoi_id.exists' => 'Câu hỏi không tồn tại.',
            'dap_an_id.exists' => 'Đáp án bạn chọn không thuộc về câu hỏi hiện tại.',
        ];
    }
}