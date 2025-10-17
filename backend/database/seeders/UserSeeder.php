<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Cria um usuário para o Dashboard Administrativo
        User::updateOrCreate(
            ['email' => 'medico@clinic.com'],
            [
                'name' => 'Dr(a). Administrador(a)',
                'password' => Hash::make('123456'), // Senha: senha123
                'email_verified_at' => now(),
            ]
        );

        // Se quiser um segundo usuário:
        // User::updateOrCreate(
        //     ['email' => 'auxiliar@clinic.com'],
        //     [
        //         'name' => 'Auxiliar da Clínica',
        //         'password' => Hash::make('auxiliar123'),
        //         'email_verified_at' => now(),
        //     ]
        // );
    }
}
