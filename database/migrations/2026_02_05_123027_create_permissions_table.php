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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id(); 
            $table->string('name'); 
            $table->string('slug')->unique(); 
            $table->string('module')->nullable()->comment('Module/Phân hệ chứa quyền');
            $table->text('description')->nullable()->comment('Mô tả chi tiết quyền');
            $table->boolean('is_system')->default(false)->comment('Là quyền hệ thống (không được xóa)');
            $table->boolean('trang_thai')->default(true)->comment('1: Hoạt động, 0: Vô hiệu hóa');
            $table->timestamps();
            $table->softDeletes();
            $table->index('slug');
            $table->index('module');
            $table->index('trang_thai');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};