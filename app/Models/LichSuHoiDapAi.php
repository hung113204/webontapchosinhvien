<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LichSuHoiDapAi extends Model
{
    use HasFactory;
    protected $table = 'lich_su_hoi_dap_ai';
    protected $fillable = ['user_id', 'session_key', 'chat_title', 'cau_hoi', 'cau_tra_loi', 'model_ai'];
    public function user() { return $this->belongsTo(User::class); }
}
