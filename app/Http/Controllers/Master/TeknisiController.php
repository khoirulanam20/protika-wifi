<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\MasterTeknisi;
use App\Support\AdminDesaScope;
use App\Support\WilayahFilter;
use Illuminate\Http\Request;

class TeknisiController extends Controller
{
    public function index(Request $request)
    {
        $baseQuery = MasterTeknisi::query();

        if (AdminDesaScope::isAdminDesaOnly()) {
            AdminDesaScope::applyWilayahMasterScope($baseQuery);
        }

        $wilayahOptions = WilayahFilter::buildOptionsFromScopedQuery(clone $baseQuery, false);

        $query = clone $baseQuery;
        WilayahFilter::applyDirectWilayah($query, $request);

        $teknisi = $query->latest()->paginate(20)->withQueryString();
        $activeFilterCount = WilayahFilter::countActiveFilters($request, ['kecamatan', 'desa']);

        return view('master.teknisi.index', array_merge(compact('teknisi', 'activeFilterCount'), $wilayahOptions));
    }

    public function create()
    {
        return view('master.teknisi.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_teknisi' => 'required|string|max:150',
            'alamat' => 'nullable|string',
            'kecamatan' => 'nullable|string|max:100',
            'desa' => 'nullable|string|max:100',
            'kontak' => 'nullable|string|max:50',
            'lokasi' => 'nullable|string',
        ]);

        if (AdminDesaScope::isAdminDesaOnly()) {
            $data = AdminDesaScope::applyWilayahToData($data);
        }

        MasterTeknisi::create($data);

        return redirect()->route('master.teknisi.index')->with('success', 'Teknisi berhasil ditambahkan');
    }

    public function edit(MasterTeknisi $teknisi)
    {
        AdminDesaScope::authorizeWilayahRecord($teknisi);

        return view('master.teknisi.edit', compact('teknisi'));
    }

    public function update(Request $request, MasterTeknisi $teknisi)
    {
        AdminDesaScope::authorizeWilayahRecord($teknisi);

        $data = $request->validate([
            'nama_teknisi' => 'required|string|max:150',
            'alamat' => 'nullable|string',
            'kecamatan' => 'nullable|string|max:100',
            'desa' => 'nullable|string|max:100',
            'kontak' => 'nullable|string|max:50',
            'lokasi' => 'nullable|string',
        ]);

        if (AdminDesaScope::isAdminDesaOnly()) {
            $data = AdminDesaScope::applyWilayahToData($data);
        }

        $teknisi->update($data);

        return redirect()->route('master.teknisi.index')->with('success', 'Teknisi berhasil diperbarui');
    }

    public function destroy(MasterTeknisi $teknisi)
    {
        AdminDesaScope::authorizeWilayahRecord($teknisi);

        $teknisi->delete();
        return redirect()->route('master.teknisi.index')->with('success', 'Teknisi berhasil dihapus');
    }
}
