<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $guard_name = config('auth.defaults.guard');
        $roles = [
            ['name' => 'Admin', 'guard_name' => $guard_name],
            ['name' => 'Manager', 'guard_name' => $guard_name],
            ['name' => 'User', 'guard_name' => $guard_name],
        ];
        $role = Role::insert($roles);
    }
}
