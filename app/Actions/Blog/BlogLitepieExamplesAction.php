<?php

namespace App\Actions\Blog;

use App\Models\Blog;
use Litepie\Actions\ActionResult;
use Litepie\Actions\BaseAction;

/**
 * BlogLitepieExamplesAction
 *
 * Demonstrates all Litepie Database features with the Blog model
 * This is a reference implementation showing how to use each trait
 */
class BlogLitepieExamplesAction extends BaseAction
{
    protected function rules(): array
    {
        return [
            'example' => 'required|string',
        ];
    }

    public function handle(): ActionResult
    {
        $example = $this->data['example'];

        $examples = [
            // Searchable Examples
            'search_basic' => fn () => Blog::search('Laravel framework')->get(),
            'search_fulltext' => fn () => Blog::fullTextSearch('Laravel framework')->get(),
            'search_weighted' => fn () => Blog::weightedSearch('Laravel')->orderByDesc('search_relevance')->get(),
            'search_fuzzy' => fn () => Blog::fuzzySearch('Laravle', threshold: 2)->get(),
            'search_boolean' => fn () => Blog::booleanSearch('+Laravel -CodeIgniter')->get(),

            // Cacheable Examples
            'cache_basic' => fn () => Blog::where('status', 'published')->cacheFor(60)->get(),
            'cache_tags' => fn () => Blog::where('featured', true)->cacheWithTags(['featured'], 60)->get(),
            'cache_forever' => fn () => Blog::where('category', 'tech')->cacheForever('tech-posts'),
            'cache_clear' => fn () => Blog::clearModelCache(),

            // Sluggable Examples
            'find_by_slug' => fn () => Blog::findBySlug('my-amazing-post'),
            'regenerate_slug' => fn () => Blog::find(1)?->regenerateSlugs(),

            // Paginatable Examples
            'paginate_cursor' => fn () => Blog::where('status', 'published')->cursorPaginate(50),
            'paginate_fast' => fn () => Blog::where('status', 'published')->fastPaginate(20),
            'paginate_optimized' => fn () => Blog::where('status', 'published')->optimizedPaginate(50),
            'paginate_cached' => fn () => Blog::with('category')->cachedPaginate(perPage: 20, cacheTtl: 300),

            // Aggregatable Examples
            'aggregate_stats' => fn () => Blog::where('status', 'published')->aggregate([
                'sum' => 'views_count',
                'avg' => 'views_count',
                'max' => 'views_count',
            ]),
            'trend_analysis' => fn () => Blog::trend('created_at', 'month', 'views_count', 'sum', 6),
            'growth_rate' => fn () => Blog::growthRate('views_count', 'month', 6),
            'yoy_comparison' => fn () => Blog::yearOverYear('views_count', 'sum'),

            // Archivable Examples
            'archive_blog' => fn () => Blog::find(1)?->archive('Content outdated', auth()->user()),
            'archived_posts' => fn () => Blog::onlyArchived()->get(),
            'recently_archived' => fn () => Blog::recentlyArchived(30)->get(),
            'unarchive_blog' => fn () => Blog::onlyArchived()->first()?->unArchive(),

            // Versionable Examples
            'create_version' => fn () => Blog::find(1)?->createVersion('Major update', auth()->user()),
            'version_history' => fn () => Blog::find(1)?->getVersionHistory(),
            'rollback_version' => fn () => Blog::find(1)?->rollbackToVersion(5),
            'compare_versions' => fn () => Blog::find(1)?->compareVersions(1, 5),

            // Metable Examples
            'set_meta' => fn () => Blog::find(1)?->setMeta('featured_priority', 10),
            'get_meta' => fn () => Blog::find(1)?->getMeta('featured_priority'),
            'increment_meta' => fn () => Blog::find(1)?->incrementMeta('view_count_alt'),
            'query_meta' => fn () => Blog::whereMeta('featured_priority', '>', 5)->get(),

            // Translatable Examples
            'translate' => fn () => Blog::find(1)?->translate('es', ['title' => 'Mi Post', 'content' => 'Contenido']),
            'get_translation' => fn () => Blog::find(1)?->setLocale('es')->title,
            'translation_completeness' => fn () => Blog::find(1)?->getTranslationCompleteness('es'),

            // Sortable Examples
            'reorder' => fn () => Blog::find(1)?->moveTo(5),
            'move_up' => fn () => Blog::find(1)?->moveUp(),
            'move_down' => fn () => Blog::find(1)?->moveDown(),

            // Exportable Examples
            'export_csv' => fn () => Blog::where('status', 'published')->get()->exportToCsv(),
            'export_excel' => fn () => Blog::where('status', 'published')->get()->exportToExcel(),
            'export_json' => fn () => Blog::where('status', 'published')->get()->exportToJson(),

            // Batch Operations Examples
            'batch_update' => fn () => Blog::where('status', 'draft')->batch(100, function ($blogs) {
                foreach ($blogs as $blog) {
                    $blog->status = 'review';
                    $blog->save();
                }
            }),

            // Measurable Examples
            'measure_performance' => fn () => Blog::measureQuery(fn () => Blog::where('status', 'published')->get()),
        ];

        if (! isset($examples[$example])) {
            return ActionResult::error('Example not found. Available: '.implode(', ', array_keys($examples)), 404);
        }

        try {
            $result = $examples[$example]();

            return ActionResult::success([
                'example' => $example,
                'result' => $result,
                'description' => $this->getExampleDescription($example),
            ]);
        } catch (\Exception $e) {
            return ActionResult::error($e->getMessage(), 500);
        }
    }

