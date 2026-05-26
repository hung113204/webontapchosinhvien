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
        Schema::table('bai_hoc', function (Blueprint $table) {
            $table->dropColumn('thoi_luong_phut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bai_hoc', function (Blueprint $table) {
            $table->integer('thoi_luong_phut')->default(0)->comment('Thời gian dự kiến hoàn thành')->after('thu_tu');
        });
    }
};