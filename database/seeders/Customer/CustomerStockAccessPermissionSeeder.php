<?php

namespace Database\Seeders\Customer;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class CustomerStockAccessPermissionSeeder extends Seeder
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
        if(!Permission::where('name', 'customer_stockaccess_view')->first()) {
            Permission::create(['name' => 'customer_stockaccess_view', 'guard_name' => 'sanctum', 
            'group_name' => 'customer_stockaccess']);
        }

        if(!Permission::where('name', 'customer_stockaccess_toggle')->first()) {
            Permission::create(['name' => 'customer_stockaccess_toggle', 'guard_name' => 'sanctum', 
            'group_name' => 'customer_stockaccess']);
        }
    }
}
