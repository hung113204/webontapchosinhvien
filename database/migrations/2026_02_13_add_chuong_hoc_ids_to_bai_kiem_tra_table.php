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
        Schema::table('bai_kiem_tra', function (Blueprint $table) {
            if (!Schema::hasColumn('bai_kiem_tra', 'chuong_hoc_ids')) {
                $table->json('chuong_hoc_ids')->nullable()->after('mon_hoc_id')->comment('Danh sách chương học được chọn cho bài kiểm tra');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bai_kiem_tra', function (Blueprint $table) {
            if (Schema::hasColumn('bai_kiem_tra', 'chuong_hoc_ids')) {
                $table->dropColumn('chuong_hoc_ids');
            }
        });
    }
};