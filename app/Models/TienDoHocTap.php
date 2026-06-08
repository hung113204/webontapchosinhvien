<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'trang_thai'           => 'integer',
        'phan_tram_hoan_thanh' => 'integer',
        'ngay_hoan_thanh'      => 'datetime',
    ];    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function baiHoc(): BelongsTo
    {
        return $this->belongsTo(BaiHoc::class, 'bai_hoc_id');
    }

    // ====================== SCOPES ======================

    public function scopeOfUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeOfBaiHoc($query, $baiHocId)
    {
        return $query->where('bai_hoc_id', $baiHocId);
    }

    public function scopeCompleted($query)
    {
        return $query->where('trang_thai', 2);
    }

    public function scopeInProgress($query)
    {
        return $query->where('trang_thai', 1);
    }

    public function scopeNotStarted($query)
    {
        return $query->where('trang_thai', 0);
    }

    // ====================== HELPER METHODS ======================

    /**
     * Kiểm tra bài học đã hoàn thành chưa
     */
    public function isCompleted(): bool
    {
        return $this->trang_thai === 2;
    }

    /**
     * Kiểm tra đang học
     */
    public function isInProgress(): bool
    {
        return $this->trang_thai === 1;
    }

    /**
     * Kiểm tra chưa học
     */
    public function isNotStarted(): bool
    {
        return $this->trang_thai === 0;
    }

    /**
     * Bắt đầu học bài (chuyển sang trạng thái Đang học)
     */
    public function startLearning(): void
    {
        if ($this->trang_thai === 0) {
            $this->update([
                'trang_thai'     => 1,
            ]);
        }
    }

    /**
     * Cập nhật tiến độ học tập
     */
    public function updateProgress(int $phanTramHoanThanh): void
    {
        $this->phan_tram_hoan_thanh = max(0, min(100, $phanTramHoanThanh));

        // Tự động chuyển trạng thái nếu tiến độ > 0
        if ($this->phan_tram_hoan_thanh > 0 && $this->trang_thai === 0) {
            $this->trang_thai = 1;
        }

        // Tự động hoàn thành nếu đạt >= 95%
        if ($this->phan_tram_hoan_thanh >= 95 && $this->trang_thai !== 2) {
            $this->markAsCompleted();
        } else {
            $this->save();
        }
    }

    /**
     * Đánh dấu hoàn thành bài học
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'trang_thai'           => 2,
            'phan_tram_hoan_thanh' => 100,
            'ngay_hoan_thanh'      => now(),
        ]);
    }

    /**
     * Reset tiến độ bài học (nếu cần học lại)
     */
    public function resetProgress(): void
    {
        $this->update([
            'trang_thai'           => 0,
            'phan_tram_hoan_thanh' => 0,
            'ngay_hoan_thanh'      => null,
        ]);
    }
}