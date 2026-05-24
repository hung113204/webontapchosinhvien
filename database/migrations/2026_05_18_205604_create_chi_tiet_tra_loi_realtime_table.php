<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('chi_tiet_tra_loi_realtime', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phong_quiz_id')->constrained('phong_quiz')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('cau_hoi_id')->constrained('cau_hoi')->onDelete('cascade');
            $table->foreignId('dap_an_id')->nullable()->constrained('dap_an')->onDelete('cascade')->comment('ID đáp án sinh viên bấm chọn');
            $table->integer('thoi_gian_tra_loi')->default(0)->comment('Số mili-giây tính từ lúc câu hỏi bắt đầu hiển thị');
            $table->integer('diem_dat_duoc')->default(0)->comment('Điểm số nhận được cho câu này (0 nếu chọn sai)');
            $table->boolean('is_chinh_xac')->default(false)->comment('Lưu nhanh trạng thái đúng/sai để vẽ biểu đồ thống kê kết thúc câu');
            $table->timestamps();
            $table->unique(['phong_quiz_id', 'user_id', 'cau_hoi_id'], 'unique_realtime_answer');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_tra_loi_realtime');
    }
};