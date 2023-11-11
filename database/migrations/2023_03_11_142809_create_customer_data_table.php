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
        Schema::create('customer_data', function (Blueprint $table) {
            $table->id('acctno');
            $table->string('subdesc', 100);
            $table->string('subadd1', 100)->nullable();
            $table->string('subadd2', 100)->nullable();
            $table->string('subadd3', 100)->nullable();
            $table->string('subpercn', 50)->nullable();
            $table->string('subphone', 50)->nullable();
            $table->string('gstno', 20)->nullable();
            $table->string('area', 50)->nullable();
            $table->string('state', 50)->nullable();
            $table->string('interstate', 1)->nullable();
            $table->string('importsw', 1)->nullable();
            $table->string('softwaretype', 50)->nullable();
            $table->date('installdate');
            $table->date('nextamcdate');
            $table->decimal('amcamount', 8, 2)->nullable();
            $table->decimal('recvamount', 8, 2)->nullable();
            $table->string('activestatus', 2)->default('Y');
            $table->string('acctcode', 5)->nullable();
            $table->string('sub3code', 30)->nullable();
            $table->string('email', 30)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->text('narration')->nullable();
            $table->string('password')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_data');
    }
};
