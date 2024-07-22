<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Create an admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'), // Ensure you hash the password
            'c_password' => Hash::make('password'), // Ensure you hash the password
            'role' => 'admin',
        ]);

        // Create a student user
        User::create([
            'name' => 'Student User',
            'email' => 'student@gmail.com',
            'password' => Hash::make('password'), // Ensure you hash the password
            'c_password' => Hash::make('password'), // Ensure you hash the password
            'role' => 'student',
        ]);
        // Create a staff user
        User::create([
            'name' => 'staff',
            'email' => 'staff@gmail.com',
            'password' => Hash::make('password'), // Ensure you hash the password
            'c_password' => Hash::make('password'), // Ensure you hash the password
            'role' => 'staff',
        ]);
    }
}
