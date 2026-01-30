<?php

namespace Litepie\Layout\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Litepie\Layout\LayoutBuilder create(string $name)
 * @method static \Litepie\Layout\LayoutBuilder for(string $module, string $context)
 * @method static void register(string $module, string $context, \Closure $callback)
 * @method static \Litepie\Layout\Layout|null get(string $module, string $context, ?int $userId = null)
 * @method static \Litepie\Layout\Layout build(string $module, string $context, \Closure $callback, ?int $userId = null)
 * @method static bool has(string $module, string $context)
 * @method static void clearCache(string $module, string $context, ?int $userId = null)
 * @method static void clearUserCache(int $userId)
 * @method static void clearAllCache()
 * @method static \Litepie\Layout\LayoutManager setCacheTtl(int $seconds)
 * @method static \Litepie\Layout\LayoutManager setCachePrefix(string $prefix)
 * @method static array getRegistered()
 * @method static \Litepie\Layout\Layout|null fresh(string $module, string $context)
 *
 * @see \Litepie\Layout\LayoutManager
 */
class Layout extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'layout';
    }
}
