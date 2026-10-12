<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'kolektor_id',
        'admin_desa_id',
        'teknisi_id',
        'penagih_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function kolektor()
    {
        return $this->belongsTo(MasterKolektor::class, 'kolektor_id');
    }

    public function teknisi()
    {
        return $this->belongsTo(MasterTeknisi::class, 'teknisi_id');
    }

    public function penagih()
    {
        return $this->belongsTo(MasterPenagih::class, 'penagih_id');
    }

    /** Field-worker display name: linked master name, else account name. */
    public function petugasNama(): string
    {
        return $this->teknisi?->nama_teknisi ?? $this->penagih?->nama_penagih ?? $this->name;
    }

    public function adminDesa()
    {
        return $this->belongsTo(MasterAdminDesa::class, 'admin_desa_id');
    }
}