    protected function getExampleDescription(string $example): string
    {
        $descriptions = [
            'search_basic' => 'Basic search across searchable fields',
            'search_fulltext' => 'MySQL FULLTEXT search for better performance',
            'search_weighted' => 'Weighted search with relevance scoring',
            'search_fuzzy' => 'Fuzzy search that handles typos',
            'search_boolean' => 'Boolean search with +/- operators',
            'cache_basic' => 'Cache query results for 60 minutes',
            'cache_tags' => 'Cache with tags for easy invalidation',
            'cache_forever' => 'Cache permanently until manually cleared',
            'cache_clear' => 'Clear all blog model cache',
            'find_by_slug' => 'Find blog by slug instead of ID',
            'regenerate_slug' => 'Regenerate slug from title',
            'paginate_cursor' => 'Cursor pagination for large datasets',
            'paginate_fast' => 'Fast pagination without total count',
            'paginate_optimized' => 'Optimized pagination with approximate count',
            'paginate_cached' => 'Cached pagination for expensive queries',
            'aggregate_stats' => 'Aggregate statistics (sum, avg, max)',
            'trend_analysis' => 'Analyze trends over time',
            'growth_rate' => 'Calculate growth rate',
            'yoy_comparison' => 'Year over year comparison',
            'archive_blog' => 'Archive a blog post with reason',
            'archived_posts' => 'Get only archived posts',
            'recently_archived' => 'Get recently archived posts (30 days)',
            'unarchive_blog' => 'Restore archived post',
            'create_version' => 'Create a new version snapshot',
            'version_history' => 'Get complete version history',
            'rollback_version' => 'Rollback to specific version',
            'compare_versions' => 'Compare two versions',
            'set_meta' => 'Set custom metadata',
            'get_meta' => 'Get metadata value',
            'increment_meta' => 'Increment numeric metadata',
            'query_meta' => 'Query blogs by metadata',
            'translate' => 'Add Spanish translation',
            'get_translation' => 'Get translated content',
            'translation_completeness' => 'Check translation percentage',
            'reorder' => 'Move blog to specific position',
            'move_up' => 'Move blog up in order',
            'move_down' => 'Move blog down in order',
            'export_csv' => 'Export blogs to CSV',
            'export_excel' => 'Export blogs to Excel',
            'export_json' => 'Export blogs to JSON',
            'batch_update' => 'Batch update blogs efficiently',
            'measure_performance' => 'Measure query performance',
        ];

        return $descriptions[$example] ?? 'No description available';
    }
}
