<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\MasterAdminDesa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminDesaController extends Controller
{
    public function index()
    {
        $adminDesa = MasterAdminDesa::with('user')
            ->latest()
            ->paginate(20);

        $adminDesa->getCollection()->transform(function ($item) {
            $item->pelanggan_count = $item->pelangganQuery()->count();
            return $item;
        });

        return view('master.admin-desa.index', compact('adminDesa'));
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
            'password'   => 'nullable|string|min:6|confirmed',
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

            if ($request->filled('email') && $request->filled('password')) {
                $user = User::create([
                    'name'          => $request->nama_admin,
                    'email'         => $request->email,
                    'password'      => Hash::make($request->password),
                    'admin_desa_id' => $admin->id,
                ]);
                $user->assignRole('admin_desa');
            }
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
            'password'   => 'nullable|string|min:6|confirmed',
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

            if ($request->filled('email')) {
                $user = $adminDesa->user;

                if ($user) {
                    $updateData = [
                        'name'  => $request->nama_admin,
                        'email' => $request->email,
                    ];
                    if ($request->filled('password')) {
                        $updateData['password'] = Hash::make($request->password);
                    }
                    $user->update($updateData);
                } elseif ($request->filled('password')) {
                    $user = User::create([
                        'name'          => $request->nama_admin,
                        'email'         => $request->email,
                        'password'      => Hash::make($request->password),
                        'admin_desa_id' => $adminDesa->id,
                    ]);
                    $user->assignRole('admin_desa');
                }
            }
        });

        return redirect()->route('master.admin-desa.index')->with('success', 'Admin Desa berhasil diperbarui.');
    }

    public function destroy(MasterAdminDesa $adminDesa)
    {
        DB::transaction(function () use ($adminDesa) {
            if ($adminDesa->user) {
                $adminDesa->user->delete();
            }
            $adminDesa->delete();
        });

        return redirect()->route('master.admin-desa.index')->with('success', 'Admin Desa berhasil dihapus.');
    }
}
