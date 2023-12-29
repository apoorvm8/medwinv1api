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
        Schema::create('customer_msgs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('mobile_no', 10);
            $table->string('email')->unique()->nullable();
            $table->string('type_of_soft');
            $table->boolean('seen');
            $table->string('seen_by')->nullable();
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
        Schema::dropIfExists('customer_msgs');
    }
};
