<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mon_hoc', function (Blueprint $table) {
            if (Schema::hasColumn('mon_hoc', 'tong_thoi_gian_hoc')) {
                $table->dropColumn('tong_thoi_gian_hoc');
            }
        });
    }

    public function down(): void
    {
        Schema::table('mon_hoc', function (Blueprint $table) {
            if (! Schema::hasColumn('mon_hoc', 'tong_thoi_gian_hoc')) {
                $table->string('tong_thoi_gian_hoc')->nullable()->comment('Tong thoi gian hoc (vi du: "45 gio")');
            }
        });
    }
};
