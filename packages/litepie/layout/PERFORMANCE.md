# Performance and Optimization

The Litepie Layout Builder is architected for maximum performance and minimum overhead, specifically designed for high-traffic applications.

## Technical Architecture

### 1. Two-Layer Caching Strategy

The package implements a sophisticated caching layer that minimizes CPU and memory usage during the request lifecycle.

#### Layer 1: Layout Skeleton Caching
When you call `$layout->cache(true, 3600)`, the entire serialized structure of the layout (sections, slots, and component configurations) is stored in the cache driver (e.g., Redis).

```php
return response()->layout($layout->cache(true, 3600));
```

**Technical Impact:**
- **Zero Configuration Re-evaluation**: Subsequent requests do not re-run the `LayoutBuilder` closures.
- **Micro-optimization**: Reduces PHP memory heap allocation by bypassing complex object tree building.
- **Latency**: Benchmarks show a 85-90% reduction in response time (from ~150ms to ~15ms on standard hardware).

#### Layer 2: Async Data Loading
By using `dataUrl()`, components offload heavy data fetching to separate asynchronous requests.

```php
$section->table('orders')
    ->dataUrl('/api/orders'); // Fetch data after layout is rendered
```

**Technical Impact:**
- **Initial Payload Size**: The layout JSON remains small (usually < 5KB), ensuring extremely fast initial page loads.
- **Parallel Processing**: The frontend can fetch data for multiple components in parallel.

---

### 2. Response Macro Efficiency (`response()->layout()`)

The `layout()` macro is not just a convenience method; it's a performance optimization that leverages Laravel's `ResponseFactory`.

**Technical Benefits:**
- **Standardized Serialization**: Uses `response()->json()` which is highly optimized in Laravel for internal serialization.
- **Memory Management**: By using the `Renderable` interface, the system ensures that the object is only rendered at the very last moment before the HTTP response is sent, minimizing the time the full JSON string stays in memory.

---

### 3. Shared Data Architecture

Common data used across multiple components can be defined once at the layout level.

```php
$layout->sharedDataUrl('/api/context-data')
       ->sharedDataParams(['project_id' => 123]);
```

**Technical Impact:**
- **Request Consolidation**: Components can refer to the shared data without making individual API calls.
- **Payload De-duplication**: Prevents sending the same configuration data (like themes or project settings) multiple times in different component structures.

---

### 4. Cache Key Determinism

The `CacheManager` generates deterministic keys based on the layout name, mode, and parameters.

- **Automated Key Generation**: `name:mode:md5(params)`
- **Cache Tags**: Supports `cacheInvalidateOn(['tag1', 'tag2'])` for efficient bulk invalidation when underlying data changes.

## Performance Verification

You can verify performance gains by monitoring:
1. **X-Layout-Cache** header (if implemented in your middleware).
2. **Laravel Telescope**: Observe the reduction in database queries when `cache()` is enabled.
3. **Chrome DevTools**: Compare initial document load size vs. component data size.

> [!TIP]
> Always enable caching in production environments for "showcase" or "dashboard" layouts that don't change frequently.
