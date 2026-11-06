<?php

namespace App\Console\Commands;

use App\Models\Perbaikan;
use Illuminate\Console\Command;

class IsiAlamatPerbaikan extends Command
{
    protected $signature = 'perbaikan:isi-alamat';
    protected $description = 'Isi alamat perbaikan yang kosong dari tag lokasinya (1 request/detik, batas Nominatim)';

    public function handle(): void
    {
        Perbaikan::whereNull('alamat')->orWhere('alamat', '')->each(function (Perbaikan $p) {
            if ($alamat = Perbaikan::alamatDariLokasi($p->lokasi)) {
                $p->update(['alamat' => $alamat]);
                $this->line("#{$p->id} {$alamat}");
            }
            sleep(1);
        });
    }
}
