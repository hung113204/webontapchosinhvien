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
        Schema::create('lich_su_hoi_dap_ai', function (Blueprint $table) {
            $table->id();

            // 1. Liên kết với sinh viên (Sử dụng user_id để lưu vết từng người)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // 2. Liên kết với môn học (Để AI biết đang hỏi về môn PHP, C++ hay Kinh tế...)
            $table->foreignId('mon_hoc_id')->nullable()->constrained('mon_hoc')->onDelete('set null');

            // 3. Liên kết với câu hỏi trắc nghiệm cụ thể (Nếu hỏi giải thích một câu Quiz)
            $table->foreignId('cau_hoi_id')->nullable()->constrained('cau_hoi')->onDelete('set null');

            // 4. Nội dung chi tiết
            $table->text('cau_hoi')->comment('Câu hỏi của sinh viên');
            $table->longText('cau_tra_loi')->nullable()->comment('Câu trả lời từ AI');

            // 5. Thông tin bổ sung
            $table->string('model_ai')->default('gemini-1.5-flash');
            $table->integer('so_token_su_dung')->default(0); 
            $table->boolean('is_helpful')->nullable()->comment('Đánh giá: 1 là hữu ích, 0 là không');

            $table->timestamps();

            // Index để tìm kiếm lịch sử theo user nhanh hơn
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lich_su_hoi_dap_ai');
    }
};