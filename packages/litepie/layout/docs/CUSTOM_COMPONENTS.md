# Custom Components Guide

This guide explains how to create and use custom components for project-specific requirements in the Litepie Layout package.

## Table of Contents

1. [Overview](#overview)
2. [Three Approaches](#three-approaches)
3. [Approach 1: Using CustomComponent](#approach-1-using-customcomponent)
4. [Approach 2: Creating Custom Component Classes](#approach-2-creating-custom-component-classes)
5. [Approach 3: Creating Custom Section Classes](#approach-3-creating-custom-section-classes)
6. [Real-World Examples](#real-world-examples)
7. [Frontend Integration](#frontend-integration)
8. [Best Practices](#best-practices)
9. [Testing Custom Components](#testing-custom-components)

## Overview

The Litepie Layout package provides three flexible approaches for implementing custom components:

- **Quick Inline Components**: Use `CustomComponent` for simple, one-off components
- **Reusable Component Classes**: Extend `BaseComponent` for shared, team-wide components
- **Complex Section Classes**: Extend `BaseSection` for nested layouts and component groupings

Choose the approach based on your needs:
- **Prototyping or simple needs** → Use `CustomComponent`
- **Reusable across project** → Create custom component class
- **Complex nested layouts** → Create custom section class

## Three Approaches

### Quick Comparison

| Feature | CustomComponent | Custom Class | Custom Section |
|---------|----------------|--------------|----------------|
| Setup Time | Instant | 5-10 min | 10-15 min |
| Reusability | Low | High | High |
| Type Safety | Basic | Strong | Strong |
| IDE Support | Limited | Full | Full |
| Team Sharing | Difficult | Easy | Easy |
| Best For | Prototyping | Components | Layouts |

## Approach 1: Using CustomComponent

Perfect for quick inline components, prototyping, or one-off requirements.

### Basic Usage

```php
use Litepie\Layout\LayoutBuilder;

$layout = LayoutBuilder::create('custom', 'custom-page')
    ->section('main', function ($section) {
        $section->custom('qr-code')
            ->component('QrCodeGenerator')  // Frontend component name
            ->view('components.qr-code')    // Optional Blade view
            ->data([
                'content' => 'https://example.com',
                'size' => 200,
            ])
            ->with('downloadable', true)
            ->label('QR Code Generator');
    });
```

### Available Methods

```php
// Set frontend component name
->component('ComponentName')

// Set Blade view path
->view('path.to.view')

// Pass data array
->data(['key' => 'value'])

// Add single data item
->with('key', 'value')

// Standard methods from BaseComponent
->label('Label')
->help('Help text')
->permissions(['view-content'])
->dataUrl('/api/endpoint')
```

### Examples

#### Color Picker

```php
$section->custom('brand-color')
    ->component('ColorPicker')
    ->data([
        'value' => '#3B82F6',
        'format' => 'hex',
        'presets' => ['#3B82F6', '#EF4444', '#10B981'],
        'showAlpha' => true,
    ])
    ->label('Brand Color')
    ->help('Select your primary brand color');
```

#### Signature Pad

```php
$section->custom('signature')
    ->component('SignaturePad')
    ->data([
        'width' => 600,
        'height' => 300,
        'penColor' => '#000000',
    ])
    ->with('clearButton', true)
    ->with('saveFormat', 'base64')
    ->label('Digital Signature')
    ->required(true);
```

#### Rich Text Editor

```php
$section->custom('content-editor')
    ->component('RichTextEditor')
    ->data([
        'content' => '',
        'toolbar' => ['bold', 'italic', 'link', 'image'],
        'height' => 400,
    ])
    ->with('autosave', true)
    ->with('imageUpload', true)
    ->label('Content Editor');
```

## Approach 2: Creating Custom Component Classes

Best for reusable components with specific functionality that will be used across your project.

### Step 1: Create Component Class

Create a new file in `src/Components/VideoPlayerComponent.php`:

```php
<?php

namespace Litepie\Layout\Components;

class VideoPlayerComponent extends BaseComponent
{
    protected ?string $videoUrl = null;
    protected ?string $posterUrl = null;
    protected bool $autoplay = false;
    protected bool $controls = true;
    protected bool $loop = false;
    protected bool $muted = false;
    protected ?int $width = null;
    protected ?int $height = null;
    protected array $tracks = [];

    public function __construct(string $name, string $type = 'video-player')
    {
        parent::__construct($name, $type);
    }

    public static function make(string $name): self
    {
        return new static($name);
    }

    public function videoUrl(string $url): self
    {
        $this->videoUrl = $url;
        return $this;
    }

    public function poster(string $url): self
    {
        $this->posterUrl = $url;
        return $this;
    }

    public function autoplay(bool $autoplay = true): self
    {
        $this->autoplay = $autoplay;
        return $this;
    }

    public function controls(bool $controls = true): self
    {
        $this->controls = $controls;
        return $this;
    }

    public function addTrack(string $src, string $kind = 'subtitles', string $label = '', string $srclang = 'en'): self
    {
        $this->tracks[] = [
            'src' => $src,
            'kind' => $kind,
            'label' => $label,
            'srclang' => $srclang,
        ];
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'video_url' => $this->videoUrl,
            'poster_url' => $this->posterUrl,
            'autoplay' => $this->autoplay,
            'controls' => $this->controls,
            'loop' => $this->loop,
            'muted' => $this->muted,
            'width' => $this->width,
            'height' => $this->height,
            'tracks' => $this->tracks,
        ]);
    }
}
```

### Step 2: Use the Component

```php
use App\Components\VideoPlayerComponent;
use Litepie\Layout\LayoutBuilder;

$layout = LayoutBuilder::create('video', 'video-page')
    ->section('main', function ($section) {
        $section->addComponent(
            VideoPlayerComponent::make('intro-video')
                ->videoUrl('/videos/intro.mp4')
                ->poster('/images/poster.jpg')
                ->controls()
                ->addTrack('/subtitles/en.vtt', 'subtitles', 'English', 'en')
                ->label('Introduction Video')
        );
    });
```

### Step 3: Add Helper Method (Optional)

In `src/Helpers/SectionContainer.php`, add:

```php
public function videoPlayer(string $name): VideoPlayerComponent
{
    $component = VideoPlayerComponent::make($name);
    $this->components[] = $component;
    return $component;
}
```

Then use it more fluently:

```php
$section->videoPlayer('intro-video')
    ->videoUrl('/videos/intro.mp4')
    ->controls();
```

## Approach 3: Creating Custom Section Classes

Best for complex layouts with nested structures and component groupings.

### Step 1: Create Section Class

Create `src/Sections/KanbanBoardSection.php`:

```php
<?php

namespace Litepie\Layout\Sections;

class KanbanBoardSection extends BaseSection
{
    protected array $columns = [];
    protected bool $draggable = true;
    protected ?string $onCardMove = null;

    public function __construct(string $name, string $type = 'kanban-board')
    {
        parent::__construct($name, $type);
    }

    public static function make(string $name): self
    {
        return new static($name);
    }

    public function addColumn(string $id, string $title, array $cards = [], ?string $color = null, ?int $limit = null): self
    {
        $this->columns[] = [
            'id' => $id,
            'title' => $title,
            'cards' => $cards,
            'color' => $color,
            'limit' => $limit,
        ];
        return $this;
    }

    public function draggable(bool $enabled = true): self
    {
        $this->draggable = $enabled;
        return $this;
    }

    public function onCardMove(string $callback): self
    {
        $this->onCardMove = $callback;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'columns' => $this->columns,
            'draggable' => $this->draggable,
            'on_card_move' => $this->onCardMove,
        ]);
    }
}
```

### Step 2: Use the Section

```php
use App\Sections\KanbanBoardSection;
use Litepie\Layout\LayoutBuilder;

$layout = LayoutBuilder::create('kanban', 'project-board')
    ->section('main', function ($section) {
        $section->addComponent(
            KanbanBoardSection::make('project-board')
                ->addColumn('todo', 'To Do', [
                    ['id' => 1, 'title' => 'Design homepage'],
                    ['id' => 2, 'title' => 'Write API docs'],
                ], '#94A3B8', 10)
                ->addColumn('in-progress', 'In Progress', [
                    ['id' => 3, 'title' => 'Build dashboard'],
                ], '#3B82F6', 5)
                ->addColumn('done', 'Done', [], '#10B981')
                ->draggable()
                ->onCardMove('handleCardMove')
                ->label('Project Tasks')
        );
    });
```

## Real-World Examples

### Chat Component

```php
class ChatComponent extends BaseComponent
{
    protected ?string $channelId = null;
    protected array $messages = [];
    protected bool $attachments = true;
    protected bool $emojis = true;
    protected ?int $maxLength = 1000;

    public function channel(string $id): self
    {
        $this->channelId = $id;
        return $this;
    }

    public function messages(array $messages): self
    {
        $this->messages = $messages;
        return $this;
    }

    public function attachments(bool $enabled = true): self
    {
        $this->attachments = $enabled;
        return $this;
    }

    // ... more methods
}
```

### 3D Model Viewer

```php
class ModelViewerComponent extends BaseComponent
{
    protected ?string $modelUrl = null;
    protected ?string $format = 'gltf';
    protected bool $autoRotate = false;
    protected array $camera = ['position' => [0, 0, 5]];

    public function model(string $url, string $format = 'gltf'): self
    {
        $this->modelUrl = $url;
        $this->format = $format;
        return $this;
    }

    public function autoRotate(bool $enabled = true): self
    {
        $this->autoRotate = $enabled;
        return $this;
    }

    // ... more methods
}
```

### Calendar/Event Component

```php
class CalendarComponent extends BaseComponent
{
    protected ?string $view = 'month';
    protected array $events = [];
    protected bool $draggable = true;
    protected bool $selectable = true;

    public function view(string $view): self
    {
        $this->view = $view; // month, week, day, list
        return $this;
    }

    public function events(array $events): self
    {
        $this->events = $events;
        return $this;
    }

    // ... more methods
}
```

## Frontend Integration

### React Component

```jsx
// components/VideoPlayer.jsx
import React from 'react';

export const VideoPlayer = ({ 
  video_url, 
  poster_url, 
  autoplay, 
  controls,
  tracks 
}) => {
  return (
    <div className="video-player">
      <video 
        src={video_url}
        poster={poster_url}
        autoPlay={autoplay}
        controls={controls}
      >
        {tracks?.map((track, idx) => (
          <track 
            key={idx}
            src={track.src}
            kind={track.kind}
            label={track.label}
            srcLang={track.srclang}
          />
        ))}
      </video>
    </div>
  );
};
```

### Vue Component

```vue
<!-- components/VideoPlayer.vue -->
<template>
  <div class="video-player">
    <video 
      :src="video_url"
      :poster="poster_url"
      :autoplay="autoplay"
      :controls="controls"
    >
      <track 
        v-for="(track, idx) in tracks"
        :key="idx"
        :src="track.src"
        :kind="track.kind"
        :label="track.label"
        :srclang="track.srclang"
      />
    </video>
  </div>
</template>

<script>
export default {
  props: ['video_url', 'poster_url', 'autoplay', 'controls', 'tracks']
}
</script>
```

### Component Registry

Create a component registry for your frontend:

```javascript
// componentRegistry.js
import { VideoPlayer } from './components/VideoPlayer';
import { ChatComponent } from './components/ChatComponent';
import { ModelViewer } from './components/ModelViewer';

export const componentRegistry = {
  'video-player': VideoPlayer,
  'chat': ChatComponent,
  '3d-viewer': ModelViewer,
  // ... more components
};

// Usage in your layout renderer
const Component = componentRegistry[component.type];
return <Component {...component} />;
```

## Best Practices

### 1. Naming Conventions

```php
// Class names: PascalCase with "Component" or "Section" suffix
class VideoPlayerComponent extends BaseComponent {}
class KanbanBoardSection extends BaseSection {}

// Component type: kebab-case
public function __construct(string $name, string $type = 'video-player') {}

// Method names: camelCase
public function videoUrl(string $url): self {}
```

### 2. Method Chaining

```php
// Always return $this for fluent API
public function setOption(string $value): self
{
    $this->option = $value;
    return $this;
}

// Enable chaining
$component->option1('value')
    ->option2('value')
    ->option3('value');
```

### 3. Default Values

```php
public function __construct(string $name, string $type = 'custom')
{
    parent::__construct($name, $type);
    
    // Set sensible defaults
    $this->controls = true;
    $this->autoplay = false;
    $this->volume = 0.8;
}
```

### 4. Data Serialization

```php
public function toArray(): array
{
    return array_merge(parent::toArray(), [
        // Convert camelCase to snake_case for JSON
        'video_url' => $this->videoUrl,
        'poster_url' => $this->posterUrl,
        'auto_play' => $this->autoplay,
        
        // Include all configurable properties
        'controls' => $this->controls,
        'tracks' => $this->tracks,
    ]);
}
```

### 5. Type Hints and Documentation

```php
/**
 * Set the video URL
 *
 * @param string $url The URL of the video file
 * @return self
 */
public function videoUrl(string $url): self
{
    $this->videoUrl = $url;
    return $this;
}
```

### 6. Validation

```php
use Litepie\Layout\Traits\Validatable;

class MyComponent extends BaseComponent
{
    use Validatable;
    
    public function __construct(string $name)
    {
        parent::__construct($name, 'custom');
        
        // Add validation rules
        $this->validation('required|url');
    }
}
```

### 7. Permissions

```php
$component->videoPlayer('intro')
    ->videoUrl('/videos/intro.mp4')
    ->permissions(['view-videos'])
    ->roles(['admin', 'editor']);
```

### 8. Data Loading

```php
$component->videoPlayer('featured')
    ->dataUrl('/api/videos/featured')
    ->dataParams(['category' => 'tutorials'])
    ->dataTransform(function ($data) {
        return [
            'video_url' => $data['url'],
            'poster_url' => $data['thumbnail'],
        ];
    });
```

### 9. Responsive Configuration

```php
use Litepie\Layout\Traits\Responsive;

class MyComponent extends BaseComponent
{
    use Responsive;
    
    // Use in layouts
    $component->setDeviceConfig('mobile', [
        'width' => 320,
        'controls' => true,
    ]);
}
```

### 10. Error Handling

```php
public function videoUrl(string $url): self
{
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        throw new \InvalidArgumentException("Invalid URL: {$url}");
    }
    
    $this->videoUrl = $url;
    return $this;
}
```

## Testing Custom Components

### Unit Test Example

```php
use Tests\TestCase;
use App\Components\VideoPlayerComponent;

class VideoPlayerComponentTest extends TestCase
{
    public function test_can_create_video_player()
    {
        $player = VideoPlayerComponent::make('test-video')
            ->videoUrl('https://example.com/video.mp4')
            ->controls();
        
        $this->assertEquals('test-video', $player->getName());
        $this->assertEquals('video-player', $player->getType());
    }
    
    public function test_serializes_to_array_correctly()
    {
        $player = VideoPlayerComponent::make('test')
            ->videoUrl('https://example.com/video.mp4')
            ->poster('https://example.com/poster.jpg')
            ->autoplay();
        
        $array = $player->toArray();
        
        $this->assertEquals('https://example.com/video.mp4', $array['video_url']);
        $this->assertEquals('https://example.com/poster.jpg', $array['poster_url']);
        $this->assertTrue($array['autoplay']);
    }
    
    public function test_can_add_tracks()
    {
        $player = VideoPlayerComponent::make('test')
            ->addTrack('/sub/en.vtt', 'subtitles', 'English', 'en')
            ->addTrack('/sub/es.vtt', 'subtitles', 'Spanish', 'es');
        
        $array = $player->toArray();
        
        $this->assertCount(2, $array['tracks']);
        $this->assertEquals('English', $array['tracks'][0]['label']);
    }
}
```

### Integration Test

```php
public function test_video_player_in_layout()
{
    $layout = LayoutBuilder::create('test', 'test')
        ->section('main', function ($section) {
            $section->addComponent(
                VideoPlayerComponent::make('intro')
                    ->videoUrl('/videos/intro.mp4')
                    ->controls()
            );
        });
    
    $array = $layout->toArray();
    
    $this->assertArrayHasKey('sections', $array);
    $this->assertCount(1, $array['sections']['main']['components']);
}
```

## Summary

Choose the right approach based on your needs:

| Scenario | Recommended Approach |
|----------|---------------------|
| Quick prototype | `CustomComponent` inline |
| One-off custom element | `CustomComponent` inline |
| Reusable across project | Custom component class |
| Team-wide component | Custom component class |
| Complex nested layout | Custom section class |
| Grouping multiple components | Custom section class |

All three approaches:
- Support full fluent API
- Integrate with permissions and roles
- Work with data loading
- Serialize to JSON via `toArray()`
- Compatible with frontend frameworks

For complete examples, see `examples/CustomComponentExample.php`.
