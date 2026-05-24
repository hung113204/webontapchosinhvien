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
        Schema::table('phien_luyen_tap', function (Blueprint $table) {
            // Thêm cột lưu danh sách ID câu hỏi đã random (Tránh lỗi null gây nhảy trang)
            if (!Schema::hasColumn('phien_luyen_tap', 'danh_sach_cau_hoi')) {
                $table->json('danh_sach_cau_hoi')->nullable()->after('so_cau_hoi');
            }

            // Thêm cột lưu chi tiết đáp án người dùng chọn để xem lại kết quả
            if (!Schema::hasColumn('phien_luyen_tap', 'ket_qua_chi_tiet')) {
                $table->json('ket_qua_chi_tiet')->nullable()->after('danh_sach_cau_hoi');
            }
        });
    }

    public function down(): void
    {
        Schema::table('phien_luyen_tap', function (Blueprint $table) {
            $table->dropColumn(['danh_sach_cau_hoi', 'ket_qua_chi_tiet']);
        });
    }
};