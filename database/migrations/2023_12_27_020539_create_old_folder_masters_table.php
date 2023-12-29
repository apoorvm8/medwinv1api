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
        Schema::create('old_folder_masters', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admin_id');
            $table->string('name', 255);
            $table->string('parent_name', 255)->nullable();
            $table->integer('parent_id')->default(0)->nullable();
            $table->integer('sub_folders')->default(0)->nullable();
            $table->integer('sub_files')->default(0)->nullable();
            $table->integer('timesDownloaded')->default(0)->nullable();
            $table->string('fileSize')->default(0)->nullable();
            $table->integer('permission')->nullable();
            $table->integer('type')->nullable(); // 0 - hidden, 1 - public, 2 - public with upload access, 3 - upload in allowed folder being used by others, so they can delete as well
            $table->string('path', 255)->nullable();
            $table->integer('depth')->nullable();
            $table->timestamp('lastDownloadedAt')->nullable();
            $table->timestamps();
            $table->timestamp('lastUploadedAt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('old_folder_masters');
    }
};
