<?php

namespace App\Http\Controllers;

use App\Models\MasterPelanggan;
use App\Models\Perbaikan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PerbaikanController extends Controller
{
    private function isSuperadmin(): bool
    {
        return auth()->user()->hasRole('superadmin');
    }

    private function authorizeOwner(Perbaikan $perbaikan): void
    {
        abort_unless($this->isSuperadmin() || $perbaikan->user_id === auth()->id(), 403);
    }

    private function rules(): array
    {
        return [
            'tanggal_perbaikan' => 'required|date',
            'lokasi'            => ['required', 'string', 'max:100', 'regex:/^-?\d{1,3}(\.\d+)?,\s*-?\d{1,3}(\.\d+)?$/'],
            'alamat'            => 'nullable|string|max:255',
            'deskripsi'         => 'required|string|max:2000',
            'pelanggan_id'      => 'nullable|exists:master_pelanggan,id',
            'foto'              => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ];
    }

    public function index(Request $request)
    {
        $query = Perbaikan::with('user.teknisi', 'pelanggan');

        if (! $this->isSuperadmin()) {
            $query->where('user_id', auth()->id());
        } elseif ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('dari')) {
            $query->whereDate('tanggal_perbaikan', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('tanggal_perbaikan', '<=', $request->sampai);
        }

        $perbaikan = $query->orderByDesc('tanggal_perbaikan')->paginate(20)->withQueryString();
        $teknisiUsers = $this->isSuperadmin()
            ? User::role('teknisi')->orderBy('name')->get(['id', 'name'])
            : collect();

        return view('perbaikan.index', compact('perbaikan', 'teknisiUsers'));
    }

    public function create()
    {
        return view('perbaikan.create', ['pelanggan' => MasterPelanggan::orderBy('nama_pelanggan')->get(['id', 'nama_pelanggan', 'desa'])]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['user_id'] = auth()->id();
        $data['lokasi'] = preg_replace('/\s+/', '', $data['lokasi']);
        unset($data['foto']);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('perbaikan', 'public');
        }

        Perbaikan::create($data);

        return redirect()->route('perbaikan.index')->with('success', 'Laporan perbaikan berhasil disimpan');
    }

    public function show(Perbaikan $perbaikan)
    {
        $this->authorizeOwner($perbaikan);
        $perbaikan->load('user', 'pelanggan');

        return view('perbaikan.show', compact('perbaikan'));
    }

    public function edit(Perbaikan $perbaikan)
    {
        $this->authorizeOwner($perbaikan);
        $pelanggan = MasterPelanggan::orderBy('nama_pelanggan')->get(['id', 'nama_pelanggan', 'desa']);

        return view('perbaikan.edit', compact('perbaikan', 'pelanggan'));
    }

    public function update(Request $request, Perbaikan $perbaikan)
    {
        $this->authorizeOwner($perbaikan);
        $data = $request->validate($this->rules());
        $data['lokasi'] = preg_replace('/\s+/', '', $data['lokasi']);
        unset($data['foto']);

        if ($request->hasFile('foto')) {
            if ($perbaikan->foto) {
                Storage::disk('public')->delete($perbaikan->foto);
            }
            $data['foto'] = $request->file('foto')->store('perbaikan', 'public');
        }

        $perbaikan->update($data);

        return redirect()->route('perbaikan.index')->with('success', 'Laporan perbaikan berhasil diperbarui');
    }

    public function destroy(Perbaikan $perbaikan)
    {
        $this->authorizeOwner($perbaikan);
        if ($perbaikan->foto) {
            Storage::disk('public')->delete($perbaikan->foto);
        }
        $perbaikan->delete();

        return redirect()->route('perbaikan.index')->with('success', 'Laporan perbaikan berhasil dihapus');
    }
}
