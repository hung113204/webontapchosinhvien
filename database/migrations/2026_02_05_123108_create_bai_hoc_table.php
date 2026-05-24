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
        Schema::create('bai_hoc', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chuong_hoc_id')->constrained('chuong_hoc')->onDelete('cascade')->comment('Bài học thuộc chương nào');
            $table->string('ten_bai_hoc'); //
            $table->longText('noi_dung_ly_thuyet')->nullable(); 
            $table->string('video_url')->nullable()->comment('Link video bài giảng (Youtube/Vimeo)');
            $table->string('tai_lieu_dinh_kem')->nullable()->comment('Đường dẫn file PDF/Slide');
            $table->integer('thu_tu')->default(0); 
            $table->integer('thoi_luong_phut')->default(0)->comment('Thời gian dự kiến hoàn thành');
            $table->boolean('cho_phep_hoc_thu')->default(false);
            $table->boolean('trang_thai')->default(true)->comment('1: Hoạt động, 0: Ẩn');

            $table->timestamps();
            $table->softDeletes();
            $table->index('chuong_hoc_id');
            $table->index('thu_tu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bai_hoc');
    }
};