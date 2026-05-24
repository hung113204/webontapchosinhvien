<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
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
            'ten_nhom_quyen' => 'required|string|max:50|unique:roles,ten_nhom_quyen,' . $id,
            'mo_ta' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'ten_nhom_quyen.required' => 'Tên nhóm quyền không được để trống.',
            'ten_nhom_quyen.unique' => 'Tên nhóm quyền này đã tồn tại.',
        ];
    }
}