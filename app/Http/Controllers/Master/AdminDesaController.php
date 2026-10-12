<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\MasterAdminDesa;
use App\Support\MasterAccount;
use App\Support\WilayahFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDesaController extends Controller
{
    public function index(Request $request)
    {
        $baseQuery = MasterAdminDesa::query();
        $wilayahOptions = WilayahFilter::buildOptionsFromScopedQuery(clone $baseQuery, false);

        $query = clone $baseQuery;
        WilayahFilter::applyDirectWilayah($query, $request);

        $adminDesa = $query->with('user')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $adminDesa->getCollection()->transform(function ($item) {
            $item->pelanggan_count = $item->pelangganQuery()->count();
            return $item;
        });

        $activeFilterCount = WilayahFilter::countActiveFilters($request, ['kecamatan', 'desa']);

        return view('master.admin-desa.index', array_merge(compact('adminDesa', 'activeFilterCount'), $wilayahOptions));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_admin' => 'required|string|max:150',
            'alamat'     => 'nullable|string',
            'kecamatan'  => 'nullable|string|max:100',
            'desa'       => 'nullable|string|max:100',
            'desa_kode'  => 'required|string|max:13|exists:wilayah,kode',
            'kontak'     => 'nullable|string|max:50',
            'lokasi'     => 'nullable|string',
            'email'      => 'nullable|email|unique:users,email',
            'password'   => 'nullable|string|min:8|confirmed',
        ]);

        DB::transaction(function () use ($request) {
            $admin = MasterAdminDesa::create([
                'nama_admin' => $request->nama_admin,
                'alamat'     => $request->alamat,
                'kecamatan'  => $request->kecamatan,
                'desa'       => $request->desa,
                'desa_kode'  => $request->desa_kode,
                'kontak'     => $request->kontak,
                'lokasi'     => $request->lokasi,
            ]);

            MasterAccount::sync($admin, 'admin_desa', 'admin_desa_id', $request->nama_admin, $request->email, $request->password);
        });

        return redirect()->route('master.admin-desa.index')->with('success', 'Admin Desa berhasil ditambahkan.');
    }

    public function update(Request $request, MasterAdminDesa $adminDesa)
    {
        $request->validate([
            'nama_admin' => 'required|string|max:150',
            'alamat'     => 'nullable|string',
            'kecamatan'  => 'nullable|string|max:100',
            'desa'       => 'nullable|string|max:100',
            'desa_kode'  => 'required|string|max:13|exists:wilayah,kode',
            'kontak'     => 'nullable|string|max:50',
            'lokasi'     => 'nullable|string',
            'email'      => 'nullable|email|unique:users,email,' . optional($adminDesa->user)->id,
            'password'   => 'nullable|string|min:8|confirmed',
        ]);

        DB::transaction(function () use ($request, $adminDesa) {
            $adminDesa->update([
                'nama_admin' => $request->nama_admin,
                'alamat'     => $request->alamat,
                'kecamatan'  => $request->kecamatan,
                'desa'       => $request->desa,
                'desa_kode'  => $request->desa_kode,
                'kontak'     => $request->kontak,
                'lokasi'     => $request->lokasi,
            ]);

            MasterAccount::sync($adminDesa, 'admin_desa', 'admin_desa_id', $request->nama_admin, $request->email, $request->password);
        });

        return redirect()->route('master.admin-desa.index')->with('success', 'Admin Desa berhasil diperbarui.');
    }

    public function destroy(MasterAdminDesa $adminDesa)
    {
        DB::transaction(function () use ($adminDesa) {
            MasterAccount::delete($adminDesa);
            $adminDesa->delete();
        });

        return redirect()->route('master.admin-desa.index')->with('success', 'Admin Desa berhasil dihapus.');
    }
}
