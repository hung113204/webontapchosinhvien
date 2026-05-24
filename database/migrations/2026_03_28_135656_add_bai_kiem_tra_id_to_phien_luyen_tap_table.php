<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('phien_luyen_tap', function (Blueprint $table) {
            $table->foreignId('bai_kiem_tra_id')
                  ->nullable()
                  ->after('user_id') // hoặc đặt vị trí bạn muốn
                  ->constrained('bai_kiem_tra')
                  ->nullOnDelete()
                  ->comment('Liên kết bài kiểm tra (dùng cho thi thử)');

            $table->index('bai_kiem_tra_id');
        });
    }

    public function down(): void
    {
        Schema::table('phien_luyen_tap', function (Blueprint $table) {
            $table->dropForeign(['bai_kiem_tra_id']);
            $table->dropIndex(['bai_kiem_tra_id']);
            $table->dropColumn('bai_kiem_tra_id');
        });
    }
};