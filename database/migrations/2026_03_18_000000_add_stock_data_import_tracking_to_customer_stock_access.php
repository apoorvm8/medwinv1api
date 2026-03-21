<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * S3 object Last-Modified for the stock CSV used to decide if a re-import is needed.
     */
    public function up(): void
    {
        Schema::table('customer_stock_access', function (Blueprint $table) {
            $table->timestamp('last_modified_at')->nullable()->after('remarks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_stock_access', function (Blueprint $table) {
            $table->dropColumn('last_modified_at');
        });
    }
};
