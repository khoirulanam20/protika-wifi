<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\MasterPenagih;
use App\Support\AdminDesaScope;
use App\Support\WilayahFilter;
use Illuminate\Http\Request;

class PenagihController extends Controller
{
    public function index(Request $request)
    {
        $baseQuery = MasterPenagih::query();

        if (AdminDesaScope::isAdminDesaOnly()) {
            AdminDesaScope::applyWilayahMasterScope($baseQuery);
        }

        $wilayahOptions = WilayahFilter::buildOptionsFromScopedQuery(clone $baseQuery, false);

        $query = clone $baseQuery;
        WilayahFilter::applyDirectWilayah($query, $request);

        $penagih = $query->latest()->paginate(20)->withQueryString();
        $activeFilterCount = WilayahFilter::countActiveFilters($request, ['kecamatan', 'desa']);

        return view('master.penagih.index', array_merge(compact('penagih', 'activeFilterCount'), $wilayahOptions));
    }

    public function create()
    {
        return view('master.penagih.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_penagih' => 'required|string|max:150',
            'alamat' => 'nullable|string',
            'kecamatan' => 'nullable|string|max:100',
            'desa' => 'nullable|string|max:100',
        ]);

        if (AdminDesaScope::isAdminDesaOnly()) {
            $data = AdminDesaScope::applyWilayahToData($data);
        }

        MasterPenagih::create($data);

        return redirect()->route('master.penagih.index')->with('success', 'Penagih berhasil ditambahkan');
    }

    public function edit(MasterPenagih $penagih)
    {
        AdminDesaScope::authorizeWilayahRecord($penagih);

        return view('master.penagih.edit', compact('penagih'));
    }

    public function update(Request $request, MasterPenagih $penagih)
    {
        AdminDesaScope::authorizeWilayahRecord($penagih);

        $data = $request->validate([
            'nama_penagih' => 'required|string|max:150',
            'alamat' => 'nullable|string',
            'kecamatan' => 'nullable|string|max:100',
            'desa' => 'nullable|string|max:100',
        ]);

        if (AdminDesaScope::isAdminDesaOnly()) {
            $data = AdminDesaScope::applyWilayahToData($data);
        }

        $penagih->update($data);

        return redirect()->route('master.penagih.index')->with('success', 'Penagih berhasil diperbarui');
    }

    public function destroy(MasterPenagih $penagih)
    {
        AdminDesaScope::authorizeWilayahRecord($penagih);

        $penagih->delete();

        return redirect()->route('master.penagih.index')->with('success', 'Penagih berhasil dihapus');
    }
}
