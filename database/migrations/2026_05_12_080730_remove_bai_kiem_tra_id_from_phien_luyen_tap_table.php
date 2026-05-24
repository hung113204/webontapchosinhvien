<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('phien_luyen_tap', function (Blueprint $table) {
            // Xóa khóa ngoại trước khi xóa cột
            $table->dropForeign(['bai_kiem_tra_id']);
            $table->dropColumn('bai_kiem_tra_id');
        });
    }

    public function down(): void
    {
        Schema::table('phien_luyen_tap', function (Blueprint $table) {
            $table->foreignId('bai_kiem_tra_id')->nullable()->constrained('bai_kiem_tra');
        });
    }
};