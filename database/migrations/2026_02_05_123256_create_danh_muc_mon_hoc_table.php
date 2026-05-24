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
    Schema::create('danh_muc_mon_hoc', function (Blueprint $table) {
        $table->id();
        $table->string('ten_danh_muc'); 
        $table->string('slug')->unique(); 
        $table->text('mo_ta')->nullable(); 
        $table->string('icon_class')->nullable()->comment('Icon hiển thị cạnh tên danh mục');
        $table->string('hinh_anh')->nullable()->comment('Ảnh đại diện cho danh mục');
        $table->integer('thu_tu')->default(0); 
        $table->boolean('trang_thai')->default(true); 

        $table->timestamps();
        $table->softDeletes();
        $table->index('trang_thai');
        $table->index('thu_tu');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('danh_muc_mon_hoc');
    }
};