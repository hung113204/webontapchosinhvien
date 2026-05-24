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
        Schema::table('chi_tiet_bai_lam', function (Blueprint $table) {
            $table->text('cau_tra_loi_tu_luan')->nullable()->after('dap_an_chon_id')->comment('Câu trả lời của sinh viên cho câu hỏi tự luận');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chi_tiet_bai_lam', function (Blueprint $table) {
            $table->dropColumn('cau_tra_loi_tu_luan');
        });
    }
};
