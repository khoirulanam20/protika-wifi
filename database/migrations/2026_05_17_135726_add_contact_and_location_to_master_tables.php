<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('master_pelanggan', function (Blueprint $table) {
            $table->string('kontak', 50)->nullable()->after('desa');
            $table->text('lokasi')->nullable()->after('kontak');
        });

        Schema::table('master_kolektor', function (Blueprint $table) {
            $table->string('kontak', 50)->nullable()->after('desa');
            $table->text('lokasi')->nullable()->after('kontak');
        });

        Schema::table('master_teknisi', function (Blueprint $table) {
            $table->string('kontak', 50)->nullable()->after('desa');
            $table->text('lokasi')->nullable()->after('kontak');
        });
    }

    public function down(): void {
        Schema::table('master_pelanggan', function (Blueprint $table) {
            $table->dropColumn(['kontak', 'lokasi']);
        });

        Schema::table('master_kolektor', function (Blueprint $table) {
            $table->dropColumn(['kontak', 'lokasi']);
        });

        Schema::table('master_teknisi', function (Blueprint $table) {
            $table->dropColumn(['kontak', 'lokasi']);
        });
    }
};
