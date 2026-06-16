<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\MasterKolektor;
use App\Models\User;
use App\Support\AdminDesaScope;
use App\Support\WilayahFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class KolektorController extends Controller
{
    public function index(Request $request)
    {
        $bulan = now()->month;
        $tahun = now()->year;

        $baseQuery = MasterKolektor::query();

        if (AdminDesaScope::isAdminDesaOnly()) {
            AdminDesaScope::applyWilayahMasterScope($baseQuery);
        }

        $wilayahOptions = WilayahFilter::buildOptionsFromScopedQuery(clone $baseQuery, false);

        $query = clone $baseQuery;
        WilayahFilter::applyDirectWilayah($query, $request);

        $kolektor = $query
            ->with('user')
            ->withCount('pelanggan')
            ->withSum(['tagihan as tagihan_sum_nominal' => function ($q) use ($bulan, $tahun) {
                $q->where('bulan', $bulan)->where('tahun', $tahun);
            }], 'nominal')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $activeFilterCount = WilayahFilter::countActiveFilters($request, ['kecamatan', 'desa']);

        return view('master.kolektor.index', array_merge(compact('kolektor', 'activeFilterCount'), $wilayahOptions));
    }

    public function create()
    {
        return view('master.kolektor.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kolektor' => 'required|string|max:150',
            'alamat'        => 'nullable|string',
            'kecamatan'     => 'nullable|string|max:100',
            'desa'          => 'nullable|string|max:100',
            'kontak'        => 'nullable|string|max:50',
            'lokasi'        => 'nullable|string',
            'email'         => 'nullable|email|unique:users,email',
            'password'      => 'nullable|string|min:6|confirmed',
        ]);

        $data = $request->only([
            'nama_kolektor', 'alamat', 'kecamatan', 'desa', 'kontak', 'lokasi',
        ]);

        if (AdminDesaScope::isAdminDesaOnly()) {
            $data = AdminDesaScope::applyWilayahToData($data);
        }

        DB::transaction(function () use ($data, $request) {
            $kolektor = MasterKolektor::create($data);

            if ($request->filled('email') && $request->filled('password')) {
                $user = User::create([
                    'name'        => $request->nama_kolektor,
                    'email'       => $request->email,
                    'password'    => Hash::make($request->password),
                    'kolektor_id' => $kolektor->id,
                ]);
                $user->assignRole('kolektor');
            }
        });

        return redirect()->route('master.kolektor.index')->with('success', 'Kolektor berhasil ditambahkan.');
    }

    public function edit(MasterKolektor $kolektor)
    {
        AdminDesaScope::authorizeWilayahRecord($kolektor);

        return view('master.kolektor.edit', compact('kolektor'));
    }

    public function update(Request $request, MasterKolektor $kolektor)
    {
        AdminDesaScope::authorizeWilayahRecord($kolektor);

        $request->validate([
            'nama_kolektor' => 'required|string|max:150',
            'alamat'        => 'nullable|string',
            'kecamatan'     => 'nullable|string|max:100',
            'desa'          => 'nullable|string|max:100',
            'kontak'        => 'nullable|string|max:50',
            'lokasi'        => 'nullable|string',
            'email'         => 'nullable|email|unique:users,email,' . optional($kolektor->user)->id,
            'password'      => 'nullable|string|min:6|confirmed',
        ]);

        $data = $request->only([
            'nama_kolektor', 'alamat', 'kecamatan', 'desa', 'kontak', 'lokasi',
        ]);

        if (AdminDesaScope::isAdminDesaOnly()) {
            $data = AdminDesaScope::applyWilayahToData($data);
        }

        DB::transaction(function () use ($request, $kolektor, $data) {
            $kolektor->update($data);

            if ($request->filled('email')) {
                $user = $kolektor->user;

                if ($user) {
                    $updateData = [
                        'name'  => $request->nama_kolektor,
                        'email' => $request->email,
                    ];
                    if ($request->filled('password')) {
                        $updateData['password'] = Hash::make($request->password);
                    }
                    $user->update($updateData);
                } elseif ($request->filled('password')) {
                    $user = User::create([
                        'name'        => $request->nama_kolektor,
                        'email'       => $request->email,
                        'password'    => Hash::make($request->password),
                        'kolektor_id' => $kolektor->id,
                    ]);
                    $user->assignRole('kolektor');
                }
            }
        });

        return redirect()->route('master.kolektor.index')->with('success', 'Kolektor berhasil diperbarui.');
    }

    public function destroy(MasterKolektor $kolektor)
    {
        AdminDesaScope::authorizeWilayahRecord($kolektor);

        DB::transaction(function () use ($kolektor) {
            if ($kolektor->user) {
                $kolektor->user->delete();
            }
            $kolektor->delete();
        });

        return redirect()->route('master.kolektor.index')->with('success', 'Kolektor berhasil dihapus.');
    }
}
