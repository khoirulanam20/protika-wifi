<?php

namespace Tests\Feature;

use App\Models\MasterKolektor;
use App\Models\MasterTeknisi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MasterAccountTest extends TestCase
{
    use RefreshDatabase;

    private function superadmin(): User
    {
        foreach (['superadmin', 'kolektor', 'teknisi', 'penagih', 'admin_desa'] as $r) {
            Role::firstOrCreate(['name' => $r, 'guard_name' => 'web']);
        }
        $u = User::factory()->create();
        $u->assignRole('superadmin');

        return $u;
    }

    private function account(string $email = 'a@x.id'): array
    {
        return ['email' => $email, 'password' => 'password123', 'password_confirmation' => 'password123'];
    }

    public function test_teknisi_created_with_account_in_one_request(): void
    {
        $this->actingAs($this->superadmin())->post(route('master.teknisi.store'), [
            'nama_teknisi' => 'Budi', 'role' => 'superadmin', 'teknisi_id' => 999,
        ] + $this->account())->assertSessionHasNoErrors();

        $t = MasterTeknisi::firstOrFail();
        $u = User::where('email', 'a@x.id')->firstOrFail();
        $this->assertSame($t->id, $u->teknisi_id);
        $this->assertTrue($u->hasRole('teknisi'));
        $this->assertFalse($u->hasRole('superadmin'));
    }

    public function test_kolektor_created_with_account(): void
    {
        $this->actingAs($this->superadmin())->post(route('master.kolektor.store'), ['nama_kolektor' => 'Ani'] + $this->account())
            ->assertSessionHasNoErrors();

        $u = User::where('email', 'a@x.id')->firstOrFail();
        $this->assertSame(MasterKolektor::first()->id, $u->kolektor_id);
        $this->assertTrue($u->hasRole('kolektor'));
    }

    public function test_master_without_account_creates_master_only(): void
    {
        $admin = $this->superadmin();
        $this->actingAs($admin)->post(route('master.teknisi.store'), ['nama_teknisi' => 'Tanpa Akun'])->assertSessionHasNoErrors();

        $this->assertSame(1, MasterTeknisi::count());
        $this->assertSame(1, User::count());
    }

    public function test_duplicate_email_creates_nothing(): void
    {
        $admin = $this->superadmin();
        $this->actingAs($admin)->post(route('master.teknisi.store'), ['nama_teknisi' => 'X'] + $this->account($admin->email))
            ->assertSessionHasErrors('email');

        $this->assertSame(0, MasterTeknisi::count());
    }

    public function test_update_adds_account_then_destroy_removes_it(): void
    {
        $this->actingAs($admin = $this->superadmin());
        $t = MasterTeknisi::create(['nama_teknisi' => 'Budi']);

        $this->put(route('master.teknisi.update', $t), ['nama_teknisi' => 'Budi'] + $this->account())->assertSessionHasNoErrors();
        $this->assertSame($t->id, User::where('email', 'a@x.id')->value('teknisi_id'));

        $this->delete(route('master.teknisi.destroy', $t));
        $this->assertDatabaseMissing('users', ['email' => 'a@x.id']);
        $this->assertSame(0, MasterTeknisi::count());
    }

    public function test_penagih_created_with_account_and_lands_on_perbaikan(): void
    {
        $this->actingAs($this->superadmin())->post(route('master.penagih.store'), ['nama_penagih' => 'Sari'] + $this->account('sari@x.id'))
            ->assertSessionHasNoErrors();

        $u = User::where('email', 'sari@x.id')->firstOrFail();
        $this->assertSame(\App\Models\MasterPenagih::first()->id, $u->penagih_id);
        $this->assertTrue($u->hasRole('penagih'));
        $this->assertSame('Sari', $u->petugasNama());

        auth()->logout();
        $this->post('/login', ['email' => 'sari@x.id', 'password' => 'password123'])->assertRedirect(route('perbaikan.index', absolute: false));
        $this->get(route('perbaikan.index'))->assertOk();
        $this->get('/dashboard')->assertForbidden();
    }
}
