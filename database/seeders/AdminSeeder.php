<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Crea un usuario administrador para el Report Studio.
     * Email: admin@zippiacloset.com | Password: Admin123!
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@zippiacloset.com'],
            [
                'name' => 'Administrador',
                'apellido' => 'Zippia',
                'telefono' => '0000000000',
                'password' => Hash::make('Admin123!'),
            ]
        );

        if (!$admin->hasRole('ADMIN')) {
            $admin->assignRole('ADMIN');
        }
    }
}
