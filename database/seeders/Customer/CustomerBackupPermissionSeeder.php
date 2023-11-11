<?php

namespace Database\Seeders\Customer;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class CustomerBackupPermissionSeeder extends Seeder
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
        if(!Permission::where('name', 'customer_backup_view')->first()) {
            Permission::create(['name' => 'customer_backup_view', 'guard_name' => 'sanctum', 
            'group_name' => 'customer_backup']);
        }

        if(!Permission::where('name', 'customer_backup_toggle')->first()) {
            Permission::create(['name' => 'customer_backup_toggle', 'guard_name' => 'sanctum', 
            'group_name' => 'customer_backup']);
        }

        if(!Permission::where('name', 'customer_backup_password')->first()) {
            Permission::create(['name' => 'customer_backup_password', 'guard_name' => 'sanctum', 
            'group_name' => 'customer_backup']);
        }
    }
}
