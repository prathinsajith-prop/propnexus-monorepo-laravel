<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bixo_product_properties', function (Blueprint $table) {
            $table->unsignedSmallInteger('beds')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bixo_product_properties', function (Blueprint $table) {
            $table->string('beds', 50)->nullable()->index()->change();
        });
    }
};
