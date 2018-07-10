<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $guard_name = config('auth.defaults.guard');
        // admin specific permissions
        $admin = [
            'user_list',
            'user_show',
            'user_create',
            'user_edit',
            'user_delete',
            'user_verify',
            'permission_list',
            'permission_show',
            'permission_create',
            'permission_edit',
            'permission_delete',
            'role_list',
            'role_show',
            'role_create',
            'role_edit',
            'role_delete',
            'invoice_list',
            'invoice_show',
            'invoice_create_shipping',
            'invoice_create_dispatch',
            'invoice_edit',
            'invoice_delete',
        ];
        // manager specific permissions
        $manager = [
            'invoice_list',
            'invoice_show',
            'invoice_create_dispatch',
            'invoice_edit',
            'invoice_delete',
        ];
        // user specific permissions
        $user = [
            'kyc_upload',
            'invoice_list',
            'invoice_pay',
            'address_list',
            'address_show',
            'address_create',
            'address_edit',
            'address_delete',
        ];

        // create all possible permissions
        $all = array_unique(array_merge($admin, $manager, $user));
        foreach ($all as $p) {
            Permission::create(['name' => $p, 'guard_name' => $guard_name]);
        }

        // assign all permissions to admin role
        Role::findOrFail(1)->givePermissionTo($admin);

        // assign permissions to manager role
        Role::findOrFail(2)->givePermissionTo($manager);

        // assign permissions to user role
        Role::findOrFail(3)->givePermissionTo($user);
    }
}
