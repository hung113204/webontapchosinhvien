<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaiHoc extends Model
{
    use HasFactory, SoftDeletes;

    
    protected $table = 'bai_hoc';
    protected $fillable = [
        'chuong_hoc_id',
        'ten_bai_hoc',
        'noi_dung_ly_thuyet',
        
        // Nhóm Code IT vừa thêm
        'ten_file_code',
        'ma_nguon_mau',
        'ngon_ngu_code',
        'giai_thich_code',
        
        'video_url',
        'tai_lieu_dinh_kem',
        'thoi_luong_phut',
        'thu_tu',
        'cho_phep_hoc_thu',
        'trang_thai',
    ];

    /**
     * Ép kiểu dữ liệu tự động (Cực kỳ quan trọng)
     */
    protected $casts = [
        // Biến chuỗi JSON trong DB thành Mảng (Array) trong PHP để dễ dùng vòng lặp
        'giai_thich_code'  => 'array', 
        
        'cho_phep_hoc_thu' => 'boolean',
        'trang_thai'       => 'boolean',
        'thu_tu'           => 'integer',
        'thoi_luong_phut'  => 'integer',
    ];

    // ==========================================
    // CÁC MỐI QUAN HỆ (RELATIONSHIPS)
    // ==========================================

    /**
     * Bài học thuộc về một Chương học
     */
    public function chuongHoc()
    {
        return $this->belongsTo(ChuongHoc::class, 'chuong_hoc_id');
    }

    /**
     * Bài học có nhiều câu hỏi kiểm tra nhanh
     */
    public function cauHois()
    {
        return $this->hasMany(CauHoi::class, 'bai_hoc_id');
    }
}