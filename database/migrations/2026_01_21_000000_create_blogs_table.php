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
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();

            // Core identifiers
            $table->string('blog_id', 50)->unique()->index();
            $table->string('title');
            $table->string('slug')->unique()->index();

            // Content
            $table->text('excerpt')->nullable();
            $table->longText('content');

            // Publication
            $table->enum('status', ['draft', 'review', 'published', 'archived', 'trash'])->default('draft')->index();
            $table->enum('visibility', ['public', 'private', 'password'])->default('public');
            $table->string('password')->nullable();

            // Authorship
            $table->unsignedBigInteger('author_id')->index();
            $table->json('co_authors')->nullable();

            // Categorization
            $table->string('category')->nullable()->index();
            $table->json('categories')->nullable();
            $table->json('tags')->nullable();

            // Media
            $table->string('featured_image')->nullable();
            $table->json('gallery')->nullable();
            $table->string('video_url')->nullable();
            $table->json('attachments')->nullable();

            // Localization
            $table->string('language', 10)->default('en')->index();
            $table->json('translations')->nullable();

            // SEO
            $table->json('seo_meta')->nullable();
            $table->json('schema_markup')->nullable();

            // Flags
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_sticky')->default(false)->index();
            $table->boolean('allow_comments')->default(true);

            // Metrics
            $table->unsignedInteger('comments_count')->default(0);
            $table->unsignedBigInteger('views_count')->default(0)->index();
            $table->unsignedInteger('likes_count')->default(0);
            $table->unsignedInteger('shares_count')->default(0);
            $table->decimal('reading_time', 8, 1)->default(0);

            // Relationships
            $table->json('related_posts')->nullable();

            // Scheduling
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamp('scheduled_at')->nullable()->index();
            $table->timestamp('expired_at')->nullable()->index();

            // Versioning
            $table->timestamp('last_edited_at')->nullable();
            $table->unsignedBigInteger('last_edited_by')->nullable();
            $table->unsignedInteger('revision_number')->default(1);

            // Custom
            $table->json('custom_fields')->nullable();
            $table->json('analytics')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['status', 'published_at']);
            $table->index(['author_id', 'status']);
            $table->index(['category', 'status']);
            $table->index(['is_featured', 'status']);

            // Fulltext index only for MySQL/PostgreSQL
            if (config('database.default') !== 'sqlite') {
                $table->fullText(['title', 'excerpt', 'content']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
