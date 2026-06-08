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
        Schema::table('lich_su_hoi_dap_ai', function (Blueprint $table) {
            if (Schema::hasColumn('lich_su_hoi_dap_ai', 'mon_hoc_id')) {
                $table->dropForeign(['mon_hoc_id']);
                $table->dropColumn('mon_hoc_id');
            }
            if (Schema::hasColumn('lich_su_hoi_dap_ai', 'cau_hoi_id')) {
                $table->dropForeign(['cau_hoi_id']);
                $table->dropColumn('cau_hoi_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lich_su_hoi_dap_ai', function (Blueprint $table) {
            // Re-add columns if needed.
        });
    }
};
