<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_dusun', function (Blueprint $table) {
            $table->string('desa_kode', 13)->nullable()->after('desa');
            $table->foreign('desa_kode')->references('kode')->on('wilayah')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('master_dusun', function (Blueprint $table) {
            $table->dropForeign(['desa_kode']);
            $table->dropColumn('desa_kode');
        });
    }
};
