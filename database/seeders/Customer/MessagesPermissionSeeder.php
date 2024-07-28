<?php

namespace Database\Seeders\Customer;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class MessagesPermissionSeeder extends Seeder
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
        if(!Permission::where('name', 'messages_view')->first()) {
            Permission::create(['name' => 'messages_view', 'guard_name' => 'sanctum', 
            'group_name' => 'message']);
        }

        // create permissions
        if(!Permission::where('name', 'messages_update')->first()) {
            Permission::create(['name' => 'messages_update', 'guard_name' => 'sanctum', 
            'group_name' => 'message']);
        }
    }
}
