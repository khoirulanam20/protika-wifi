<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class TagihanDaftarExport implements WithMultipleSheets
{
    public function __construct(
        private int $bulan,
        private int $tahun,
        private Collection $tagihan,
    ) {}

    public function sheets(): array
    {
        $grouped = $this->tagihan->groupBy('kolektor_id')->sortKeys();

        $sheets = [];
        foreach ($grouped as $items) {
            $nama = $items->first()->kolektor?->nama_kolektor ?? 'Tanpa Kolektor';
            $sorted = $items->sortBy([
                fn ($t) => mb_strtolower($t->pelanggan?->dusun?->dusun ?? $t->pelanggan?->desa ?? ''),
                fn ($t) => mb_strtolower($t->pelanggan?->nama_pelanggan ?? ''),
            ])->values();

            $sheets[] = new TagihanKolektorSheet($nama, $this->bulan, $this->tahun, $sorted);
        }

        if ($sheets === []) {
            $sheets[] = new TagihanKolektorSheet('—', $this->bulan, $this->tahun, collect());
        }

        return $sheets;
    }
}
