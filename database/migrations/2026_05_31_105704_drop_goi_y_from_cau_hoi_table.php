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
        Schema::table('cau_hoi', function (Blueprint $table) {
            $table->dropColumn('goi_y');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cau_hoi', function (Blueprint $table) {
            $table->text('goi_y')->nullable();
        });
    }
};
