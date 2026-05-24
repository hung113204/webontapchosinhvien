<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ThanhVienPhong extends Model
{
    protected $table = 'thanh_vien_phong';
    protected $fillable = [
        'phong_quiz_id', 'user_id', 'biem_danh', 'tong_diem', 'so_cau_dung', 'is_ready', 'is_online'
    ];

    // Thuộc về phòng nào
    public function phong(): BelongsTo
    {
        return $this->belongsTo(PhongQuiz::class, 'phong_quiz_id');
    }

    // Lấy thông tin profile của User tương ứng
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Lấy tất cả các câu đã trả lời của thành viên này trong phòng
    public function chiTietTraLoi(): HasMany
    {
        return $this->hasMany(ChiTietTraLoiRealtime::class, 'user_id', 'user_id')
                    ->where('phong_quiz_id', $this->phong_quiz_id);
    }
}