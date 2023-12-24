<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Database\Seeders\Customer\CustomerAmcDuePermissionSeeder;
use Database\Seeders\Customer\CustomerBackupPermissionSeeder;
use Database\Seeders\Customer\CustomerEinvoicePermissionSeeder;
use Database\Seeders\Customer\CustomerMasterPermissionSeeder;
use Database\Seeders\Customer\CustomerRegisterPermissionSeeder;
use Database\Seeders\Customer\CustomerStockAccessPermissionSeeder;
use Database\Seeders\Customer\CustomerWhatsappPermissionSeeder;
use Database\Seeders\Customer\FolderPermissionSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
            CustomerMasterPermissionSeeder::class,
            CustomerEinvoicePermissionSeeder::class,
            CustomerRegisterPermissionSeeder::class,
            CustomerBackupPermissionSeeder::class,
            CustomerStockAccessPermissionSeeder::class,
            FolderPermissionSeeder::class,
            CustomerWhatsappPermissionSeeder::class,
            CustomerAmcDuePermissionSeeder::class,
        ]);
    }
}
