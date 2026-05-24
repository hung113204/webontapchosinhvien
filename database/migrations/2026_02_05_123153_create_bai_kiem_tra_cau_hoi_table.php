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
        Schema::create('bai_kiem_tra_cau_hoi', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('bai_kiem_tra_id')->constrained('bai_kiem_tra')->onDelete('cascade')->comment('Thuộc bài kiểm tra nào');
            $table->foreignId('cau_hoi_id')->constrained('cau_hoi')->onDelete('cascade')->comment('Câu hỏi nào trong bài kiểm tra');
            $table->integer('thu_tu')->default(0)->comment('Thứ tự câu hỏi trong đề thi');
            $table->decimal('diem', 5, 2)->default(1.0)->comment('Điểm của câu hỏi này');
            $table->integer('so_luot_tra_loi')->default(0)->comment('Số lượt trả lời câu này trong bài thi');
            $table->decimal('ty_le_dung', 5, 2)->default(0)->comment('Tỷ lệ đúng câu này trong bài thi (%)');

            $table->timestamps();
            $table->index('bai_kiem_tra_id');
            $table->index('cau_hoi_id');
            $table->index('thu_tu');
            $table->index(['bai_kiem_tra_id', 'thu_tu']);
            $table->unique(['bai_kiem_tra_id', 'cau_hoi_id']);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bai_kiem_tra_cau_hoi');
    }
};