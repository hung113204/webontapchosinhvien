<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DapAn extends Model
{
    use HasFactory;

    // Khai báo tên bảng chính xác theo Migration của bạn
    protected $table = 'dap_an';

    protected $fillable = [
        'cau_hoi_id',
        'noi_dung',
        'hinh_anh',
        'is_dung',
        'thu_tu',
        'ky_hieu',
        'so_luot_chon',
        'ty_le_chon',
        'trang_thai',
    ];

    // Ép kiểu dữ liệu để đảm bảo tính chính xác khi xử lý logic tính điểm
    protected $casts = [
        'is_dung'       => 'boolean',
        'trang_thai'    => 'boolean',
        'thu_tu'        => 'integer',
        'so_luot_chon'  => 'integer',
        'ty_le_chon'    => 'decimal:2',
    ];

    /**
     * Quan hệ: Một đáp án thuộc về một câu hỏi cụ thể
     */
    public function cauHoi()
    {
        return $this->belongsTo(CauHoi::class, 'cau_hoi_id');
    }

    /**
     * Scope hỗ trợ chỉ lấy những đáp án đúng của một câu hỏi
     */
    public function scopeCorrect($query)
    {
        return $query->where('is_dung', true);
    }

    /**
     * Scope hỗ trợ lấy các đáp án đang hiển thị
     */
    public function scopeActive($query)
    {
        return $query->where('trang_thai', true);
    }
}