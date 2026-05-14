<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tagihan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelanggan_id')->constrained('master_pelanggan')->cascadeOnDelete();
            $table->foreignId('kolektor_id')->nullable()->constrained('master_kolektor')->nullOnDelete();
            $table->tinyInteger('bulan')->unsigned();
            $table->smallInteger('tahun')->unsigned();
            $table->date('tanggal_bayar')->nullable();
            $table->decimal('nominal', 12, 2)->default(0);
            $table->enum('status', ['lunas', 'belum_lunas', 'sebagian'])->default('belum_lunas');
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['pelanggan_id', 'bulan', 'tahun'], 'unique_tagihan_per_bulan');
        });
    }
    public function down(): void {
        Schema::dropIfExists('tagihan');
    }
};
