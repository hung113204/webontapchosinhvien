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
        Schema::table('tien_do_bai_hoc', function (Blueprint $table) {
            if (Schema::hasColumn('tien_do_bai_hoc', 'thoi_gian_da_hoc')) {
                $table->dropColumn('thoi_gian_da_hoc');
            }
            if (Schema::hasColumn('tien_do_bai_hoc', 'ngay_bat_dau')) {
                $table->dropColumn('ngay_bat_dau');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tien_do_bai_hoc', function (Blueprint $table) {
            if (!Schema::hasColumn('tien_do_bai_hoc', 'thoi_gian_da_hoc')) {
                $table->integer('thoi_gian_da_hoc')->default(0);
            }
            if (!Schema::hasColumn('tien_do_bai_hoc', 'ngay_bat_dau')) {
                $table->timestamp('ngay_bat_dau')->nullable();
            }
        });
    }
};
