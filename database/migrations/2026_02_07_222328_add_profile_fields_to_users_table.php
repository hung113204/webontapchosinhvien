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
        Schema::table('users', function (Blueprint $table) {
            $table->string('gioi_tinh', 10)->nullable()->after('email');
            $table->date('ngay_sinh')->nullable()->after('gioi_tinh');
            $table->text('dia_chi')->nullable()->after('ngay_sinh');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['gioi_tinh', 'ngay_sinh', 'dia_chi']);
        });
    }
};