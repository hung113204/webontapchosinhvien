<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tien_do_bai_hoc', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('bai_hoc_id')->constrained('bai_hoc')->onDelete('cascade');
            $table->tinyInteger('trang_thai')->default(0)->comment('0: Chưa học, 1: Đang học, 2: Đã hoàn thành');
            $table->integer('thoi_gian_da_hoc')->default(0)->comment('Thời gian đã học tính bằng giây');
            $table->integer('phan_tram_hoan_thanh')->default(0)->comment('Tiến độ bài học (%)');
            $table->timestamp('ngay_bat_dau')->nullable();
            $table->timestamp('ngay_hoan_thanh')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'bai_hoc_id']);
            $table->index(['user_id', 'trang_thai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tien_do_bai_hoc');
    }
};