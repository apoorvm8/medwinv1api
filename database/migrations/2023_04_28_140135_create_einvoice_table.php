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
        Schema::create('einvoice', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('acctno');
            $table->foreign('acctno')->references('acctno')->on('customer_data')->onDelete('cascade')->onUpdate('cascade');
            $table->boolean('active')->default(0)->nullable();
            $table->date('install_date')->nullable();
            $table->date('next_amc_date')->nullable();
            $table->text('remarks')->nullable();
            $table->string('username', 255)->nullable();
            $table->string('password', 255)->nullable();
            $table->string('ipaddress', 255)->nullable();
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
        Schema::dropIfExists('einvoice');
    }
};
