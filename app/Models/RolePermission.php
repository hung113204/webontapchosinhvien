<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    use HasFactory;

    protected $table = 'role_permissions';

    protected $fillable = [
        'role_id',
        'permission_id',
        'can_view',
        'can_create',
        'can_update',
        'can_delete',
        'trang_thai',
    ];

    // Ép kiểu dữ liệu để sử dụng dưới dạng Boolean trong code
    protected $casts = [
        'can_view' => 'boolean',
        'can_create' => 'boolean',
        'can_update' => 'boolean',
        'can_delete' => 'boolean',
        'trang_thai' => 'boolean',
    ];

    /**
     * Liên kết ngược lại với bảng Roles
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Liên kết ngược lại với bảng Permissions
     */
    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }
}