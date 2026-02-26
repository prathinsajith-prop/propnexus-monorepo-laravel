<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bixo_ndocs_notes', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid')->nullable();
            $table->text('note')->nullable();
            $table->text('attachments')->nullable();
            $table->integer('subject_id')->nullable()->index();
            $table->string('subject_type', 150)->nullable()->index();
            $table->string('type', 50)->nullable();
            $table->integer('user_id')->nullable();
            $table->string('user_type', 100)->default('');
            $table->string('temp_subject_type', 100)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bixo_ndocs_notes');
    }
};
