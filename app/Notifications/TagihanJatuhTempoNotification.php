<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Tagihan;

class TagihanJatuhTempoNotification extends Notification
{
    use Queueable;

    public $tagihan;

    /**
     * Create a new notification instance.
     */
    public function __construct(Tagihan $tagihan)
    {
        $this->tagihan = $tagihan;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'tagihan_jatuh_tempo',
            'title' => 'Tagihan Jatuh Tempo',
            'message' => 'Tagihan bulan ' . $this->tagihan->bulan . '/' . $this->tagihan->tahun . ' untuk ' . $this->tagihan->pelanggan->nama_pelanggan . ' telah jatuh tempo.',
            'kolektor' => $this->tagihan->kolektor?->nama_kolektor,
            'url' => route('tagihan.index', ['search' => $this->tagihan->pelanggan->nama_pelanggan, 'status' => 'belum_lunas']),
        ];
    }
}
