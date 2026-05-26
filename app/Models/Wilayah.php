<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wilayah extends Model
{
    protected $table = 'wilayah';
    protected $primaryKey = 'kode';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['kode', 'nama'];

    /**
     * Get provinsi list
     */
    public static function getProvinsi()
    {
        return self::whereRaw('LENGTH(kode) = 2')->orderBy('kode')->get();
    }

    /**
     * Get kabupaten/kota by provinsi
     */
    public static function getKabupaten($provinsiKode)
    {
        return self::whereRaw('LENGTH(kode) = 5')
            ->where('kode', 'like', $provinsiKode . '.%')
            ->orderBy('kode')
            ->get();
    }

    /**
     * Get kecamatan by kabupaten
     */
    public static function getKecamatan($kabupatenKode)
    {
        return self::whereRaw('LENGTH(kode) = 8')
            ->where('kode', 'like', $kabupatenKode . '.%')
            ->orderBy('kode')
            ->get();
    }

    /**
     * Get desa/kelurahan by kecamatan
     */
    public static function getDesa($kecamatanKode)
    {
        return self::whereRaw('LENGTH(kode) = 13')
            ->where('kode', 'like', $kecamatanKode . '.%')
            ->orderBy('kode')
            ->get();
    }
}
