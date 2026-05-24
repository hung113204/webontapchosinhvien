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
        Schema::table('bai_hoc', function (Blueprint $table) {
            // Thêm 4 cột này vào sau cột 'noi_dung_ly_thuyet' cho gọn gàng
            $table->string('ten_file_code', 100)->nullable()->comment('VD: hello.cpp, index.php')->after('noi_dung_ly_thuyet');
            $table->text('ma_nguon_mau')->nullable()->comment('Đoạn code hiển thị trong bài')->after('ten_file_code');
            $table->string('ngon_ngu_code', 50)->nullable()->comment('VD: cpp, php, js, html')->after('ma_nguon_mau');
            $table->json('giai_thich_code')->nullable()->comment('Lưu mảng JSON giải thích code')->after('ngon_ngu_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bai_hoc', function (Blueprint $table) {
            $table->dropColumn([
                'ten_file_code', 
                'ma_nguon_mau', 
                'ngon_ngu_code', 
                'giai_thich_code'
            ]);
        });
    }
};