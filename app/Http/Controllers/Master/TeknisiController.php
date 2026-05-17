<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\MasterTeknisi;
use Illuminate\Http\Request;

class TeknisiController extends Controller
{
    public function index()
    {
        $teknisi = MasterTeknisi::latest()->paginate(20);
        return view('master.teknisi.index', compact('teknisi'));
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

        MasterTeknisi::create($data);

        return redirect()->route('master.teknisi.index')->with('success', 'Teknisi berhasil ditambahkan');
    }

    public function edit(MasterTeknisi $teknisi)
    {
        return view('master.teknisi.edit', compact('teknisi'));
    }

    public function update(Request $request, MasterTeknisi $teknisi)
    {
        $data = $request->validate([
            'nama_teknisi' => 'required|string|max:150',
            'alamat' => 'nullable|string',
            'kecamatan' => 'nullable|string|max:100',
            'desa' => 'nullable|string|max:100',
            'kontak' => 'nullable|string|max:50',
            'lokasi' => 'nullable|string',
        ]);

        $teknisi->update($data);

        return redirect()->route('master.teknisi.index')->with('success', 'Teknisi berhasil diperbarui');
    }

    public function destroy(MasterTeknisi $teknisi)
    {
        $teknisi->delete();
        return redirect()->route('master.teknisi.index')->with('success', 'Teknisi berhasil dihapus');
    }
}
