<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\MasterBulanan;
use Illuminate\Http\Request;

class BulanController extends Controller
{
    public function index()
    {
        $bulanan = MasterBulanan::latest()->paginate(20);
        return view('master.bulanan.index', compact('bulanan'));
    }

    public function create()
    {
        return view('master.bulanan.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nominal' => 'required|numeric|min:0',
            'terbilang' => 'required|string|max:255',
        ]);

        MasterBulanan::create($data);

        return redirect()->route('master.bulanan.index')->with('success', 'Paket bulanan berhasil ditambahkan');
    }

    public function edit(MasterBulanan $bulanan)
    {
        return view('master.bulanan.edit', compact('bulanan'));
    }

    public function update(Request $request, MasterBulanan $bulanan)
    {
        $data = $request->validate([
            'nominal' => 'required|numeric|min:0',
            'terbilang' => 'required|string|max:255',
        ]);

        $bulanan->update($data);

        return redirect()->route('master.bulanan.index')->with('success', 'Paket bulanan berhasil diperbarui');
    }

    public function destroy(MasterBulanan $bulanan)
    {
        $bulanan->delete();
        return redirect()->route('master.bulanan.index')->with('success', 'Paket bulanan berhasil dihapus');
    }
}
