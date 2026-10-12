<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\MasterTeknisi;
use App\Support\AdminDesaScope;
use App\Support\MasterAccount;
use App\Support\WilayahFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

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

        $teknisi = $query->with('user')->latest()->paginate(20)->withQueryString();
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
            'email' => 'nullable|email|unique:users,email',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $account = Arr::only($data, ['email', 'password']);
        $data = Arr::except($data, ['email', 'password']);

        if (AdminDesaScope::isAdminDesaOnly()) {
            $data = AdminDesaScope::applyWilayahToData($data);
        }

        DB::transaction(function () use ($data, $account) {
            $teknisi = MasterTeknisi::create($data);
            MasterAccount::sync($teknisi, 'teknisi', 'teknisi_id', $data['nama_teknisi'], $account['email'] ?? null, $account['password'] ?? null);
        });

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
            'email' => 'nullable|email|unique:users,email,' . optional($teknisi->user)->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $account = Arr::only($data, ['email', 'password']);
        $data = Arr::except($data, ['email', 'password']);

        if (AdminDesaScope::isAdminDesaOnly()) {
            $data = AdminDesaScope::applyWilayahToData($data);
        }

        DB::transaction(function () use ($teknisi, $data, $account) {
            $teknisi->update($data);
            MasterAccount::sync($teknisi, 'teknisi', 'teknisi_id', $data['nama_teknisi'], $account['email'] ?? null, $account['password'] ?? null);
        });

        return redirect()->route('master.teknisi.index')->with('success', 'Teknisi berhasil diperbarui');
    }

    public function destroy(MasterTeknisi $teknisi)
    {
        AdminDesaScope::authorizeWilayahRecord($teknisi);

        DB::transaction(function () use ($teknisi) {
            MasterAccount::delete($teknisi);
            $teknisi->delete();
        });

        return redirect()->route('master.teknisi.index')->with('success', 'Teknisi berhasil dihapus');
    }
}
