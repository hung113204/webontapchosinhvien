<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('dap_an', function (Blueprint $table) {
        $table->id(); 
        $table->foreignId('cau_hoi_id')
              ->constrained('cau_hoi')
              ->onDelete('cascade')
              ->comment('Đáp án thuộc câu hỏi nào');
        $table->longText('noi_dung')->comment('Nội dung đáp án');
        $table->string('hinh_anh')->nullable()->comment('Hình ảnh minh họa đáp án');
        $table->boolean('is_dung')->default(false)->comment('1: Đáp án đúng, 0: Đáp án sai');
        $table->integer('thu_tu')->default(0)->comment('Thứ tự hiển thị các đáp án');
        $table->char('ky_hieu', 1)->nullable()->comment('Ký hiệu A, B, C, D...');
        $table->integer('so_luot_chon')->default(0)->comment('Số lượt sinh viên chọn đáp án này');
        $table->decimal('ty_le_chon', 5, 2)->default(0)->comment('Tỷ lệ chọn đáp án này (%)');
        $table->boolean('trang_thai')->default(true)->comment('1: Hiển thị, 0: Ẩn');
        
        $table->timestamps();
        
        $table->index('cau_hoi_id');
        $table->index('is_dung');
        $table->index('thu_tu');
        $table->index(['cau_hoi_id', 'is_dung']);
        $table->index(['cau_hoi_id', 'trang_thai']);
        $table->unique(['cau_hoi_id', 'ky_hieu']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dap_an');
    }
};