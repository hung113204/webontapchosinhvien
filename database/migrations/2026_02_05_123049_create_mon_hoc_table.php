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
        Schema::create('mon_hoc', function (Blueprint $table) {
            $table->id(); 
            $table->string('ten_mon_hoc');
            $table->string('ma_mon_hoc')->unique()->nullable()->comment('Mã môn học (ví dụ: IT001)');
            $table->string('mo_ta_ngan', 500)->nullable()->comment('Mô tả ngắn về môn học');
            $table->text('mo_ta_chi_tiet')->nullable()->comment('Mô tả chi tiết, đề cương môn học');
            $table->string('icon_class')->nullable()->comment('Class icon (FontAwesome, Bootstrap Icons)');
            $table->string('hinh_anh')->nullable()->comment('Đường dẫn hình ảnh đại diện môn học');
            $table->string('mau_sac')->default('#3498db')->comment('Màu sắc đại diện môn học');
            $table->integer('so_tin_chi')->default(3)->comment('Số tín chỉ của môn học');
            $table->tinyInteger('muc_do_mon_hoc')->default(1)->comment('1: Dễ, 2: Trung bình, 3: Khó, 4: Rất khó');
            $table->integer('thu_tu')->default(0)->comment('Thứ tự hiển thị');
            $table->boolean('is_popular')->default(false)->comment('Có phải môn học phổ biến?');
            $table->boolean('is_featured')->default(false)->comment('Có hiển thị nổi bật?');
            $table->integer('so_luong_bai_hoc')->default(0)->comment('Tổng số bài học trong môn');
            $table->integer('so_luong_cau_hoi')->default(0)->comment('Tổng số câu hỏi trong môn');
            $table->integer('so_luong_nguoi_hoc')->default(0)->comment('Số người đang học môn này');
            $table->boolean('trang_thai')->default(true)->comment('1: Hoạt động, 0: Tạm ẩn');
            $table->timestamps();
            $table->softDeletes(); 
            $table->index('muc_do_mon_hoc');
            $table->index('trang_thai');
            $table->index(['trang_thai', 'is_featured']);
            $table->index('thu_tu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mon_hoc');
    }
};
