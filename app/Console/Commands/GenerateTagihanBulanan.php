<?php

namespace App\Console\Commands;

use App\Models\MasterPelanggan;
use App\Models\Tagihan;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateTagihanBulanan extends Command
{
    protected $signature = 'app:generate-tagihan-bulanan';

    protected $description = 'Generate tagihan bulan berjalan untuk semua pelanggan aktif';

    public function handle(): int
    {
        $now = Carbon::now();
        $bulan = $now->month;
        $tahun = $now->year;
        $count = 0;

        $pelangganList = MasterPelanggan::with('bulanan')
            ->whereNotNull('kolektor_id')
            ->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('aktif_kembali_at')
                    ->orWhereDate('aktif_kembali_at', '<=', $now->toDateString());
            })
            ->get();

        foreach ($pelangganList as $pelanggan) {
            $tagihan = Tagihan::firstOrCreate(
                ['pelanggan_id' => $pelanggan->id, 'bulan' => $bulan, 'tahun' => $tahun],
                [
                    'kolektor_id' => $pelanggan->kolektor_id,
                    'nominal'     => $pelanggan->bulanan?->nominal ?? 0,
                    'status'      => 'belum_lunas',
                    'created_by'  => null,
                ]
            );

            if ($tagihan->wasRecentlyCreated) {
                $count++;
            }
        }

        $this->info("Berhasil membuat {$count} tagihan baru untuk {$bulan}/{$tahun}.");

        return self::SUCCESS;
    }
}
