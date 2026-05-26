<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wilayah;
use Illuminate\Http\Request;

class WilayahController extends Controller
{
    /**
     * Get all provinces
     */
    public function provinces()
    {
        $provinces = Wilayah::getProvinsi();
        return response()->json($provinces->map(function ($p) {
            return ['id' => $p->kode, 'name' => $p->nama];
        }));
    }

    /**
     * Get regencies by province
     */
    public function regencies($provinceId)
    {
        $regencies = Wilayah::getKabupaten($provinceId);
        return response()->json($regencies->map(function ($r) {
            return ['id' => $r->kode, 'name' => $r->nama];
        }));
    }

    /**
     * Get districts by regency
     */
    public function districts($regencyId)
    {
        $districts = Wilayah::getKecamatan($regencyId);
        return response()->json($districts->map(function ($d) {
            return ['id' => $d->kode, 'name' => $d->nama];
        }));
    }

    /**
     * Get villages by district
     */
    public function villages($districtId)
    {
        $villages = Wilayah::getDesa($districtId);
        return response()->json($villages->map(function ($v) {
            return ['id' => $v->kode, 'name' => $v->nama];
        }));
    }
}
