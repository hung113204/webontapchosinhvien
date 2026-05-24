<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhanCongBaiTap extends Model
{
    // Bắt buộc khai báo để khớp với tên db bạn đã đặt
    protected $table = 'phan_cong_bai_tap'; 

    protected $fillable = ['lop_hoc_id', 'bai_kiem_tra_id'];
    
    // Các quan hệ ngược lại
    public function lopHoc() {
        return $this->belongsTo(LopHoc::class, 'lop_hoc_id');
    }

    public function baiKiemTra() {
        return $this->belongsTo(BaiKiemTra::class, 'bai_kiem_tra_id');
    }
}