<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'user@gmail.com'],
            [
                'name' => 'Pembeli',
                'password' => Hash::make('12345'),
                'role' => 'buyer'
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Penjual',
                'password' => Hash::make('12345'),
                'role' => 'seller'
            ]
        );
    }
}
