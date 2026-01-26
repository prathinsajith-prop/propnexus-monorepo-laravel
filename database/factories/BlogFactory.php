<?php

namespace Database\Factories;

use App\Models\Blog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Blog>
 */
class BlogFactory extends Factory
{
    protected $model = Blog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(rand(4, 8));
        $slug = \Illuminate\Support\Str::slug($title);
        $categories = ['Technology', 'Business', 'Lifestyle', 'Education', 'Health', 'Travel', 'Food', 'Science', 'Entertainment', 'Sports'];
        $tags = ['Tutorial', 'Guide', 'News', 'Opinion', 'Review', 'Comparison', 'Tips', 'How-to', 'Best Practices', 'Case Study'];
        $statuses = ['draft', 'review', 'published', 'archived'];
        $languages = ['en', 'es', 'fr', 'de'];

        return [
            'blog_id' => 'BLOG-' . strtoupper(uniqid()),
            'title' => $title,
            'slug' => $slug,
            'excerpt' => fake()->paragraph(2),
            'content' => $this->generateContent(),
            'status' => fake()->randomElement($statuses),
            'visibility' => fake()->randomElement(['public', 'private']),
            'author_id' => rand(1, 4),
            'co_authors' => fake()->boolean(30) ? [rand(1, 4), rand(1, 4)] : [],
            'category' => fake()->randomElement($categories),
            'categories' => fake()->randomElements($categories, rand(1, 3)),
            'tags' => fake()->randomElements($tags, rand(2, 5)),
            'featured_image' => '/images/blogs/' . fake()->slug() . '.jpg',
            'gallery' => fake()->boolean(40) ? [
                '/images/gallery/' . fake()->slug() . '.jpg',
                '/images/gallery/' . fake()->slug() . '.jpg',
            ] : [],
            'video_url' => fake()->boolean(20) ? 'https://youtube.com/watch?v=' . fake()->bothify('???########') : null,
            'attachments' => fake()->boolean(30) ? [
                ['name' => 'document.pdf', 'url' => '/files/' . fake()->slug() . '.pdf'],
            ] : [],
            'language' => fake()->randomElement($languages),
            'translations' => [],
            'seo_meta' => [
                'title' => $title,
                'description' => fake()->sentence(15),
                'keywords' => fake()->words(5),
                'canonical_url' => fake()->url(),
            ],
            'schema_markup' => [
                '@context' => 'https://schema.org',
                '@type' => 'BlogPosting',
                'headline' => $title,
            ],
            'is_featured' => fake()->boolean(20),
            'is_sticky' => fake()->boolean(10),
            'allow_comments' => fake()->boolean(80),
            'comments_count' => rand(0, 50),
            'views_count' => rand(100, 5000),
            'likes_count' => rand(10, 500),
            'shares_count' => rand(5, 200),
            'related_posts' => [],
            'published_at' => fake()->boolean(70) ? fake()->dateTimeBetween('-6 months', 'now') : null,
            'scheduled_at' => fake()->boolean(10) ? fake()->dateTimeBetween('now', '+1 month') : null,
            'expired_at' => null,
            'custom_fields' => [],
            'analytics' => [
                'bounce_rate' => rand(20, 80),
                'avg_time_on_page' => rand(30, 300),
            ],
        ];
    }

    /**
     * Generate rich HTML content
     */
    private function generateContent(): string
    {
        $paragraphs = [];
        $numParagraphs = rand(5, 10);

        for ($i = 0; $i < $numParagraphs; $i++) {
            $paragraphs[] = '<p>' . fake()->paragraph(rand(3, 6)) . '</p>';

            // Randomly add a heading
            if (rand(0, 3) == 0) {
                $paragraphs[] = '<h2>' . fake()->sentence(rand(3, 5)) . '</h2>';
            }

            // Randomly add a list
            if (rand(0, 4) == 0) {
                $listItems = '';
                for ($j = 0; $j < rand(3, 5); $j++) {
                    $listItems .= '<li>' . fake()->sentence() . '</li>';
                }
                $paragraphs[] = '<ul>' . $listItems . '</ul>';
            }
        }

        return implode("\n", $paragraphs);
    }

    /**
     * Indicate that the blog post is published.
     */
    public function published(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'published',
            'published_at' => fake()->dateTimeBetween('-6 months', 'now'),
        ]);
    }

    /**
     * Indicate that the blog post is featured.
     */
    public function featured(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_featured' => true,
        ]);
    }

    /**
     * Indicate that the blog post is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'draft',
            'published_at' => null,
        ]);
    }
}
