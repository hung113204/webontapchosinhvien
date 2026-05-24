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
        Schema::table('thanh_vien_phong', function (Blueprint $table) {
            $table->string('biem_danh')->nullable()->after('user_id')->comment('Biệt danh hiển thị trong phòng đấu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('thanh_vien_phong', function (Blueprint $table) {
            $table->dropColumn('biem_danh');
        });
    }
};
