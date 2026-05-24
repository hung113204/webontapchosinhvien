<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhanCongGiangDay extends Model
{
    use HasFactory;

    // Tên bảng (nếu bạn đặt tên migration là create_phan_cong_giang_day_table)
    protected $table = 'phan_cong_giang_day';

    // Các trường có thể gán dữ liệu hàng loạt (Mass Assignment)
    protected $fillable = ['user_id', 'mon_hoc_id', 'lop_hoc_id', 'nam_hoc_id'];

    /**
     * Lấy thông tin Giáo viên (User) được phân công.
     */
    public function giaoVien(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Lấy thông tin Môn học được phân công.
     */
    public function monHoc(): BelongsTo
    {
        return $this->belongsTo(MonHoc::class, 'mon_hoc_id');
    }

    /**
     * Lấy thông tin Lớp học được phân công.
     */
    public function lopHoc(): BelongsTo
    {
        return $this->belongsTo(LopHoc::class, 'lop_hoc_id');
    }

    /**
     * Lấy thông tin Năm học được phân công.
     */
    public function namHoc(): BelongsTo
    {
        return $this->belongsTo(NamHoc::class, 'nam_hoc_id');
    }
}