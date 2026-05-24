<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Sử dụng SoftDeletes theo Migration

class CauHoi extends Model
{
    use HasFactory, SoftDeletes;

    // Khai báo tên bảng chính xác
    protected $table = 'cau_hoi';

    protected $fillable = ['chuong_hoc_id', 'nguoi_tao_id','bai_hoc_id', 'noi_dung', 'hinh_anh', 'goi_y', 'giai_thich', 'muc_do', 'loai_cau_hoi', 'so_lan_su_dung', 'ty_le_dung', 'trang_thai'];

    // Ép kiểu dữ liệu để xử lý logic trong code chuẩn hơn
    protected $casts = [
        'muc_do' => 'integer',
        'loai_cau_hoi' => 'integer',
        'ty_le_dung' => 'decimal:2',
        'trang_thai' => 'boolean',
        'so_lan_su_dung' => 'integer',
    ];

    /**
     * Quan hệ: Câu hỏi thuộc về một chương học cụ thể
     */
    public function chuongHoc()
    {
        return $this->belongsTo(ChuongHoc::class, 'chuong_hoc_id');
    }

    /**
     * Quan hệ: Câu hỏi được tạo bởi một giáo viên (User)
     */
    public function nguoiTao()
    {
        return $this->belongsTo(User::class, 'nguoi_tao_id');
    }

    /**
     * Quan hệ: Một câu hỏi có nhiều đáp án (cho Trắc nghiệm)
     */
    public function dapAns()
    {
        return $this->hasMany(DapAn::class, 'cau_hoi_id');
    }
    public function baiKiemTras()
    {
        return $this->belongsToMany(BaiKiemTra::class, 'bai_kiem_tra_cau_hoi', 'cau_hoi_id', 'bai_kiem_tra_id')->withPivot('thu_tu', 'diem')->withTimestamps();
    }

    public function baiHoc()
    {
        return $this->belongsTo(BaiHoc::class, 'bai_hoc_id');
    }
    /**
     * Scope hỗ trợ lọc câu hỏi theo mức độ
     * 1: Dễ, 2: Trung bình, 3: Khó
     */
    public function scopeMucDo($query, $level)
    {
        return $query->where('muc_do', $level);
    }

    /**
     * Scope: Chỉ lấy câu hỏi của giáo viên hiện tại (nếu là Teacher)
     * Admin thì được xem tất cả
     */
    public function scopeForTeacher($query, $user = null)
    {
        if (!$user) {
            $user = auth()->user();
        }

        // Nếu là Teacher thì chỉ xem câu hỏi của mình
        if ($user && $user->isTeacher()) {
            return $query->where('nguoi_tao_id', $user->id);
        }

        // Nếu là Admin thì xem tất cả
        return $query;
    }
}