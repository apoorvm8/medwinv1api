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
        Schema::create('folder_masters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('name', 255);
            $table->string('slug', 255)->unique()->index('folder_slug');
            $table->mediumText('children_count')->nullable(); // {'folder': 4, 'file': 10 } // this will allow to us to diversify this more
            $table->integer('depth')->nullable();
            $table->text('path')->nullable();
            $table->string('file_size', 100)->default('0')->nullable();
            $table->string('times_downloaded', 100)->default('0')->nullable();
            $table->enum('resource_type', ['folder', 'file']);
            $table->enum('resource_module', ['all', 'software', 'backup', 'stock']);
            $table->timestamp('last_downloaded_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('parent_id')->references('id')->on('folder_masters')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('folder_masters');
    }
};
