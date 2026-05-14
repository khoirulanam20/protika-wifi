@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page-title', 'Manajemen Pengguna')
@section('page-subtitle', 'Kelola akun login dan hak akses sistem')

@section('content')

    <div x-data="{ 
        showModal: {{ $errors->any() ? 'true' : 'false' }}, 
        mode: 'create',
        formAction: '{{ route('master.users.store') }}',
        formMethod: 'POST',
        formData: { name: '{{ old('name') }}', email: '{{ old('email') }}', role: '{{ old('role') }}', kolektor_id: '{{ old('kolektor_id') }}' },

        openCreate() {
            this.mode = 'create';
            this.formAction = '{{ route('master.users.store') }}';
            this.formMethod = 'POST';
            this.formData = { name: '', email: '', role: '', kolektor_id: '' };
            this.showModal = true;
        },
        openEdit(item, roleName) {
            this.mode = 'edit';
            this.formAction = '/master/users/' + item.id;
            this.formMethod = 'PUT';
            this.formData = { 
                name: item.name, 
                email: item.email,
                role: roleName,
                kolektor_id: item.kolektor_id || ''
            };
            this.showModal = true;
        }
    }">

        <div class="card overflow-hidden">
            <div class="px-6 py-5 border-b border-border flex items-center justify-between">
                <h2 class="text-content-primary font-semibold text-lg">Daftar Akun</h2>
                <button @click="openCreate()" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah User
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-border">
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-content-tertiary uppercase tracking-wider">
                                Nama</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-content-tertiary uppercase tracking-wider">
                                Email</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-content-tertiary uppercase tracking-wider">
                                Role</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-content-tertiary uppercase tracking-wider">
                                Kolektor</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-content-tertiary uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($users as $user)
                            <tr class="hover:bg-base-page transition-colors">
                                <td class="px-6 py-4 text-sm text-content-primary font-medium">{{ $user->name }}</td>
                                <td class="px-6 py-4 text-sm text-content-secondary">{{ $user->email }}</td>
                                <td class="px-6 py-4 text-sm">
                                    @foreach($user->roles as $role)
                                        <span
                                            class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-primary-light text-primary-deep">
                                            {{ $role->name }}
                                        </span>
                                    @endforeach
                                </td>
                                <td class="px-6 py-4 text-sm text-content-secondary">
                                    {{ $user->kolektor?->nama_kolektor ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <button @click="openEdit({{ $user->toJson() }}, '{{ $user->getRoleNames()->first() }}')"
                                            class="p-1.5 rounded-lg bg-amber-500/10 text-amber-500 hover:bg-amber-500/20 transition-colors"
                                            title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <form method="POST" action="{{ route('master.users.destroy', $user) }}"
                                            onsubmit="return confirm('Hapus pengguna ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="p-1.5 rounded-lg bg-status-danger/10 text-status-danger hover:bg-status-danger/20 transition-colors"
                                                title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-content-tertiary">Belum ada user.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="px-6 py-4 border-t border-border">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

        {{-- Modal Pop-up --}}
        <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center" style="display: none;">
            <div x-show="showModal" x-transition.opacity class="absolute inset-0 bg-content-primary/40 backdrop-blur-sm"
                @click="showModal = false"></div>

            <div x-show="showModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                class="relative bg-white rounded-xl shadow-modal w-full max-w-2xl mx-4 z-10 overflow-hidden">

                <div class="px-6 py-4 border-b border-border flex justify-between items-center bg-base-page">
                    <h3 class="text-lg font-semibold text-content-primary"
                        x-text="mode === 'create' ? 'Tambah Pengguna Baru' : 'Edit Data Pengguna'"></h3>
                    <button @click="showModal = false"
                        class="text-content-tertiary hover:text-content-primary transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form :action="formAction" method="POST">
                    @csrf
                    <input type="hidden" name="_method" :value="formMethod">

                    <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                        <div>
                            <label class="block text-content-secondary text-sm mb-2">Nama Lengkap</label>
                            <input type="text" name="name" x-model="formData.name" class="input-field" required>
                        </div>
                        <div>
                            <label class="block text-content-secondary text-sm mb-2">Email</label>
                            <input type="email" name="email" x-model="formData.email" class="input-field" required>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-content-secondary text-sm mb-2">Password <span
                                        x-show="mode === 'edit'"
                                        class="text-xs text-content-tertiary">(Opsional)</span></label>
                                <input type="password" name="password" class="input-field" :required="mode === 'create'">
                            </div>
                            <div>
                                <label class="block text-content-secondary text-sm mb-2">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" class="input-field"
                                    :required="mode === 'create'">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-content-secondary text-sm mb-2">Role Sistem</label>
                                <select name="role" x-model="formData.role" class="input-field" required>
                                    <option value="">Pilih Role</option>
                                    @foreach($roles as $r)
                                        <option value="{{ $r->name }}">{{ ucfirst($r->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-content-secondary text-sm mb-2">Mapping Kolektor (Opsional)</label>
                                <select name="kolektor_id" x-model="formData.kolektor_id" class="input-field">
                                    <option value="">Tidak ada mapping</option>
                                    @foreach($kolektor as $k)
                                        <option value="{{ $k->id }}">{{ $k->nama_kolektor }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 border-t border-border flex justify-end gap-3 bg-base-page">
                        <button type="button" @click="showModal = false" class="btn-secondary">Batal</button>
                        <button type="submit" class="btn-primary"
                            x-text="mode === 'create' ? 'Simpan Pengguna' : 'Update Pengguna'"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection