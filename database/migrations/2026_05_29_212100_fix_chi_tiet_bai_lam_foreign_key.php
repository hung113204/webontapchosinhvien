<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('chi_tiet_bai_lam', function (Blueprint $table) {
            // FK cũ đã bị xóa tự động khi ket_qua_thi bị drop
            // Chỉ cần thêm FK mới trỏ vào ket_qua_thi_v2
            $table->foreign('ket_qua_id')
                  ->references('id')
                  ->on('ket_qua_thi_v2')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('chi_tiet_bai_lam', function (Blueprint $table) {
            $table->dropForeign('chi_tiet_bai_lam_ket_qua_id_foreign');

            $table->foreign('ket_qua_id')
                  ->references('id')
                  ->on('ket_qua_thi')
                  ->onDelete('cascade');
        });
    }
};
