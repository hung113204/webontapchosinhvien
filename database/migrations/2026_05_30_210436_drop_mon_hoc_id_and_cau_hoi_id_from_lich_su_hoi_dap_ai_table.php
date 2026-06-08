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
        if (Schema::hasColumn('lich_su_hoi_dap_ai', 'mon_hoc_id')) {
            try {
                Schema::table('lich_su_hoi_dap_ai', function (Blueprint $table) {
                    $table->dropForeign(['mon_hoc_id']);
                });
            } catch (\Exception $e) {}
            try {
                Schema::table('lich_su_hoi_dap_ai', function (Blueprint $table) {
                    $table->dropColumn('mon_hoc_id');
                });
            } catch (\Exception $e) {}
        }
        
        if (Schema::hasColumn('lich_su_hoi_dap_ai', 'cau_hoi_id')) {
            try {
                Schema::table('lich_su_hoi_dap_ai', function (Blueprint $table) {
                    $table->dropForeign(['cau_hoi_id']);
                });
            } catch (\Exception $e) {}
            try {
                Schema::table('lich_su_hoi_dap_ai', function (Blueprint $table) {
                    $table->dropColumn('cau_hoi_id');
                });
            } catch (\Exception $e) {}
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lich_su_hoi_dap_ai', function (Blueprint $table) {
            $table->foreignId('mon_hoc_id')->nullable()->constrained('mon_hoc')->onDelete('set null');
            $table->foreignId('cau_hoi_id')->nullable()->constrained('cau_hoi')->onDelete('set null');
        });
    }
};
