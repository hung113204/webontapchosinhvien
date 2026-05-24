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
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade'); //
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade'); //
            $table->boolean('can_view')->default(false)->comment('Quyền xem'); //
            $table->boolean('can_create')->default(false)->comment('Quyền thêm mới'); //
            $table->boolean('can_update')->default(false)->comment('Quyền chỉnh sửa'); //
            $table->boolean('can_delete')->default(false)->comment('Quyền xóa'); //
            $table->boolean('trang_thai')->default(true)->comment('1: Hoạt động, 0: Vô hiệu hóa');

            $table->timestamps();
            $table->index(['role_id', 'permission_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
    }
};