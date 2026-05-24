<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaiKiemTraCauHoi extends Model
{
    use HasFactory;

    // Tên bảng tương ứng trong database 
    protected $table = 'bai_kiem_tra_cau_hoi';

    // Các trường cho phép lưu dữ liệu hàng loạt
    protected $fillable = [
        'bai_kiem_tra_id',
        'cau_hoi_id',
        'thu_tu',
        'diem',
        'so_luot_tra_loi',
        'ty_le_dung',
    ];

    /**
     * Quan hệ: Bản ghi này thuộc về một Bài kiểm tra cụ thể 
     */
    public function baiKiemTra()
    {
        return $this->belongsTo(BaiKiemTra::class, 'bai_kiem_tra_id');
    }

    /**
     * Quan hệ: Bản ghi này liên kết tới một Câu hỏi cụ thể 
     */
    public function cauHoi()
    {
        return $this->belongsTo(CauHoi::class, 'cau_hoi_id');
    }
}