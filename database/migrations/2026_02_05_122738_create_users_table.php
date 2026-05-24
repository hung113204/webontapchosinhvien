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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('ma_sv')->unique()->comment('Tài khoản đăng nhập');
            $table->string('ho_ten');
            $table->string('email')->unique();
            $table->string('mat_khau');
            $table->string('user_token', 80)->unique()->nullable()->index();
            $table->string('reset_password_token', 100)->nullable()->index();
            $table->timestamp('reset_password_expires_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->foreignId('vai_tro_id')->constrained('roles')->onDelete('restrict');
            $table->string('so_dien_thoai', 15)->nullable();
            $table->string('avatar_url')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->boolean('trang_thai')->default(true)->comment('1: Hoạt động, 0: Bị khóa');
            $table->boolean('is_first_login')->default(true);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};