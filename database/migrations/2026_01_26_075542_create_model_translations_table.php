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
        Schema::create('model_translations', function (Blueprint $table) {
            $table->id();
            $table->morphs('translatable'); // translatable_type and translatable_id
            $table->string('locale', 10)->index();
            $table->string('key'); // The field name being translated (title, content, etc.)
            $table->text('value')->nullable(); // The translated value
            $table->timestamps();

            // Composite unique index to prevent duplicate translations
            $table->unique(['translatable_type', 'translatable_id', 'locale', 'key'], 'translations_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_translations');
    }
};
