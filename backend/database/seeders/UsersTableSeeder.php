<?php

namespace Database\Seeders;

use App\Models\Kyc;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'first_name' => 'Admin',
            'last_name' => 'A',
            'email' => 'admin@afro.com',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('Admin');
        $user = User::create([
            'first_name' => 'Manager',
            'last_name' => 'A',
            'email' => 'manager@afro.com',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('Manager');
        $user = User::create([
            'first_name' => 'User',
            'last_name' => 'A',
            'uid' => 'User00001',
            'email' => 'user@afro.com',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('User');

        Kyc::create(['user_id' => 1]);
        Kyc::create(['user_id' => 2]);
        Kyc::create(['user_id' => 3]);
    }
}
