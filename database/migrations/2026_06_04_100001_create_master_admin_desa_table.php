<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('master_admin_desa', function (Blueprint $table) {
            $table->id();
            $table->string('nama_admin', 150);
            $table->text('alamat')->nullable();
            $table->string('kecamatan', 100)->nullable();
            $table->string('desa', 100)->nullable();
            $table->string('desa_kode', 13);
            $table->string('kontak', 50)->nullable();
            $table->string('lokasi')->nullable();
            $table->timestamps();

            $table->foreign('desa_kode')->references('kode')->on('wilayah')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_admin_desa');
    }
};
