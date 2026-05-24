<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PhongQuiz extends Model
{
    use SoftDeletes;

    protected $table = 'phong_quiz';
    protected $fillable = [
        'ma_phong', 'ten_phong', 'mon_hoc_id', 'muc_do_cau_hoi', 
        'chu_phong_id', 'trang_thai', 'cau_hoi_hien_tai_id', 
        'thoi_gian_bat_dau_cau_hoi', 'thoi_gian_tra_loi_cau_hoi', 'tong_so_cau',
        'danh_sach_cau_hoi'
    ];

    protected $casts = [
        'muc_do_cau_hoi' => 'integer',
        'trang_thai' => 'integer',
        'cau_hoi_hien_tai_id' => 'integer',
        'thoi_gian_bat_dau_cau_hoi' => 'datetime',
        'thoi_gian_tra_loi_cau_hoi' => 'integer',
        'tong_so_cau' => 'integer',
    ];

    // Lấy thông tin môn học của phòng này
    public function monHoc(): BelongsTo
    {
        return $this->belongsTo(MonHoc::class, 'mon_hoc_id');
    }

    // Lấy thông tin chủ phòng
    public function chuPhong(): BelongsTo
    {
        return $this->belongsTo(User::class, 'chu_phong_id');
    }

    // Lấy danh sách thành viên đang ở trong phòng này
    public function thanhVien(): HasMany
    {
        return $this->hasMany(ThanhVienPhong::class, 'phong_quiz_id');
    }
}
