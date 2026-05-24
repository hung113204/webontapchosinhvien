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
    Schema::create('chi_tiet_bai_lam', function (Blueprint $table) {
        $table->id();
        $table->foreignId('ket_qua_id')
              ->constrained('ket_qua_thi')
              ->onDelete('cascade')
              ->comment('Thuộc kết quả bài làm nào');
        $table->foreignId('cau_hoi_id')
              ->constrained('cau_hoi')
              ->onDelete('cascade')
              ->comment('Câu hỏi nào trong bài thi');
        
        $table->foreignId('dap_an_chon_id')
              ->nullable()
              ->constrained('dap_an')
              ->nullOnDelete()
              ->comment('Đáp án người dùng chọn (null = bỏ trống)');
        $table->boolean('is_correct')->default(false)->comment('1: Trả lời đúng, 0: Trả lời sai');
        $table->boolean('is_marked')->default(false)->comment('1: Đánh dấu để xem lại, 0: Không đánh dấu');
        $table->integer('thoi_gian_tra_loi')->default(0)->comment('Thời gian trả lời câu hỏi (giây)');
        $table->integer('thu_tu_tra_loi')->nullable()->comment('Thứ tự trả lời câu hỏi');
        $table->json('dap_an_dung_ids')->nullable()->comment('Danh sách ID đáp án đúng (dạng JSON)');
        $table->json('dap_an_chon_ids')->nullable()->comment('Danh sách ID đáp án chọn (dạng JSON - cho câu nhiều đáp án)');
        $table->text('phan_tich_sai')->nullable()->comment('Phân tích lỗi sai (nếu có)');
        
        $table->timestamps();
        
        // --- INDEX TỐI ƯU ---
        $table->index('ket_qua_id');
        $table->index('cau_hoi_id');
        $table->index('is_correct');
        $table->index('is_marked');
        $table->index(['ket_qua_id', 'is_correct']);
        $table->index(['ket_qua_id', 'cau_hoi_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_bai_lam');
    }
};