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
        Schema::create('phien_luyen_tap', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('Người thực hiện phiên luyện tập');
            $table->foreignId('mon_hoc_id')->constrained('mon_hoc')->onDelete('cascade')->comment('Môn học luyện tập');

            $table->foreignId('chuong_hoc_id')->nullable()->constrained('chuong_hoc')->nullOnDelete()->comment('Chương học luyện tập (null = toàn môn)');
            $table->integer('so_cau_hoi')->comment('Số câu hỏi trong phiên');
            $table->tinyInteger('che_do')->default(1)->comment('1: Luyện tập tự do, 2: Thi thử, 3: Ôn tập yếu');
            $table->boolean('gioi_han_thoi_gian')->default(false)->comment('Có giới hạn thời gian?');
            $table->integer('thoi_gian_phut')->nullable()->comment('Thời gian làm bài (phút)');
            $table->timestamp('thoi_gian_bat_dau')->comment('Thời gian bắt đầu phiên');
            $table->timestamp('thoi_gian_ket_thuc')->nullable()->comment('Thời gian kết thúc phiên');
            $table->integer('so_cau_dung')->default(0)->comment('Số câu trả lời đúng');
            $table->decimal('diem_so', 5, 2)->default(0)->comment('Điểm số đạt được');
            $table->decimal('phan_tram_dung', 5, 2)->default(0)->comment('Phần trăm đúng (%)');
            $table->tinyInteger('trang_thai')->default(0)->comment('0: Đang làm, 1: Đã hoàn thành, 2: Bỏ dở giữa chừng');

            $table->timestamps();
            $table->index('user_id');
            $table->index('mon_hoc_id');
            $table->index('chuong_hoc_id');
            $table->index('thoi_gian_bat_dau');
            $table->index('trang_thai');
            $table->index(['user_id', 'mon_hoc_id']);
            $table->index(['user_id', 'trang_thai']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phien_luyen_tap');
    }
};