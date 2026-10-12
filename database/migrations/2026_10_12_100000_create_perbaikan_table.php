<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('perbaikan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('pelanggan_id')->nullable()->constrained('master_pelanggan')->nullOnDelete();
            $table->dateTime('tanggal_perbaikan');
            $table->string('lokasi');
            $table->string('alamat')->nullable();
            $table->text('deskripsi');
            $table->string('foto')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'tanggal_perbaikan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perbaikan');
    }
};
