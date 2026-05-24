<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiTietBaiLam extends Model
{
    use HasFactory;

    // Khai báo tên bảng chính xác theo Migration của bạn
    protected $table = 'chi_tiet_bai_lam';

    protected $fillable = [
        'ket_qua_id',
        'cau_hoi_id',
        'dap_an_chon_id',
        'cau_tra_loi_tu_luan',
        'diem',
        'is_correct',
        'is_marked',
        'thoi_gian_tra_loi',
        'thu_tu_tra_loi',
        'dap_an_dung_ids',
        'dap_an_chon_ids',
        'phan_tich_sai',
    ];

    /**
     * Ép kiểu dữ liệu để xử lý logic trong code
     */
    protected $casts = [
        'is_correct'        => 'boolean',
        'is_marked'         => 'boolean',
        'thoi_gian_tra_loi' => 'integer',
        'thu_tu_tra_loi'    => 'integer',
        'dap_an_dung_ids'   => 'array', // Tự động convert JSON sang Array
        'dap_an_chon_ids'   => 'array', // Tự động convert JSON sang Array
    ];

    // --- QUAN HỆ (RELATIONSHIPS) ---

    /**
     * Quan hệ: Thuộc về một kết quả thi tổng quát
     */
    public function ketQua()
    {
        return $this->belongsTo(KetQuaThi::class, 'ket_qua_id');
    }

    /**
     * Quan hệ: Liên kết với câu hỏi cụ thể trong bài thi
     */
    public function cauHoi()
    {
        return $this->belongsTo(CauHoi::class, 'cau_hoi_id');
    }

    /**
     * Quan hệ: Liên kết với đáp án mà người dùng đã chọn (cho câu 1 đáp án)
     */
    public function dapAnChon()
    {
        return $this->belongsTo(DapAn::class, 'dap_an_chon_id');
    }

    // --- CÁC HÀM HỖ TRỢ (HELPERS) ---

    /**
     * Kiểm tra xem câu hỏi này sinh viên có đánh dấu xem lại không
     */
    public function isMarkedForReview()
    {
        return $this->is_marked === true;
    }
}