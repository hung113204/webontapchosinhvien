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
        Schema::create('danh_muc_trang_chu', function (Blueprint $table) {
            $table->id();
            $table->string('tieu_de');
            $table->string('slug')->unique();
            $table->string('mo_ta')->nullable();
            $table->string('loai_danh_muc')->comment('banner, course_list, stats, testimonial');
            $table->string('icon_class')->nullable();
            $table->string('hinh_anh')->nullable();
            $table->integer('thu_tu')->default(0);
            $table->integer('so_luong_hien_thi')->default(4);
            $table->boolean('trang_thai')->default(true);

            $table->timestamps();
            $table->softDeletes();

            
            $table->index(['trang_thai', 'thu_tu']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('danh_muc_trang_chu');
    }
};