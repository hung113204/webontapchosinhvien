<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    // Khai báo tên bảng (mặc định Laravel sẽ hiểu là số nhiều của Role, nhưng khai báo cho chắc chắn)
    protected $table = 'roles';

    // Các trường cho phép lưu dữ liệu hàng loạt (Mass Assignment)
    protected $fillable = ['ten_nhom_quyen', 'mo_ta'];

    /**
     * Mối quan hệ: Một nhóm quyền có nhiều người dùng
     * (Ví dụ: Nhóm 'Sinh viên' có hàng nghìn User)
     */
    public function users()
    {
        return $this->hasMany(User::class, 'vai_tro_id');
    }

    /**
     * Mối quan hệ: Một nhóm quyền có nhiều quyền chi tiết (qua bảng trung gian role_permissions)
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions', 'role_id', 'permission_id')->withPivot('can_view', 'can_create', 'can_update', 'can_delete')->withTimestamps();
    }
}