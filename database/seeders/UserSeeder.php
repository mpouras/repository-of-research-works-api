<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds for default admin user.
     */
    public function run(): void
    {
        User::create([
            'first_name' => 'admin',
            'last_name' => 'admin',
            'username' => 'admin',
            'email' => 'johndoe@localhost',
            'password' => Hash::make('admin1234'),
            'role' => 'admin',
        ]);

        User::create([
            'first_name' => 'scraper',
            'last_name' => 'scraper',
            'username' => 'scraper',
            'email' => 'scraper@localhost',
            'password' => Hash::make('scraper123456'),
            'role' => 'admin'
        ]);
    }
}
