<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('master_pelanggan', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('status_alat');
            $table->timestamp('nonaktif_at')->nullable()->after('is_active');
            $table->timestamp('aktif_kembali_at')->nullable()->after('nonaktif_at');
        });
    }

    public function down(): void
    {
        Schema::table('master_pelanggan', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'nonaktif_at', 'aktif_kembali_at']);
        });
    }
};
