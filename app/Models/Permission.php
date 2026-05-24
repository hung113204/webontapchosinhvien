<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Quan trọng để dùng Soft Delete

class Permission extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'permissions';

    protected $fillable = ['name', 'slug', 'module', 'description', 'is_system', 'trang_thai'];

    // Ép kiểu dữ liệu để khi lấy ra Laravel tự hiểu là true/false
    protected $casts = [
        'is_system' => 'boolean',
        'trang_thai' => 'boolean',
    ];

    /**
     * Mối quan hệ với bảng RolePermission
     * Một quyền có thể xuất hiện trong nhiều nhóm quyền khác nhau
     */
    public function rolePermissions()
    {
        return $this->hasMany(RolePermission::class, 'permission_id');
    }
    // Trong file App\Models\Permission.php

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions', 'permission_id', 'role_id');
    }
}