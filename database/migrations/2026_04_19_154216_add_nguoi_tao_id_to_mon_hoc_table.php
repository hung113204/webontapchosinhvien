<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('mon_hoc', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->unsignedBigInteger('nguoi_tao_id')->nullable()->after('id');
            // Nếu có dùng khóa ngoại:
            // $table->foreign('nguoi_tao_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mon_hoc', function (Blueprint $table) {
            //
        });
    }
};