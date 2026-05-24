<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\MasterPelanggan;

class PelangganBaruNotification extends Notification
{
    use Queueable;

    public $pelanggan;

    /**
     * Create a new notification instance.
     */
    public function __construct(MasterPelanggan $pelanggan)
    {
        $this->pelanggan = $pelanggan;
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
            'type' => 'pelanggan_baru',
            'title' => 'Pelanggan Baru',
            'message' => 'Pelanggan baru ditambahkan: ' . $this->pelanggan->nama_pelanggan,
            'url' => route('master.pelanggan.index', ['search' => $this->pelanggan->nama_pelanggan]),
        ];
    }
}
