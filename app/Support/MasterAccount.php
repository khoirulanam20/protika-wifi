<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Creates/updates/deletes the login user tied to a master record
 * (kolektor, admin_desa, teknisi). Role is always chosen by the caller.
 */
class MasterAccount
{
    public static function sync(Model $master, string $role, string $fkColumn, string $name, ?string $email, ?string $password): void
    {
        if (blank($email)) {
            return;
        }

        $user = $master->user;

        if ($user) {
            $attrs = ['name' => $name, 'email' => $email];
            if (filled($password)) {
                $attrs['password'] = $password;
            }
            $user->update($attrs);

            return;
        }

        if (filled($password)) {
            $user = User::create([
                'name'     => $name,
                'email'    => $email,
                'password' => $password,
                $fkColumn  => $master->id,
            ]);
            $user->assignRole($role);
        }
    }

    public static function delete(Model $master): void
    {
        $master->user?->delete();
    }
}
