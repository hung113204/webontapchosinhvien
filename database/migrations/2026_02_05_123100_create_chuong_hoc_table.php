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
        Schema::create('chuong_hoc', function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId('mon_hoc_id')
                ->constrained('mon_hoc')
                ->onDelete('cascade') 
                ->comment('Môn học chứa chương này');
            $table->string('ten_chuong'); 
            $table->text('mo_ta_chuong')->nullable(); 
            $table->integer('thu_tu')->default(0)->comment('Thứ tự hiển thị các chương');
            $table->boolean('trang_thai')->default(true)->comment('1: Hoạt động, 0: Tạm ẩn');

            $table->timestamps();
            $table->softDeletes(); 
            $table->index('mon_hoc_id');
            $table->index('thu_tu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chuong_hoc');
    }
};