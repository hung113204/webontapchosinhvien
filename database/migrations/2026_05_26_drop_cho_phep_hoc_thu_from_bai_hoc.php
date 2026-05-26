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
            $table->dropColumn('cho_phep_hoc_thu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bai_hoc', function (Blueprint $table) {
            $table->boolean('cho_phep_hoc_thu')->default(false)->after('thu_tu');
        });
    }
};
