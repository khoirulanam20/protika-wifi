<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wilayah', function (Blueprint $table) {
            $table->string('kode', 13)->primary();
            $table->string('nama', 100);
            $table->index('nama');
        });

        // Import data dari file SQL
        $path = database_path('wilayah.sql');
        
        if (file_exists($path)) {
            // Baca file SQL
            $sql = file_get_contents($path);
            
            // Split menjadi statements (berdasarkan INSERT INTO)
            $statements = explode('INSERT INTO wilayah', $sql);
            
            // Skip statement pertama (CREATE TABLE dll)
            array_shift($statements);
            
            // Execute setiap INSERT statement
            foreach ($statements as $statement) {
                if (trim($statement)) {
                    try {
                        DB::unprepared('INSERT INTO wilayah' . $statement);
                    } catch (\Exception $e) {
                        // Lanjutkan jika ada error (mungkin duplicate)
                        continue;
                    }
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('wilayah');
    }
};
