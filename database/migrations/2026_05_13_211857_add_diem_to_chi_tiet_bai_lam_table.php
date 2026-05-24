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
            $table->decimal('diem', 8, 2)->default(0)->after('is_correct')->comment('Điểm số đạt được cho câu hỏi này');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chi_tiet_bai_lam', function (Blueprint $table) {
            $table->dropColumn('diem');
        });
    }
};
