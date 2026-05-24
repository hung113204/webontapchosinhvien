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
            // Vì cột nguoi_tao_id đã tồn tại rồi, ta chỉ thêm khóa ngoại thôi
            $table->foreign('nguoi_tao_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('mon_hoc', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->dropForeign(['nguoi_tao_id']);
        });
    }
};