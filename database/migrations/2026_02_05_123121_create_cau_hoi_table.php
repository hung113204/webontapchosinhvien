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
        Schema::create('cau_hoi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chuong_hoc_id')->constrained('chuong_hoc')->onDelete('cascade')->comment('Câu hỏi thuộc chương nào');
            $table->foreignId('nguoi_tao_id')->nullable()->constrained('users')->nullOnDelete()->comment('Giáo viên tạo câu hỏi này');
            $table->longText('noi_dung');
            $table->string('hinh_anh')->nullable()->comment('Ảnh minh họa');
            $table->longText('goi_y')->nullable();
            $table->longText('giai_thich')->nullable();
            $table->tinyInteger('muc_do')->default(1)->comment('1: Dễ, 2: Trung bình, 3: Khó');
            $table->tinyInteger('loai_cau_hoi')->default(1)->comment('1: Trắc nghiệm 1 đáp án, 2: Trắc nghiệm nhiều đáp án, 3: Đúng/Sai');
            $table->integer('so_lan_su_dung')->default(0);
            $table->decimal('ty_le_dung', 5, 2)->default(0)->comment('Tỷ lệ trả lời đúng %');
            $table->boolean('trang_thai')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['chuong_hoc_id', 'muc_do', 'trang_thai']);
            $table->index('loai_cau_hoi');
            $table->index('nguoi_tao_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cau_hoi');
    }
};