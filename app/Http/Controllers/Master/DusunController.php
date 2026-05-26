<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\MasterDusun;
use Illuminate\Http\Request;

class DusunController extends Controller
{
    public function index()
    {
        $dusun = MasterDusun::latest()->paginate(20);
        return view('master.dusun.index', compact('dusun'));
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

        MasterDusun::create($data);

        return redirect()->route('master.dusun.index')->with('success', 'Dusun berhasil ditambahkan');
    }

    public function edit(MasterDusun $dusun)
    {
        return view('master.dusun.edit', compact('dusun'));
    }

    public function update(Request $request, MasterDusun $dusun)
    {
        $data = $request->validate([
            'kecamatan' => 'required|string|max:100',
            'desa' => 'required|string|max:100',
            'desa_kode' => 'nullable|string|max:13|exists:wilayah,kode',
            'dusun' => 'required|string|max:100',
        ]);

        $dusun->update($data);

        return redirect()->route('master.dusun.index')->with('success', 'Dusun berhasil diperbarui');
    }

    public function destroy(MasterDusun $dusun)
    {
        $dusun->delete();
        return redirect()->route('master.dusun.index')->with('success', 'Dusun berhasil dihapus');
    }
}
