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
        Schema::table('mon_hoc', function (Blueprint $table) {
            $table->dropColumn('mo_ta_chi_tiet');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mon_hoc', function (Blueprint $table) {
            $table->text('mo_ta_chi_tiet')->nullable()->comment('Mô tả chi tiết, đề cương môn học')->after('mo_ta_ngan');
        });
    }
};