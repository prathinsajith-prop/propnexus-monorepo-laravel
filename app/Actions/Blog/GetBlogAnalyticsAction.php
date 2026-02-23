<?php

namespace App\Actions\Blog;

use App\Models\Blog;
use Litepie\Actions\ActionResult;
use Litepie\Actions\BaseAction;

/**
 * GetBlogAnalyticsAction
 *
 * Advanced analytics using the Aggregatable trait
 * Provides comprehensive blog statistics and trends
 */
class GetBlogAnalyticsAction extends BaseAction
{
    protected function rules(): array
    {
        return [
            'metric' => 'sometimes|in:views,likes,shares,comments',
            'period' => 'sometimes|in:day,week,month,year',
            'months' => 'sometimes|integer|min:1|max:24',
            'category' => 'sometimes|string',
            'author_id' => 'sometimes|integer',
        ];
    }

    public function handle(): ActionResult
    {
        $metric = $this->data['metric'] ?? 'views';
        $period = $this->data['period'] ?? 'month';
        $months = $this->data['months'] ?? 6;
        $metricColumn = $metric.'_count';

        $query = Blog::where('status', 'published');

        // Apply filters
        if (! empty($this->data['category'])) {
            $query->where('category', $this->data['category']);
        }

        if (! empty($this->data['author_id'])) {
            $query->where('author_id', $this->data['author_id']);
        }

        // Aggregate statistics using direct queries
        $aggregates = $query->selectRaw("
            COUNT(*) as count,
            SUM({$metricColumn}) as total_metric,
            AVG({$metricColumn}) as avg_metric,
            MAX({$metricColumn}) as max_metric,
            MIN({$metricColumn}) as min_metric,
            SUM(reading_time) as total_reading_time
        ")->first();

        // Trend analysis - group by period
        $dateFormat = $this->getDateFormat($period);
        $trend = Blog::where('status', 'published')
            ->selectRaw("
                {$dateFormat} as period,
                SUM({$metricColumn}) as value,
                COUNT(*) as count
            ")
            ->where('created_at', '>=', now()->subMonths($months))
            ->groupBy('period')
            ->orderBy('period', 'asc')
            ->get();

        // Calculate growth rate
        $recentPeriod = Blog::where('status', 'published')
            ->where('created_at', '>=', now()->subMonths(1))
            ->sum($metricColumn);

        $previousPeriod = Blog::where('status', 'published')
            ->where('created_at', '>=', now()->subMonths(2))
            ->where('created_at', '<', now()->subMonths(1))
            ->sum($metricColumn);

        $growthRate = $previousPeriod > 0
            ? (($recentPeriod - $previousPeriod) / $previousPeriod) * 100
            : 0;

        // Year over year comparison
        $currentYearTotal = Blog::where('status', 'published')
            ->whereYear('created_at', now()->year)
            ->sum($metricColumn);

        $previousYearTotal = Blog::where('status', 'published')
            ->whereYear('created_at', now()->year - 1)
            ->sum($metricColumn);

        // Top performing posts
        $topPosts = Blog::where('status', 'published')
            ->orderByDesc($metricColumn)
            ->take(10)
            ->get(['id', 'title', 'slug', $metricColumn, 'published_at']);

        // Category distribution
        $categoryStats = Blog::where('status', 'published')
            ->selectRaw('category, COUNT(*) as count, SUM('.$metricColumn.') as total_'.$metric)
            ->whereNotNull('category')
            ->groupBy('category')
            ->orderByDesc('total_'.$metric)
            ->get();

        // Weekly distribution
        $weeklyDistribution = Blog::where('status', 'published')
            ->selectRaw('DAYOFWEEK(created_at) as day_of_week, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths($months))
            ->groupBy('day_of_week')
            ->orderBy('day_of_week')
            ->get()
            ->mapWithKeys(function ($dayData) {
                $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

                return [$days[$dayData->day_of_week - 1] => $dayData->count];
            });

        return ActionResult::success([
            'summary' => [
                'total_posts' => $aggregates->count ?? 0,
                'total_'.$metric => $aggregates->total_metric ?? 0,
                'avg_'.$metric => round($aggregates->avg_metric ?? 0, 2),
                'max_'.$metric => $aggregates->max_metric ?? 0,
                'min_'.$metric => $aggregates->min_metric ?? 0,
                'total_reading_time' => round($aggregates->total_reading_time ?? 0, 1),
            ],
            'trends' => [
                'timeline' => $trend,
                'growth_rate' => [
                    'rate' => round($growthRate, 2),
                    'period' => $period,
                    'months' => $months,
                    'current' => $recentPeriod,
                    'previous' => $previousPeriod,
                ],
                'yoy_comparison' => [
                    'current_year' => $currentYearTotal,
                    'previous_year' => $previousYearTotal,
                    'change' => $previousYearTotal > 0
                        ? round((($currentYearTotal - $previousYearTotal) / $previousYearTotal) * 100, 2)
                        : 0,
                ],
            ],
            'top_posts' => $topPosts,
            'by_category' => $categoryStats,
            'weekly_distribution' => $weeklyDistribution,
            'period' => $period,
            'metric' => $metric,
        ]);
    }

    /**
     * Get date format for SQL grouping
     */
    protected function getDateFormat(string $period): string
    {
        return match ($period) {
            'day' => 'DATE(created_at)',
            'week' => 'YEARWEEK(created_at, 1)',
            'month' => "DATE_FORMAT(created_at, '%Y-%m')",
            'year' => 'YEAR(created_at)',
            default => 'DATE(created_at)'
        };
    }
}
