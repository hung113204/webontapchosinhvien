<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ket_qua_thi_v2', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('bai_kiem_tra_id')->constrained('bai_kiem_tra')->onDelete('cascade');
            $table->float('diem')->default(0); // Điểm số chính thức
            $table->integer('so_cau_dung')->default(0);
            $table->integer('tong_so_cau')->default(0);
            $table->timestamp('thoi_gian_vao_thi')->nullable();
            $table->timestamp('thoi_gian_nop_bai')->nullable();
            $table->string('tong_thoi_gian_lam')->nullable()->comment('Ví dụ: 15 phút 30 giây');
            $table->integer('so_lan_vi_pham_tab')->default(0)->comment('Số lần chuyển tab khi thi');
            $table->longText('ai_feedback')->nullable()->comment('Lời khuyên lộ trình học từ AI');
            $table->float('xep_hang_phan_tram')->nullable()->comment('Vị trí so với cả lớp, ví dụ: Top 10%');
            $table->tinyInteger('trang_thai')->default(1)->comment('1: Đã nộp bài, 2: Đang chấm, 3: Bị hủy');
            $table->timestamps();
            $table->index(['user_id', 'bai_kiem_tra_id']);
            $table->index('diem');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ket_qua_thi_v2');
    }
};
