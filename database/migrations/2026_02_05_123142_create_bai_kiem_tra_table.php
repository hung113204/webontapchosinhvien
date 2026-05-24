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
        Schema::create('bai_kiem_tra', function (Blueprint $table) {
            $table->id();
            $table->string('ten_bai');
            $table->text('mo_ta')->nullable()->comment('Mô tả ngắn về bài kiểm tra');
            $table->foreignId('mon_hoc_id')->constrained('mon_hoc')->onDelete('cascade')->comment('Bài kiểm tra thuộc môn học nào');
            $table->foreignId('nguoi_tao_id')->constrained('users')->onDelete('cascade')->comment('Giáo viên tạo bài kiểm tra');
            $table->integer('thoi_gian_phut')->comment('Thời gian làm bài (phút)');
            $table->dateTime('thoi_gian_bat_dau')->nullable()->comment('Thời gian mở bài kiểm tra');
            $table->dateTime('thoi_gian_ket_thuc')->nullable()->comment('Thời gian đóng bài kiểm tra');
            $table->integer('so_lan_lam_bai')->default(1)->comment('Số lần được phép làm bài');
            $table->tinyInteger('cach_tinh_diem')->default(1)->comment('1: 10 điểm, 2: 100 điểm, 3: Thang điểm riêng');
            $table->boolean('tu_dong_lay_de')->default(false)->comment('Tự động lấy đề ngẫu nhiên từ ngân hàng câu hỏi');
            $table->boolean('xem_diem')->default(true)->comment('Cho xem điểm sau khi làm bài');
            $table->boolean('xem_bai_lam')->default(true)->comment('Cho xem bài làm sau khi nộp');
            $table->boolean('dao_cau_hoi')->default(true)->comment('Đảo thứ tự câu hỏi');
            $table->boolean('dao_dap_an')->default(true)->comment('Đảo thứ tự đáp án');
            $table->boolean('nop_khi_chuyen_tab')->default(false)->comment('Tự động nộp bài khi chuyển tab');
            $table->integer('so_luot_thi')->default(0)->comment('Tổng số lượt thi');
            $table->decimal('diem_trung_binh', 5, 2)->default(0)->comment('Điểm trung bình của tất cả lượt thi');
            $table->tinyInteger('trang_thai')->default(0)->comment('0: Bản nháp, 1: Đang mở, 2: Đã đóng');

            $table->timestamps();
            $table->softDeletes();
            $table->index('mon_hoc_id');
            $table->index('nguoi_tao_id');
            $table->index('trang_thai');
            $table->index(['mon_hoc_id', 'trang_thai']);
            $table->index(['thoi_gian_bat_dau', 'thoi_gian_ket_thuc']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bai_kiem_tra');
    }
};