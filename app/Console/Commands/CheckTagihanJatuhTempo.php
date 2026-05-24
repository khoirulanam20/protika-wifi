<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tagihan;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TagihanJatuhTempoNotification;
use Carbon\Carbon;

class CheckTagihanJatuhTempo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-tagihan-jatuh-tempo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check tagihan yang jatuh tempo hari ini dan kirim notifikasi';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();
        $dayOfMonth = $today->day;

        // Cari tagihan yang belum lunas
        $tagihanList = Tagihan::with('pelanggan')
            ->whereIn('status', ['belum_lunas', 'sebagian'])
            ->get();

        $count = 0;
        foreach ($tagihanList as $tagihan) {
            $pelanggan = $tagihan->pelanggan;
            if (!$pelanggan || !$pelanggan->tanggal_pemasangan) continue;

            $tglPasang = Carbon::parse($pelanggan->tanggal_pemasangan);
            
            // Jatuh tempo jika hari ini >= tanggal pemasangan (di bulan yang sama/berbeda)
            // Sebagai simplifikasi, kita cek jika hari ini sama dengan tanggal pemasangan
            if ($dayOfMonth == $tglPasang->day) {
                // Dispatch notifikasi
                $superadmins = User::role('superadmin')->get();
                Notification::send($superadmins, new TagihanJatuhTempoNotification($tagihan));

                if ($tagihan->kolektor_id) {
                    $kolektors = User::where('kolektor_id', $tagihan->kolektor_id)->get();
                    $kolektorsToNotify = $kolektors->reject(fn($user) => $superadmins->contains('id', $user->id));
                    if ($kolektorsToNotify->count() > 0) {
                        Notification::send($kolektorsToNotify, new TagihanJatuhTempoNotification($tagihan));
                    }
                }
                $count++;
            }
        }

        $this->info("Berhasil mengirim $count notifikasi jatuh tempo.");
    }
}
