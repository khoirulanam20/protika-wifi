<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PerbaikanExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(private Collection $rows) {}

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return ['No', 'Tanggal', 'Petugas', 'Pelanggan', 'Alamat', 'Koordinat', 'Link Maps', 'Deskripsi', 'Peralatan'];
    }

    public function map($p): array
    {
        static $no = 0;

        return [
            ++$no,
            $p->tanggal_perbaikan->format('d-m-Y H:i'),
            $p->user->petugasNama(),
            $p->pelanggan?->nama_pelanggan ?? '-',
            $p->alamat ?: '-',
            $p->lokasi,
            $p->maps_url,
            $p->deskripsi,
            $p->peralatan ?: '-',
        ];
    }
}
