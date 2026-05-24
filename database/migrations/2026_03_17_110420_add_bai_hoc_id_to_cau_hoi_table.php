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
        Schema::table('cau_hoi', function (Blueprint $table) {
            $table->foreignId('bai_hoc_id')->nullable()->constrained('bai_hoc')->onDelete('cascade')->after('chuong_hoc_id');
        });
    }

    public function down(): void
    {
        Schema::table('cau_hoi', function (Blueprint $table) {
            $table->dropForeign(['bai_hoc_id']);
            $table->dropColumn('bai_hoc_id');
        });
    }
};