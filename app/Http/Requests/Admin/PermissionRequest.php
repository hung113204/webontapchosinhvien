<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PermissionRequest extends FormRequest
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
        $id = $this->id;
        return [
            'name' => 'required|string|max:100',
            'slug' => 'required|string|max:100|unique:permissions,slug,' . $id,
            'module' => 'required|string', // Ví dụ: 'User', 'MonHoc'
            'description' => 'nullable|string|max:500',
            'is_system' => 'nullable|boolean',
        ];
    }
    protected function prepareForValidation()
    {
        // Chuyển giá trị checkbox về dạng boolean 0/1
        $this->merge([
            'is_system' => $this->has('is_system') ? 1 : 0,
        ]);
    }
}