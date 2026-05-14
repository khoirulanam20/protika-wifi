<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('master_teknisi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_teknisi', 150);
            $table->text('alamat')->nullable();
            $table->string('kecamatan', 100)->nullable();
            $table->string('desa', 100)->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('master_teknisi');
    }
};
