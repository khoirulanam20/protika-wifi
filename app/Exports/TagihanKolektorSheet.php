<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TagihanKolektorSheet implements FromArray, WithStyles, WithTitle
{
    public function __construct(
        private string $kolektorNama,
        private int $bulan,
        private int $tahun,
        private Collection $tagihan,
    ) {}

    public function array(): array
    {
        $monthName = Carbon::createFromDate($this->tahun, $this->bulan, 1)
            ->locale('id')
            ->translatedFormat('F');

        $rows = [
            ['', strtoupper($this->kolektorNama), '', $monthName],
            [],
            ['NO', 'NAMA', 'NML', 'WIL', 'STATUS', 'KET'],
        ];

        $no = 1;
        foreach ($this->tagihan as $item) {
            $pelanggan = $item->pelanggan;
            $wil = $pelanggan?->dusun?->dusun ?? $pelanggan?->desa ?? '';
            $nml = (int) round($item->nominal / 1000);
            $status = match ($item->status) {
                'lunas' => 'Lunas',
                'belum_lunas' => 'Belum Lunas',
                'sebagian' => 'Sebagian',
                default => $item->status,
            };

            $rows[] = [
                $no++,
                $pelanggan?->nama_pelanggan ?? '—',
                $nml,
                $wil,
                $status,
                $item->keterangan ?? '',
            ];
        }

        return $rows;
    }

    public function title(): string
    {
        return Str::limit($this->kolektorNama, 31, '');
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = max(3, $sheet->getHighestRow());
        $range = "A3:F{$lastRow}";

        $sheet->getStyle('A3:F3')->getFont()->setBold(true);
        $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('A4:A'.$lastRow)->getAlignment()->setHorizontal('center');

        return [];
    }
}
