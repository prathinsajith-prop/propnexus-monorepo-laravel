<?php

/**
 * Simple test to verify cache methods exist on LayoutBuilder
 */

require_once __DIR__.'/src/LayoutBuilder.php';
require_once __DIR__.'/src/Traits/Cacheable.php';

use Litepie\Layout\LayoutBuilder;

// Test that methods exist and are chainable
try {
    $builder = new LayoutBuilder('test', 'view');

    echo "Testing cache methods...\n";

    // Test cache methods
    $result = $builder
        ->cache()
        ->ttl(600)
        ->key('test-key')
        ->tags(['tag1', 'tag2'])
        ->beforeRender(function ($layout) {
            echo "Before render callback\n";
        })
        ->afterRender(function ($layout, $output) {
            echo "After render callback\n";
        })
        ->resolveAuthorization((object) ['id' => 1]);

    if ($result instanceof LayoutBuilder) {
        echo "✓ All cache methods are chainable\n";
        echo "✓ ttl() method exists\n";
        echo "✓ key() method exists\n";
        echo "✓ tags() method exists\n";
        echo "✓ beforeRender() method exists\n";
        echo "✓ afterRender() method exists\n";
        echo "✓ resolveAuthorization() method exists\n";
        echo "\nAll tests passed!\n";
    } else {
        echo "✗ Methods are not chainable\n";
    }
} catch (\Throwable $e) {
    echo '✗ Error: '.$e->getMessage()."\n";
    echo '  in '.$e->getFile().' on line '.$e->getLine()."\n";
}
