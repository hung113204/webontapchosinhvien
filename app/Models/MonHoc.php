<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MonHoc extends Model
{
    use HasFactory;

    protected $table = 'mon_hoc';

    protected $fillable = ['ten_mon_hoc', 'ma_mon_hoc', 'mo_ta_ngan', 'icon_class', 'hinh_anh', 'mau_sac', 'so_tin_chi', 'muc_do_mon_hoc', 'thu_tu', 'is_popular', 'is_featured', 'so_luong_bai_hoc', 'so_luong_cau_hoi', 'so_luong_nguoi_hoc', 'trang_thai', 'nguoi_tao_id'];

    const STATUS_PUBLIC = 1;
    const STATUS_PRIVATE = 0;

    protected $status = [
        1 => [
            'name' => 'Public',
        ],
        0 => [
            'name' => 'Private',
        ],
    ];

    public function getStatus()
    {
        return $this->status[$this->trang_thai]['name'] ?? 'Unknown';
    }
    /**
     * Ép kiểu dữ liệu khi lấy từ DB ra để tiện xử lý logic
     */
    protected $casts = [
        'is_popular' => 'boolean',
        'is_featured' => 'boolean',
        'trang_thai' => 'boolean',
        'muc_do_mon_hoc' => 'integer',
        'so_tin_chi' => 'integer',
        'thu_tu' => 'integer',
    ];

    /**
     * Một môn học có nhiều Chương học
     */
    public function chuongHocs()
    {
        return $this->hasMany(ChuongHoc::class, 'mon_hoc_id');
    }

    /**
     * Một môn học có nhiều Bài kiểm tra [cite: 4]
     */
    public function baiKiemTras()
    {
        return $this->hasMany(BaiKiemTra::class, 'mon_hoc_id');
    }

    /**
     * Một môn học có thể xuất hiện trong nhiều danh mục trang chủ [cite: 8]
     */
    public function danhMucTrangChus()
    {
        //   return $this->belongsToMany(DanhMucTrangChu::class, 'danh_muc_mon_hoc', 'mon_hoc_id', 'danh_muc_id');
    }

    public function cauHois()
    {
        // Tham số: Model đích (CauHoi), Model trung gian (ChuongHoc), khóa ngoại trên bảng trung gian, khóa ngoại trên bảng đích
        return $this->hasManyThrough(
            CauHoi::class,
            ChuongHoc::class,
            'mon_hoc_id', // Khóa ngoại trên bảng chuong_hoc
            'chuong_hoc_id', // Khóa ngoại trên bảng cau_hoi
            'id', // Khóa chính của bảng mon_hoc
            'id', // Khóa chính của bảng chuong_hoc
        );
    }
    public function baiHocs()
    {
        return $this->hasManyThrough(
            BaiHoc::class,
            ChuongHoc::class,
            'mon_hoc_id', // Khóa ngoại trên bảng chuong_hoc
            'chuong_hoc_id', // Khóa ngoại trên bảng bai_hoc
            'id', // Khóa chính của bảng mon_hoc
            'id', // Khóa chính của bảng chuong_hoc
        );
    }
    public function tienDoHocs()
    {
        return $this->hasManyThrough(
            \App\Models\TienDoBaiHoc::class, // Model đích (bạn cần tạo Model này nếu chưa có)
            \App\Models\ChuongHoc::class, // Model trung gian 1
            'mon_hoc_id', // Khóa ngoại trên bảng chuong_hoc
            'bai_hoc_id', // Khóa ngoại trên bảng tien_do_bai_hoc (thực tế phải qua bảng bai_hoc nữa)
            'id', // Khóa chính mon_hoc
            'id', // Khóa chính chuong_hoc
        );
    }

    /**
     * Cách đơn giản và chính xác nhất để đếm số người học duy nhất
     */
    public function getSoLuongNguoiHocThucTe()
    {
        // Lấy tất cả ID bài học thuộc môn này
        $baiHocIds = $this->baiHocs()->pluck('bai_hoc.id');

        // Đếm số user duy nhất trong bảng tien_do_bai_hoc có bai_hoc_id nằm trong danh sách trên
        return DB::table('tien_do_bai_hoc')->whereIn('bai_hoc_id', $baiHocIds)->distinct('user_id')->count('user_id');
    }
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
