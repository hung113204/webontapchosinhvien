<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Phienluyentap extends Model
{
    protected $table = 'phien_luyen_tap';

    protected $fillable = [
        'user_id',
        'mon_hoc_id',
        'chuong_hoc_id',
        'so_cau_hoi',
        'danh_sach_cau_hoi', // Bổ sung mới
        'ket_qua_chi_tiet', // Bổ sung mới
        'che_do',
        'gioi_han_thoi_gian',
        'thoi_gian_phut',
        'thoi_gian_bat_dau',
        'thoi_gian_ket_thuc',
        'so_cau_dung',
        'diem_so',
        'phan_tram_dung',
        'trang_thai',
    ];

    protected $casts = [
        'gioi_han_thoi_gian' => 'boolean',
        'diem_so' => 'decimal:2',
        'danh_sach_cau_hoi' => 'array', // Tự động chuyển JSON thành Array
        'ket_qua_chi_tiet' => 'array', // Tự động chuyển JSON thành Array
        'phan_tram_dung' => 'decimal:2',
        'thoi_gian_bat_dau'  => 'datetime',
        'thoi_gian_ket_thuc' => 'datetime',
    ];

    // -----------------------------------------------------------------------
    // Hằng số trạng thái
    // -----------------------------------------------------------------------
    const TRANG_THAI_DANG_LAM = 0;
    const TRANG_THAI_HOAN_THANH = 1;
    const TRANG_THAI_BO_DO = 2;

    // -----------------------------------------------------------------------
    // Hằng số chế độ
    // -----------------------------------------------------------------------
    const CHE_DO_TU_DO = 1;
    const CHE_DO_THI_THU = 2;
    const CHE_DO_ON_YEU = 3;

    // -----------------------------------------------------------------------
    // Relationships
    // -----------------------------------------------------------------------
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function monHoc(): BelongsTo
    {
        return $this->belongsTo(MonHoc::class, 'mon_hoc_id');
    }

    public function chuongHoc(): BelongsTo
    {
        return $this->belongsTo(ChuongHoc::class, 'chuong_hoc_id');
    }

    // -----------------------------------------------------------------------
    // Scopes tiện dụng
    // -----------------------------------------------------------------------
    public function scopeDangLam($query)
    {
        return $query->where('trang_thai', self::TRANG_THAI_DANG_LAM);
    }

    public function scopeHoanThanh($query)
    {
        return $query->where('trang_thai', self::TRANG_THAI_HOAN_THANH);
    }

    public function scopeOfUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}