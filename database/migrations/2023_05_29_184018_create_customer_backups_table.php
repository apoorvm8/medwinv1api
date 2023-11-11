<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_backups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('acctno');
            $table->foreign('acctno')->references('acctno')->on('customer_data')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('folder_id');
            $table->foreign('folder_id')->references('id')->on('folder_masters')->onDelete('cascade')->onUpdate('cascade');
            $table->boolean('active')->default(0)->nullable();
            $table->mediumText('number_of_backup')->nullable();
            $table->date('install_date')->nullable();
            $table->date('next_amc_date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_backups');
    }
};
