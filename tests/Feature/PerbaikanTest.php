<?php

namespace Tests\Feature;

use App\Models\Perbaikan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PerbaikanTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role): User
    {
        Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        $u = User::factory()->create();
        $u->assignRole($role);
        return $u;
    }

    public function test_teknisi_can_store_with_photo_and_only_sees_own(): void
    {
        Storage::fake('public');
        $a = $this->user('teknisi');
        $b = $this->user('teknisi');

        $this->actingAs($a)->post(route('perbaikan.store'), [
            'tanggal_perbaikan' => now()->format('Y-m-d\TH:i'),
            'lokasi' => '-7.3195, 110.1770',
            'deskripsi' => 'Ganti kabel',
            'foto' => UploadedFile::fake()->image('x.jpg'),
        ])->assertRedirect(route('perbaikan.index'));

        $log = Perbaikan::firstOrFail();
        $this->assertSame($a->id, $log->user_id);
        Storage::disk('public')->assertExists($log->foto);

        $this->actingAs($b)->get(route('perbaikan.show', $log))->assertForbidden();
        $this->actingAs($b)->get(route('perbaikan.index'))->assertOk()->assertDontSee('Ganti kabel');
    }

    public function test_teknisi_blocked_from_dashboard_and_kolektor_from_perbaikan(): void
    {
        $this->actingAs($this->user('teknisi'))->get('/dashboard')->assertForbidden();
        $this->actingAs($this->user('kolektor'))->get(route('perbaikan.index'))->assertForbidden();
    }

    public function test_superadmin_links_teknisi_master_to_user(): void
    {
        Role::firstOrCreate(['name' => 'teknisi', 'guard_name' => 'web']);
        $tek = \App\Models\MasterTeknisi::create(['nama_teknisi' => 'Budi', 'alamat' => '-', 'kecamatan' => '-', 'desa' => '-']);

        $this->actingAs($this->user('superadmin'))->post(route('master.users.store'), [
            'name' => 'Budi', 'email' => 'budi@x.id', 'password' => 'password123', 'password_confirmation' => 'password123',
            'role' => 'teknisi', 'teknisi_id' => $tek->id,
        ])->assertSessionHasNoErrors()->assertRedirect(route("master.users.index"));

        $this->assertSame($tek->id, User::where('email', 'budi@x.id')->first()->teknisi_id);
    }

    public function test_create_form_renders_searchable_pelanggan_picker(): void
    {
        $this->actingAs($this->user('teknisi'))->get(route('perbaikan.create'))
            ->assertOk()
            ->assertSee('Cari nama pelanggan atau desa...', false);
    }
}
