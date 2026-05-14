<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MasterKolektor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles', 'kolektor')->latest()->paginate(20);
        $roles = Role::all();
        $kolektor = MasterKolektor::all();
        return view('master.users.index', compact('users', 'roles', 'kolektor'));
    }

    public function create()
    {
        $roles = Role::all();
        $kolektor = MasterKolektor::all();
        return view('master.users.create', compact('roles', 'kolektor'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'kolektor_id' => 'nullable|exists:master_kolektor,id',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'kolektor_id' => $data['kolektor_id'],
        ]);

        $user->assignRole($data['role']);

        return redirect()->route('master.users.index')->with('success', 'Pengguna berhasil ditambahkan');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $kolektor = MasterKolektor::all();
        return view('master.users.edit', compact('user', 'roles', 'kolektor'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'kolektor_id' => 'nullable|exists:master_kolektor,id',
        ]);

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'kolektor_id' => $data['kolektor_id'],
        ]);

        if ($data['password']) {
            $user->update(['password' => Hash::make($data['password'])]);
        }

        $user->syncRoles($data['role']);

        return redirect()->route('master.users.index')->with('success', 'Pengguna berhasil diperbarui');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus diri sendiri');
        }
        $user->delete();
        return redirect()->route('master.users.index')->with('success', 'Pengguna berhasil dihapus');
    }
}
