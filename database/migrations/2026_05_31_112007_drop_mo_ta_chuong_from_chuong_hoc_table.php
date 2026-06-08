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
        Schema::table('chuong_hoc', function (Blueprint $table) {
            if (Schema::hasColumn('chuong_hoc', 'mo_ta_chuong')) {
                $table->dropColumn('mo_ta_chuong');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chuong_hoc', function (Blueprint $table) {
            if (!Schema::hasColumn('chuong_hoc', 'mo_ta_chuong')) {
                $table->text('mo_ta_chuong')->nullable();
            }
        });
    }
};
