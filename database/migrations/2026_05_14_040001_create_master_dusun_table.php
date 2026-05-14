<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('master_dusun', function (Blueprint $table) {
            $table->id();
            $table->string('kecamatan', 100);
            $table->string('desa', 100);
            $table->string('dusun', 100);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('master_dusun');
    }
};
