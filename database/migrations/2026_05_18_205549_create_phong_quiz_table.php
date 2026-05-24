<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('phong_quiz', function (Blueprint $table) {
            $table->id();
            $table->string('ma_phong', 10)->unique()->comment('Mã PIN vào phòng ngẫu nhiên (Ví dụ: 829104)');
            $table->string('ten_phong')->nullable()->comment('Tên phòng do chủ phòng đặt hoặc tự động');
            $table->foreignId('mon_hoc_id')->constrained('mon_hoc')->onDelete('cascade')->comment('Môn học áp dụng cho phòng chơi');
            $table->tinyInteger('muc_do_cau_hoi')->nullable()->comment('NULL: Hỗn hợp mọi mức độ, 1: Dễ, 2: Trung bình, 3: Khó');
            $table->foreignId('chu_phong_id')->constrained('users')->onDelete('cascade')->comment('ID người tạo phòng');  
            $table->tinyInteger('trang_thai')->default(1)->comment('1: Đang đợi thành viên, 2: Đang thi đấu, 3: Đã kết thúc');
            $table->integer('cau_hoi_hien_tai_id')->nullable()->comment('ID câu hỏi đang hiển thị trên màn hình chính của phòng');
            $table->timestamp('thoi_gian_bat_dau_cau_hoi')->nullable()->comment('Mốc thời gian bắt đầu câu hỏi để tính đếm ngược đằng Frontend');
            $table->integer('thoi_gian_tra_loi_cau_hoi')->default(30)->comment('Số giây giới hạn trả lời của mỗi câu hỏi');
            $table->integer('tong_so_cau')->default(10)->comment('Số lượng câu hỏi được chọn ra cho trận đấu');
            $table->text('danh_sach_cau_hoi')->nullable()->comment('Danh sách ID câu hỏi dưới dạng JSON để chơi trong phòng');
            $table->timestamps();
            $table->softDeletes();
            $table->index('ma_phong');
            $table->index('trang_thai');
            $table->index(['mon_hoc_id', 'muc_do_cau_hoi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phong_quiz');
    }
};