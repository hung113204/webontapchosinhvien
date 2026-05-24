<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChiTietTraLoiRealtime extends Model
{
    protected $table = 'chi_tiet_tra_loi_realtime';
    protected $fillable = [
        'phong_quiz_id', 'user_id', 'cau_hoi_id', 'dap_an_id', 'thoi_gian_tra_loi', 'diem_dat_duoc', 'is_chinh_xac'
    ];

    // Thuộc về câu hỏi nào
    public function cauHoi(): BelongsTo
    {
        return $this->belongsTo(CauHoi::class, 'cau_hoi_id');
    }

    // Thuộc về đáp án nào được chọn
    public function dapAn(): BelongsTo
    {
        return $this->belongsTo(DapAn::class, 'dap_an_id');
    }
}