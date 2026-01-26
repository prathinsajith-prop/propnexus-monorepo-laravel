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
        Schema::table('blogs', function (Blueprint $table) {
            // Archivable trait columns
            $table->timestamp('archived_at')->nullable()->index()->after('deleted_at');
            $table->string('archived_by')->nullable()->after('archived_at');
            $table->text('archived_reason')->nullable()->after('archived_by');

            // Sortable trait columns
            $table->unsignedInteger('position')->default(0)->index()->after('id');

            // Metable support (optional, or use separate meta table)
            // Using JSON column for metadata
            $table->json('meta_data')->nullable()->after('custom_fields');

            // Versionable tracking (additional columns)
            $table->unsignedInteger('version_count')->default(0)->after('revision_number');
            $table->string('version_created_by')->nullable()->after('version_count');

            // Export/Import tracking
            $table->timestamp('last_exported_at')->nullable()->after('analytics');
            $table->timestamp('last_imported_at')->nullable()->after('last_exported_at');

            // Performance measurement
            $table->json('performance_metrics')->nullable()->after('last_imported_at');

            // Cache warming indicators
            $table->timestamp('cache_warmed_at')->nullable()->after('performance_metrics');
        });

        // Create blog_meta table for Metable trait
        Schema::create('blog_meta', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('blog_id')->index();
            $table->string('meta_key')->index();
            $table->text('meta_value')->nullable();
            $table->string('meta_type')->default('string'); // string, integer, boolean, array, object
            $table->timestamps();

            $table->foreign('blog_id')
                ->references('id')
                ->on('blogs')
                ->onDelete('cascade');

            $table->unique(['blog_id', 'meta_key']);
        });

        // Create blog_versions table for Versionable trait
        Schema::create('blog_versions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('blog_id')->index();
            $table->unsignedInteger('version_number');
            $table->json('version_data'); // Complete snapshot of blog data
            $table->string('version_reason')->nullable();
            $table->string('version_created_by')->nullable();
            $table->json('changes_summary')->nullable(); // What changed
            $table->timestamps();

            $table->foreign('blog_id')
                ->references('id')
                ->on('blogs')
                ->onDelete('cascade');

            $table->unique(['blog_id', 'version_number']);
            $table->index(['blog_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_versions');
        Schema::dropIfExists('blog_meta');

        Schema::table('blogs', function (Blueprint $table) {
            $table->dropColumn([
                'archived_at',
                'archived_by',
                'archived_reason',
                'position',
                'meta_data',
                'version_count',
                'version_created_by',
                'last_exported_at',
                'last_imported_at',
                'performance_metrics',
                'cache_warmed_at',
            ]);
        });
    }
};
