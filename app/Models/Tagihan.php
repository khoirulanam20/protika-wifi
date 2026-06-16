<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Tagihan extends Model
{
    protected $table = 'tagihan';
    protected $fillable = [
        'pelanggan_id', 'kolektor_id', 'bulan', 'tahun', 'tanggal_bayar',
        'nominal', 'terbayar', 'status', 'keterangan', 'created_by'
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
    ];

    public function pelanggan() { return $this->belongsTo(MasterPelanggan::class, 'pelanggan_id'); }
    public function kolektor()  { return $this->belongsTo(MasterKolektor::class, 'kolektor_id'); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }

    public function getSisaTagihanAttribute(): float
    {
        return max(0, $this->nominal - $this->terbayar);
    }

    /**
     * Tanggal jatuh tempo = tanggal pemasangan pada bulan tagihan ini.
     * Contoh: pasang tgl 10, tagihan Mei 2026 → jatuh tempo 10 Mei 2026
     */
    public function getTanggalJatuhTempoAttribute(): ?Carbon
    {
        $pelanggan = $this->pelanggan;
        if (!$pelanggan || !$pelanggan->tanggal_pemasangan) {
            return null;
        }

        $tanggalPasang = Carbon::parse($pelanggan->tanggal_pemasangan);
        $maxDay = Carbon::createFromDate($this->tahun, $this->bulan, 1)->daysInMonth;
        $hari   = min($tanggalPasang->day, $maxDay);

        return Carbon::createFromDate($this->tahun, $this->bulan, $hari);
    }

    /**
     * Berapa hari lagi jatuh tempo. Negatif = sudah lewat.
     */
    public function getSisaHariAttribute(): ?int
    {
        $jt = $this->tanggal_jatuh_tempo;
        if (!$jt) return null;
        return (int) Carbon::today()->diffInDays($jt, false);
    }

    /**
     * Apakah tagihan ini merupakan tunggakan (bulan lampau, belum lunas).
     */
    public function getIsTunggakanAttribute(): bool
    {
        $now = Carbon::now();
        if ($this->status === 'lunas') return false;
        if ($this->tahun < $now->year) return true;
        if ($this->tahun == $now->year && $this->bulan < $now->month) return true;
        return false;
    }

    public function getIsPelunasanPrevAttribute(): bool
    {
        return (bool) ($this->attributes['is_pelunasan_prev'] ?? false);
    }

    public function getStatusDisplayAttribute(): string
    {
        if ($this->is_pelunasan_prev) {
            return 'pelunasan_bulan_sebelumnya';
        }

        return $this->status;
    }

    public function getStatusDisplayLabelAttribute(): string
    {
        return match ($this->status_display) {
            'lunas' => 'Lunas',
            'belum_lunas' => 'Belum Lunas',
            'sebagian' => 'Sebagian',
            'pelunasan_bulan_sebelumnya' => 'Pelunasan bulan sebelumnya',
            default => $this->status,
        };
    }
}
