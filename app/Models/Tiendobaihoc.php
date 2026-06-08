<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TienDoBaiHoc extends Model
{
    use HasFactory;

    protected $table = 'tien_do_bai_hoc';

    protected $fillable = [
        'user_id',
        'bai_hoc_id',
        'trang_thai',
        'phan_tram_hoan_thanh',
        'ngay_hoan_thanh',
    ];

    protected $casts = [
        'ngay_hoan_thanh' => 'datetime',
    ];

    // 1. Liên kết tới bài học để lấy tên bài, nội dung
    public function baiHoc()
    {
        return $this->belongsTo(BaiHoc::class, 'bai_hoc_id');
    }

    // 2. Lấy các phiên luyện tập của CÙNG NGƯỜI DÙNG và CÙNG MÔN HỌC với bài học này
    // Cách này giúp Hùng hiện ra: "Trong lúc học bài này, Hùng đã luyện tập được kết quả như sau..."
    public function getDanhSachLuyenTapAttribute()
{
    if (!$this->baiHoc || !$this->baiHoc->chuongHoc) {
        return collect();
    }

    $monHocId = $this->baiHoc->chuongHoc->mon_hoc_id;

    return PhienLuyenTap::where('user_id', $this->user_id)
        ->where('mon_hoc_id', $monHocId)
        ->orderBy('created_at', 'desc')
        ->get();
}
    
}