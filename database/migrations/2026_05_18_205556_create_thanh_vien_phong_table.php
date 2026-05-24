<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('thanh_vien_phong', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phong_quiz_id')->constrained('phong_quiz')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('tong_diem')->default(0)->comment('Tổng điểm tích lũy trong phòng chơi');
            $table->integer('so_cau_dung')->default(0)->comment('Số lượng câu trả lời đúng');
            $table->boolean('is_ready')->default(false)->comment('Trạng thái sẵn sàng ở màn hình chờ');
            $table->boolean('is_online')->default(true)->comment('1: Đang kết nối, 0: Đã rời phòng/mất mạng');
            $table->timestamps();
            $table->unique(['phong_quiz_id', 'user_id']);
            $table->index('tong_diem');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thanh_vien_phong');
    }
};