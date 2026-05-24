<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaiKiemTra extends Model
{
    use HasFactory, SoftDeletes;

    // Khai báo tên bảng chính xác theo Migration của bạn
    protected $table = 'bai_kiem_tra';

    protected $fillable = ['ten_bai', 'mo_ta', 'mon_hoc_id', 'nguoi_tao_id', 'thoi_gian_phut', 'thoi_gian_bat_dau', 'thoi_gian_ket_thuc', 'so_lan_lam_bai', 'cach_tinh_diem', 'tu_dong_lay_de', 'xem_diem', 'xem_bai_lam', 'dao_cau_hoi', 'dao_dap_an', 'nop_khi_chuyen_tab', 'so_luot_thi', 'diem_trung_binh', 'trang_thai', 'chuong_hoc_ids'];

    const STATUS_PUBLIC =1;
    const STATUS_PRIVATE =0;

    protected $status = [
        1=>[
            'name'=>'Public',
        ],
        0=>[
            'name'=>'Private',
        ]
    ];

    public function getStatus()
    {
        return $this->status[$this->trang_thai]['name'] ?? 'Unknown';
    }

    // Ép kiểu các trường Boolean và Datetime để xử lý logic dễ dàng hơn
    protected $casts = [
        'thoi_gian_bat_dau' => 'datetime',
        'thoi_gian_ket_thuc' => 'datetime',
        'tu_dong_lay_de' => 'boolean',
        'xem_diem' => 'boolean',
        'xem_bai_lam' => 'boolean',
        'dao_cau_hoi' => 'boolean',
        'dao_dap_an' => 'boolean',
        'nop_khi_chuyen_tab' => 'boolean',
        'diem_trung_binh' => 'decimal:2',
        'trang_thai' => 'integer',
        'chuong_hoc_ids' => 'array',
    ];

    /**
     * Quan hệ: Bài kiểm tra thuộc về một môn học
     */
    public function monHoc()
    {
        return $this->belongsTo(MonHoc::class, 'mon_hoc_id');
    }

    /**
     * Quan hệ: Bài kiểm tra do một giáo viên (User) tạo ra
     */
    public function nguoiTao()
    {
        return $this->belongsTo(User::class, 'nguoi_tao_id');
    }

    /**
     * Quan hệ: Một bài kiểm tra có thể có nhiều kết quả thi (Theo file .txt)
     */
    public function ketQuaThi()
    {
        return $this->hasMany(KetQuaThi::class, 'bai_kiem_tra_id');
    }

    /**
     * Quan hệ: Một bài kiểm tra có thể chứa nhiều câu hỏi (Nếu bạn làm bảng trung gian)
     */
    public function cauHois()
    {
        return $this->belongsToMany(CauHoi::class, 'bai_kiem_tra_cau_hoi', 'bai_kiem_tra_id', 'cau_hoi_id')->withPivot('thu_tu', 'diem')->withTimestamps();
    }
   // public function phanCongLops()
    //{
      //  return $this->hasMany(PhanCongBaiTap::class, 'bai_kiem_tra_id');
    //}
    public function baiKiemTraCauHois()
    {
        return $this->hasMany(BaiKiemTraCauHoi::class, 'bai_kiem_tra_id');
    }

    /**
     * Scope: Chỉ lấy bài kiểm tra của giáo viên hiện tại (nếu là Teacher)
     * Admin thì được xem tất cả
     */
    public function scopeForTeacher($query, $user = null)
    {
        if (!$user) {
            $user = auth()->user();
        }

        // Nếu là Teacher thì chỉ xem bài của mình
        if ($user && $user->isTeacher()) {
            return $query->where('nguoi_tao_id', $user->id);
        }

        // Nếu là Admin thì xem tất cả
        return $query;
    }
}