<?php

namespace App\Models;
use App\Http\Traits\PermissionTrait;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'users';

    protected $fillable = ['ma_sv', 'ho_ten', 'email', 'mat_khau', 'google_id', 'gioi_tinh', 'ngay_sinh', 'dia_chi', 'user_token', 'vai_tro_id', 'so_dien_thoai', 'avatar_url', 'trang_thai', 'is_first_login', 'last_login_at'];

    protected $authPasswordName = 'mat_khau';

    protected $hidden = ['mat_khau', 'remember_token', 'user_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'reset_password_expires_at' => 'datetime',
        'trang_thai' => 'boolean',
        'is_first_login' => 'boolean',
    ];

    public function getAuthPassword()
    {
        return $this->google_id ? '' : $this->mat_khau;
    }
    

    // --- RELATIONSHIPS ---

    public function role()
    {
        return $this->belongsTo(Role::class, 'vai_tro_id', 'id');
    }

    public function baiKiemTras()
    {
        return $this->hasMany(BaiKiemTra::class, 'nguoi_tao_id');
    }

    public function ketQuaThis()
    {
        return $this->hasMany(KetQuaThi::class, 'user_id');
    }

    public function tienDoBaiHocs()
    {
        return $this->hasMany(TienDoBaiHoc::class, 'user_id');
    }
    

    // --- HELPERS ---

    public function isAdmin(): bool
    {
        return in_array($this->vai_tro_id, [1, 2]);
    }

    public function isSuperAdmin(): bool
    {
        return $this->vai_tro_id === 1;
    }

    public function isTeacher(): bool
    {
        return $this->vai_tro_id === 2;
    }

    public function isStudent(): bool
    {
        return !$this->isAdmin();
    }

    public function isActive(): bool
    {
        return $this->trang_thai === true;
    }

    // --- ACCESSORS ---

    public function getNameAttribute()
    {
        return $this->ho_ten;
    }

    // --- PERMISSIONS ---

    public function hasPermission($module, $action = 'view'): bool
    {
        // ✅ FIX: Dùng load() để đảm bảo permissions + pivot luôn được load
        $role = $this->load('role.permissions')->role;

        if (!$role) {
            return false;
        }

        // Tìm permission theo slug (ví dụ: 'bai-kiem-tra')
        $permission = $role->permissions->where('slug', $module)->first();

        if (!$permission) {
            return false;
        }

        // Kiểm tra cột can_xxx trong bảng pivot (can_create, can_view, ...)
        $actionField = 'can_' . $action;

        return $permission->pivot ? (bool) $permission->pivot->$actionField : false;
    }
}