<?php

namespace App\Http\Requests\PhongQuiz;

use Illuminate\Foundation\Http\FormRequest;

class JoinRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if ($this->has('ma_phong')) {
            $this->merge([
                'ma_phong' => trim($this->ma_phong)
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'ma_phong' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $exists = \App\Models\PhongQuiz::where('ma_phong', $value)
                        ->whereIn('trang_thai', [1, 2])
                        ->exists();
                    if (!$exists) {
                        $fail('Mã phòng không tồn tại hoặc phòng chơi này đã kết thúc.');
                    }
                }
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'ma_phong.required' => 'Vui lòng nhập mã phòng.',
            'ma_phong.exists' => 'Mã phòng không tồn tại hoặc phòng chơi này đã bắt đầu/kết thúc.',
        ];
    }
}