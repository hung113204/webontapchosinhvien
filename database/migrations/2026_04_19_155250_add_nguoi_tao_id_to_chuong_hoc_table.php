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
        Schema::table('chuong_hoc', function (Blueprint $table) {
            // Thêm cột nguoi_tao_id sau cột id
            $table->unsignedBigInteger('nguoi_tao_id')->nullable()->after('id');

            // Nếu bạn muốn ràng buộc khóa ngoại với bảng users
            $table->foreign('nguoi_tao_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('chuong_hoc', function (Blueprint $table) {
            $table->dropForeign(['nguoi_tao_id']);
            $table->dropColumn('nguoi_tao_id');
        });
    }
};