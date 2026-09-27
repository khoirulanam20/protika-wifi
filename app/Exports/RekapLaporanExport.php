<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RekapLaporanExport implements FromArray, WithStyles
{
    public function __construct(private Collection $items) {}

    public function array(): array
    {
        $rows = [[
            'Pelanggan',
            'Desa',
            'Dusun',
            'Kolektor',
            'Periode Tagihan',
            'Tanggal Bayar',
            'Nominal',
            'Status',
            'Keterangan',
        ]];

        foreach ($this->items as $item) {
            $pelanggan = $item->pelanggan;
            $rows[] = [
                $pelanggan?->nama_pelanggan ?? '—',
                $pelanggan?->desa ?? '',
                $pelanggan?->dusun?->dusun ?? '',
                $item->kolektor?->nama_kolektor ?? '',
                $item->bulan.'/'.$item->tahun,
                $item->tanggal_bayar?->format('d/m/Y') ?? '',
                (float) $item->nominal,
                $item->status_display_label,
                $item->keterangan ?? '',
            ];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = max(1, $sheet->getHighestRow());
        $lastCol = 'I';
        $range = "A1:{$lastCol}{$lastRow}";

        $sheet->getStyle("A1:{$lastCol}1")->getFont()->setBold(true);
        $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        return [];
    }
}
