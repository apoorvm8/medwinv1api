<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Data-dump table: delete all then bulk insert on each import. No primary key.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_view_data', function (Blueprint $table) {
            $table->string('Division', 255)->nullable();
            $table->integer('Outlet_Id')->nullable();
            $table->string('Outlet_Name', 255)->nullable();
            $table->string('Address1', 255)->nullable();
            $table->string('Address2', 255)->nullable();
            $table->string('Company_name', 255)->nullable();
            $table->integer('Item_code')->nullable();
            $table->string('Item_Name', 255)->nullable();
            $table->string('PackDesc', 255)->nullable();
            $table->decimal('PackSize', 15, 2)->nullable();
            $table->decimal('MRP', 15, 2)->nullable();
            $table->decimal('SaleScm1', 15, 2)->nullable();
            $table->decimal('Salescm2', 15, 2)->nullable();
            $table->integer('BatchQty')->nullable();
            $table->decimal('GSTPER', 5, 2)->nullable();
            $table->date('Dateofsending')->nullable();
            $table->string('Timeofsending', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_view_data');
    }
};
