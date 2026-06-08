<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KetQuaThi extends Model
{
    use HasFactory;

    // Khai báo chính xác tên bảng theo Migration của bạn
    protected $table = 'ket_qua_thi_v2';

    protected $fillable = [
        'user_id',
        'bai_kiem_tra_id',
        'diem',
        'so_cau_dung',
        'tong_so_cau',
        'thoi_gian_vao_thi',
        'thoi_gian_nop_bai',
        'tong_thoi_gian_lam',
        'so_lan_vi_pham_tab',
        'ai_feedback',
        'xep_hang_phan_tram',
        'trang_thai',
    ];

    /**
     * Ép kiểu dữ liệu để xử lý logic tính toán dễ dàng hơn
     */
    protected $casts = [
        'diem'               => 'float',
        'so_cau_dung'        => 'integer',
        'tong_so_cau'        => 'integer',
        'thoi_gian_vao_thi'  => 'datetime',
        'thoi_gian_nop_bai'  => 'datetime',
        'so_lan_vi_pham_tab' => 'integer',
        'xep_hang_phan_tram' => 'float',
        'trang_thai'         => 'integer',
    ];

    // --- QUAN HỆ (RELATIONSHIPS) ---

    /**
     * Quan hệ: Kết quả thi thuộc về một sinh viên (User)
     */
    public function sinhVien()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Quan hệ: Kết quả thi của một bài kiểm tra cụ thể
     */
    public function baiKiemTra()
    {
        return $this->belongsTo(BaiKiemTra::class, 'bai_kiem_tra_id');
    }

    /**
     * Quan hệ: Một kết quả thi có nhiều chi tiết bài làm (từng câu trả lời)
     */
    public function chiTietBaiLam()
    {
        return $this->hasMany(ChiTietBaiLam::class, 'ket_qua_id');
    }

    // --- CÁC HÀM HỖ TRỢ (HELPERS) ---

    /**
     * Kiểm tra bài thi có bị hủy hay không (trạng thái = 3)
     */
    public function isCancelled()
    {
        return $this->trang_thai === 3;
    }

    /**
     * Tính tỷ lệ làm đúng (phần trăm)
     */
    public function getAccuracyAttribute()
    {
        if ($this->tong_so_cau <= 0) return 0;
        return round(($this->so_cau_dung / $this->tong_so_cau) * 100, 2);
    }

    /**
     * Lấy thời gian làm bài dưới dạng số giây (từ thoi_gian_vao_thi và thoi_gian_nop_bai)
     * Dùng để tính toán lại hoặc hiển thị thành phút/giây
     */
    public function getThoiGianLamAttribute()
    {
        if (!$this->thoi_gian_vao_thi || !$this->thoi_gian_nop_bai) {
            return 0;
        }
        return $this->thoi_gian_nop_bai->diffInSeconds($this->thoi_gian_vao_thi);
    }
}