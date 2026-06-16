<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\MasterDusun;
use App\Support\AdminDesaScope;
use App\Support\WilayahFilter;
use Illuminate\Http\Request;

class DusunController extends Controller
{
    public function index(Request $request)
    {
        $baseQuery = MasterDusun::query();

        if (AdminDesaScope::isAdminDesaOnly()) {
            AdminDesaScope::applyDusunScope($baseQuery);
        }

        $wilayahOptions = WilayahFilter::buildOptionsFromScopedQuery(clone $baseQuery, true);

        $query = clone $baseQuery;
        WilayahFilter::applyDirectWilayah($query, $request);

        $dusun = $query->latest()->paginate(20)->withQueryString();
        $activeFilterCount = WilayahFilter::countActiveFilters($request, ['kecamatan', 'desa', 'dusun_id']);

        return view('master.dusun.index', array_merge(compact('dusun', 'activeFilterCount'), $wilayahOptions));
    }

    public function create()
    {
        return view('master.dusun.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kecamatan' => 'required|string|max:100',
            'desa' => 'required|string|max:100',
            'desa_kode' => 'nullable|string|max:13|exists:wilayah,kode',
            'dusun' => 'required|string|max:100',
        ]);

        if (AdminDesaScope::isAdminDesaOnly()) {
            $data['desa_kode'] = AdminDesaScope::desaKode();
            $labels = AdminDesaScope::wilayahLabels();
            $data['kecamatan'] = $labels['kecamatan'] ?? $data['kecamatan'];
            $data['desa'] = $labels['desa'] ?? $data['desa'];
        }

        MasterDusun::create($data);

        return redirect()->route('master.dusun.index')->with('success', 'Dusun berhasil ditambahkan');
    }

    public function edit(MasterDusun $dusun)
    {
        $this->authorizeDusunAccess($dusun);

        return view('master.dusun.edit', compact('dusun'));
    }

    public function update(Request $request, MasterDusun $dusun)
    {
        $this->authorizeDusunAccess($dusun);

        $data = $request->validate([
            'kecamatan' => 'required|string|max:100',
            'desa' => 'required|string|max:100',
            'desa_kode' => 'nullable|string|max:13|exists:wilayah,kode',
            'dusun' => 'required|string|max:100',
        ]);

        if (AdminDesaScope::isAdminDesaOnly()) {
            $data['desa_kode'] = AdminDesaScope::desaKode();
            $labels = AdminDesaScope::wilayahLabels();
            $data['kecamatan'] = $labels['kecamatan'] ?? $data['kecamatan'];
            $data['desa'] = $labels['desa'] ?? $data['desa'];
        }

        $dusun->update($data);

        return redirect()->route('master.dusun.index')->with('success', 'Dusun berhasil diperbarui');
    }

    public function destroy(MasterDusun $dusun)
    {
        $this->authorizeDusunAccess($dusun);

        $dusun->delete();

        return redirect()->route('master.dusun.index')->with('success', 'Dusun berhasil dihapus');
    }

    private function authorizeDusunAccess(MasterDusun $dusun): void
    {
        if (AdminDesaScope::isAdminDesaOnly() && $dusun->desa_kode !== AdminDesaScope::desaKode()) {
            abort(403, 'Anda tidak memiliki akses ke data dusun ini.');
        }
    }
}
