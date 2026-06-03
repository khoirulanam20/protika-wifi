<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\MasterKolektor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class KolektorController extends Controller
{
    public function index()
    {
        $bulan = now()->month;
        $tahun = now()->year;

        $kolektor = MasterKolektor::with('user')
            ->withCount('pelanggan')
            ->withSum(['tagihan as tagihan_sum_nominal' => function ($query) use ($bulan, $tahun) {
                $query->where('bulan', $bulan)->where('tahun', $tahun);
            }], 'nominal')
            ->latest()->paginate(20);

        return view('master.kolektor.index', compact('kolektor'));
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
            // Akun login (opsional — hanya jika diisi)
            'email'         => 'nullable|email|unique:users,email',
            'password'      => 'nullable|string|min:6|confirmed',
        ]);

        DB::transaction(function () use ($request) {
            $kolektor = MasterKolektor::create([
                'nama_kolektor' => $request->nama_kolektor,
                'alamat'        => $request->alamat,
                'kecamatan'     => $request->kecamatan,
                'desa'          => $request->desa,
                'kontak'        => $request->kontak,
                'lokasi'        => $request->lokasi,
            ]);

            // Buat user login jika email diisi
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
        return view('master.kolektor.edit', compact('kolektor'));
    }

    public function update(Request $request, MasterKolektor $kolektor)
    {
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

        DB::transaction(function () use ($request, $kolektor) {
            $kolektor->update([
                'nama_kolektor' => $request->nama_kolektor,
                'alamat'        => $request->alamat,
                'kecamatan'     => $request->kecamatan,
                'desa'          => $request->desa,
                'kontak'        => $request->kontak,
                'lokasi'        => $request->lokasi,
            ]);

            if ($request->filled('email')) {
                $user = $kolektor->user;

                if ($user) {
                    // Update akun yang sudah ada
                    $updateData = [
                        'name'  => $request->nama_kolektor,
                        'email' => $request->email,
                    ];
                    if ($request->filled('password')) {
                        $updateData['password'] = Hash::make($request->password);
                    }
                    $user->update($updateData);
                } else {
                    // Buat akun baru jika belum punya
                    if ($request->filled('password')) {
                        $user = User::create([
                            'name'        => $request->nama_kolektor,
                            'email'       => $request->email,
                            'password'    => Hash::make($request->password),
                            'kolektor_id' => $kolektor->id,
                        ]);
                        $user->assignRole('kolektor');
                    }
                }
            }
        });

        return redirect()->route('master.kolektor.index')->with('success', 'Kolektor berhasil diperbarui.');
    }

    public function destroy(MasterKolektor $kolektor)
    {
        DB::transaction(function () use ($kolektor) {
            // Hapus user yang terhubung jika ada
            if ($kolektor->user) {
                $kolektor->user->delete();
            }
            $kolektor->delete();
        });

        return redirect()->route('master.kolektor.index')->with('success', 'Kolektor berhasil dihapus.');
    }
}
