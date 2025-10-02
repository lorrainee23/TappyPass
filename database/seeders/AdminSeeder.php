<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@tappypass.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '09123456789',
        ]);

        echo "Admin user created successfully!\n";
        echo "Email: admin@tappypass.com\n";
        echo "Password: password\n";
    }
}
