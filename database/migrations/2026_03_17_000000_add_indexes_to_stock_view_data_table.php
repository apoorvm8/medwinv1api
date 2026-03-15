<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add indexes to improve DataTables filter, sort, and search on 1GB server.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_view_data', function (Blueprint $table) {
            $table->index('Outlet_Id');
            $table->index('Item_Name');
            $table->index('Company_name');
            $table->index('MRP');
            $table->index('BatchQty');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_view_data', function (Blueprint $table) {
            $table->dropIndex(['Outlet_Id']);
            $table->dropIndex(['Item_Name']);
            $table->dropIndex(['Company_name']);
            $table->dropIndex(['MRP']);
            $table->dropIndex(['BatchQty']);
        });
    }
};
