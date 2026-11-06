<?php

namespace App\Console\Commands;

use App\Models\Perbaikan;
use Illuminate\Console\Command;

class IsiAlamatPerbaikan extends Command
{
    protected $signature = 'perbaikan:isi-alamat {--semua : timpa juga alamat yang sudah terisi}';
    protected $description = 'Isi alamat perbaikan yang kosong dari tag lokasinya (1 request/detik, batas Nominatim)';

    public function handle(): void
    {
        ($this->option('semua') ? Perbaikan::query() : Perbaikan::whereNull('alamat')->orWhere('alamat', ''))->each(function (Perbaikan $p) {
            if ($alamat = Perbaikan::alamatDariLokasi($p->lokasi)) {
                $p->update(['alamat' => $alamat]);
                $this->line("#{$p->id} {$alamat}");
            }
            sleep(1);
        });
    }
}
