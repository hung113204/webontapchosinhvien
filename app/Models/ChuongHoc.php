<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChuongHoc extends Model
{
    use HasFactory;

    // Khai báo bảng số ít để khớp với phong cách các bảng khác của bạn
    protected $table = 'chuong_hoc';

    protected $fillable = ['mon_hoc_id', 'ten_chuong', 'thu_tu', 'trang_thai'];

    /**
     * Quan hệ ngược lại: Một chương thuộc về một môn học
     */
    public function monHoc()
    {
        return $this->belongsTo(MonHoc::class, 'mon_hoc_id');
    }
    public function cauHois()
    {
        return $this->hasMany(CauHoi::class, 'chuong_hoc_id');
    }
    /**
     * Một chương có nhiều bài học
     */
    public function baiHocs()
    {
        // Nên đặt số nhiều
        return $this->hasMany(BaiHoc::class, 'chuong_hoc_id');
    }
}