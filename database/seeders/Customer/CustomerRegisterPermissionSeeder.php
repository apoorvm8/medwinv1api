<?php

namespace Database\Seeders\Customer;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class CustomerRegisterPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        if(!Permission::where('name', 'customer_register_view')->first()) {
            Permission::create(['name' => 'customer_register_view', 'guard_name' => 'sanctum', 
            'group_name' => 'customer_register']);
        }

        if(!Permission::where('name', 'customer_register_delete')->first()) {
            Permission::create(['name' => 'customer_register_delete', 'guard_name' => 'sanctum', 
            'group_name' => 'customer_register']);
        }
    }
}
