<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('master_pelanggan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pelanggan', 150);
            $table->string('kecamatan', 100)->nullable();
            $table->string('desa', 100)->nullable();
            $table->foreignId('dusun_id')->nullable()->constrained('master_dusun')->nullOnDelete();
            $table->foreignId('bulanan_id')->nullable()->constrained('master_bulanan')->nullOnDelete();
            $table->date('tanggal_pemasangan')->nullable();
            $table->foreignId('kolektor_id')->nullable()->constrained('master_kolektor')->nullOnDelete();
            $table->foreignId('teknisi_id')->nullable()->constrained('master_teknisi')->nullOnDelete();
            $table->foreignId('penagih_id')->nullable()->constrained('master_penagih')->nullOnDelete();
            $table->enum('status_alat', ['beli', 'pinjam'])->default('beli');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('master_pelanggan');
    }
};
