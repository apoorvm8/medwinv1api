<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Stores comma-separated acctnos of linked outlets (manual entry, max ~10).
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_data', function (Blueprint $table) {
            $table->string('linked_outlet_ids', 255)->nullable()->after('password');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_data', function (Blueprint $table) {
            $table->dropColumn('linked_outlet_ids');
        });
    }
};
