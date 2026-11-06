<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('perbaikan', function (Blueprint $table) {
            $table->timestamp('dibayar_at')->nullable()->after('foto');
        });
    }

    public function down(): void
    {
        Schema::table('perbaikan', function (Blueprint $table) {
            $table->dropColumn('dibayar_at');
        });
    }
};
