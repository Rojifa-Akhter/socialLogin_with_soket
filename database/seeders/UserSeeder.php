<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::insert([
            [
                'full_name' => 'admin',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('12345678'),
                'role' => 'ADMIN',
            ],
            [
                'full_name' => 'bata',
                'email' => 'bata_unique@gmail.com', // Ensure unique email
                'password' => bcrypt('12345678'),
                'role' => 'CUSTOMER',
            ],
            [
                'full_name' => 'apex',
                'email' => 'apex_unique@gmail.com', // Ensure unique email
                'password' => bcrypt('12345678'),
                'role' => 'CUSTOMER',
            ],
            [
                'full_name' => 'user',
                'email' => 'user@gmail.com',
                'password' => bcrypt('12345678'),
                'role' => 'USER',
            ],
            [
                'full_name' => 'user1',
                'email' => 'user1@gmail.com',
                'password' => bcrypt('12345678'),
                'role' => 'USER',
            ],
            [
                'full_name' => 'user2',
                'email' => 'user2@gmail.com',
                'password' => bcrypt('12345678'),
                'role' => 'USER',
            ],
            [
                'full_name' => 'user3',
                'email' => 'user3@gmail.com',
                'password' => bcrypt('12345678'),
                'role' => 'USER',
            ],
        ]);
    }
}
