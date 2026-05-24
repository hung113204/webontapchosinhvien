<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lich_su_hoi_dap_ai', function (Blueprint $table) {
            $table->string('session_key', 80)->nullable()->after('user_id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('lich_su_hoi_dap_ai', function (Blueprint $table) {
            $table->dropIndex(['session_key']);
            $table->dropColumn('session_key');
        });
    }
};
