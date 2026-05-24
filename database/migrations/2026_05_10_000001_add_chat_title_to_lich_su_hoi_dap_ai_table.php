<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lich_su_hoi_dap_ai', function (Blueprint $table) {
            $table->string('chat_title', 160)->nullable()->after('session_key');
        });
    }

    public function down(): void
    {
        Schema::table('lich_su_hoi_dap_ai', function (Blueprint $table) {
            $table->dropColumn('chat_title');
        });
    }
};
