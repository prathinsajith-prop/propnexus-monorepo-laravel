<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bixo_product_description', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type', 200)->nullable();
            $table->string('category', 200)->nullable();
            $table->string('language', 100)->nullable();
            $table->unsignedBigInteger('portal_id')->default(0);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_type', 50)->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('deleted_at')->nullable();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('bixo_product_description');
    }
};
