<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class danhmuctrangchu extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Tên bảng trong database.
     *
     * @var string
     */
    protected $table = 'danh_muc_trang_chu';

    /**
     * Các trường có thể gán dữ liệu hàng loạt (Mass Assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = ['tieu_de', 'slug', 'mo_ta', 'loai_danh_muc', 'icon_class', 'hinh_anh', 'thu_tu', 'so_luong_hien_thi', 'trang_thai'];

    /**
     * Ép kiểu dữ liệu cho các trường.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'trang_thai' => 'boolean',
        'thu_tu' => 'integer',
        'so_luong_hien_thi' => 'integer',
    ];

    // GỢI Ý: Bạn có thể viết thêm các Local Scope ở đây để query nhanh hơn ở Controller.
    // Ví dụ: Scope lấy các danh mục đang hoạt động và sắp xếp theo thứ tự
    /**
     * Scope: Lấy danh mục đang active và sắp xếp theo thứ tự.
     */
    public function scopeActiveAndOrdered($query)
    {
        return $query->where('trang_thai', true)->orderBy('thu_tu', 'asc');
    }

    /**
     * Một danh mục trang chủ có thể có nhiều môn học.
     */
    public function monHocs()
    {
        return $this->belongsToMany(\App\Models\MonHoc::class, 'danh_muc_mon_hoc', 'danh_muc_id', 'mon_hoc_id');
    }
}