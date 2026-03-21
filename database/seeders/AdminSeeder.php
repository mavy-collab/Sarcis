<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gestion.com'],
            [
                'name'     => 'Administrateur',
                'email'    => 'admin@gestion.com',
                'password' => Hash::make('Admin@1234'),
                'is_admin' => true,
            ]
        );
    }
}
