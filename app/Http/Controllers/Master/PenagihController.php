<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\MasterPenagih;
use App\Support\AdminDesaScope;
use App\Support\MasterAccount;
use App\Support\WilayahFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

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

        $penagih = $query->with('user')->latest()->paginate(20)->withQueryString();
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
            'email' => 'nullable|email|unique:users,email',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $account = Arr::only($data, ['email', 'password']);
        $data = Arr::except($data, ['email', 'password']);

        if (AdminDesaScope::isAdminDesaOnly()) {
            $data = AdminDesaScope::applyWilayahToData($data);
        }

        DB::transaction(function () use ($data, $account) {
            $penagih = MasterPenagih::create($data);
            MasterAccount::sync($penagih, 'penagih', 'penagih_id', $data['nama_penagih'], $account['email'] ?? null, $account['password'] ?? null);
        });

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
            'email' => 'nullable|email|unique:users,email,' . optional($penagih->user)->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $account = Arr::only($data, ['email', 'password']);
        $data = Arr::except($data, ['email', 'password']);

        if (AdminDesaScope::isAdminDesaOnly()) {
            $data = AdminDesaScope::applyWilayahToData($data);
        }

        DB::transaction(function () use ($penagih, $data, $account) {
            $penagih->update($data);
            MasterAccount::sync($penagih, 'penagih', 'penagih_id', $data['nama_penagih'], $account['email'] ?? null, $account['password'] ?? null);
        });

        return redirect()->route('master.penagih.index')->with('success', 'Penagih berhasil diperbarui');
    }

    public function destroy(MasterPenagih $penagih)
    {
        AdminDesaScope::authorizeWilayahRecord($penagih);

        DB::transaction(function () use ($penagih) {
            MasterAccount::delete($penagih);
            $penagih->delete();
        });

        return redirect()->route('master.penagih.index')->with('success', 'Penagih berhasil dihapus');
    }
}
