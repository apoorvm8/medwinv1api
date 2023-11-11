<?php

namespace Database\Seeders\Customer;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CustomerMasterPermissionSeeder extends Seeder
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
        if(!Permission::where('name', 'customer_master_view')->first()) {
            Permission::create(['name' => 'customer_master_view', 'guard_name' => 'sanctum', 
            'group_name' => 'customer_master']);
        }

        if(!Permission::where('name', 'customer_master_lock')->first()) {
            Permission::create(['name' => 'customer_master_lock', 'guard_name' => 'sanctum', 
            'group_name' => 'customer_master']);
        }
        
        if(!Permission::where('name', 'customer_master_einvoice')->first()) {
            Permission::create(['name' => 'customer_master_einvoice', 'guard_name' => 'sanctum', 
            'group_name' => 'customer_master']);
        }

    }
}
