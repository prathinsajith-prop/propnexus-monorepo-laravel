<?php

/**
 * Custom Component Examples
 *
 * This file demonstrates three approaches to implementing custom components
 * for project-specific requirements:
 *
 * 1. Using the CustomComponent class (quick inline components)
 * 2. Creating custom component classes extending BaseComponent
 * 3. Creating custom section classes extending BaseSection
 */

use Litepie\Layout\Components\CustomComponent;
use Litepie\Layout\LayoutBuilder;

// ============================================================================
// APPROACH 1: Using CustomComponent Class
// ============================================================================
// Best for: Quick inline components, simple custom elements, prototyping

$layout1 = LayoutBuilder::create('custom-inline', 'custom-inline')
    ->title('Custom Component Examples - Inline Approach')

    // Example 1: QR Code Generator
    ->section('qr-code-section', function ($section) {
        $section->custom('qr-code-generator')
            ->component('QrCodeGenerator')  // Frontend component name
            ->view('components.qr-code')    // Blade view path (optional)
            ->data([
                'content' => 'https://example.com',
                'size' => 200,
                'color' => '#000000',
                'background' => '#ffffff',
            ])
            ->with('downloadable', true)
            ->with('format', 'png')
            ->label('QR Code Generator')
            ->help('Generate QR codes for URLs, text, or contact information');
    })

    // Example 2: Signature Pad
    ->section('signature-section', function ($section) {
        $section->custom('signature-pad')
            ->component('SignaturePad')
            ->data([
                'width' => 600,
                'height' => 300,
                'penColor' => '#000000',
                'backgroundColor' => '#ffffff',
            ])
            ->with('clearButton', true)
            ->with('saveFormat', 'base64')
            ->label('Digital Signature')
            ->required(true)
            ->validation('required|signature');
    })

    // Example 3: Color Picker
    ->section('color-picker-section', function ($section) {
        $section->custom('brand-color')
            ->component('ColorPicker')
            ->data([
                'value' => '#3B82F6',
                'format' => 'hex',
                'presets' => ['#3B82F6', '#EF4444', '#10B981', '#F59E0B'],
                'showAlpha' => true,
            ])
            ->with('mode', 'advanced')
            ->with('swatches', true)
            ->label('Brand Color')
            ->help('Select your primary brand color');
    })

    // Example 4: Rich Text Editor
    ->section('editor-section', function ($section) {
        $section->custom('content-editor')
            ->component('RichTextEditor')
            ->view('components.editor')
            ->data([
                'content' => '',
                'toolbar' => ['bold', 'italic', 'underline', 'link', 'image', 'code'],
                'height' => 400,
                'placeholder' => 'Start typing...',
            ])
            ->with('autosave', true)
            ->with('spellcheck', true)
            ->with('imageUpload', true)
            ->label('Content Editor')
            ->required(true);
    })

    // Example 5: Map Component
    ->section('map-section', function ($section) {
        $section->custom('location-map')
            ->component('MapPicker')
            ->data([
                'center' => ['lat' => 40.7128, 'lng' => -74.0060],
                'zoom' => 12,
                'marker' => true,
                'searchBox' => true,
            ])
            ->with('mapType', 'roadmap')
            ->with('interactive', true)
            ->label('Select Location')
            ->help('Click on the map to select a location');
    })

    // Example 6: File Uploader with Preview
    ->section('uploader-section', function ($section) {
        $section->custom('document-uploader')
            ->component('FileUploader')
            ->data([
                'accept' => '.pdf,.doc,.docx',
                'maxSize' => 10485760, // 10MB
                'multiple' => true,
                'preview' => true,
            ])
            ->with('dragDrop', true)
            ->with('progress', true)
            ->label('Upload Documents')
            ->validation('required|file|max:10240');
    })

    // Example 7: Calendar/Date Range Picker
    ->section('calendar-section', function ($section) {
        $section->custom('date-range')
            ->component('DateRangePicker')
            ->data([
                'startDate' => null,
                'endDate' => null,
                'format' => 'Y-m-d',
                'minDate' => now()->format('Y-m-d'),
            ])
            ->with('presets', [
                'today' => 'Today',
                'week' => 'This Week',
                'month' => 'This Month',
            ])
            ->label('Select Date Range')
            ->required(true);
    });

// ============================================================================
// APPROACH 2: Creating Custom Component Classes
// ============================================================================
// Best for: Reusable components, complex logic, team collaboration

// This would typically be in: src/Components/VideoPlayerComponent.php
class VideoPlayerComponent extends \Litepie\Layout\Components\BaseComponent
{
    protected ?string $videoUrl = null;

    protected ?string $posterUrl = null;

    protected bool $autoplay = false;

    protected bool $controls = true;

    protected bool $loop = false;

    protected bool $muted = false;

    protected ?int $width = null;

    protected ?int $height = null;

    protected array $tracks = []; // Subtitles/captions

    protected ?string $quality = 'auto';

    protected array $chapters = [];

    protected bool $pip = true; // Picture-in-picture

    protected bool $fullscreen = true;

    protected ?float $playbackRate = 1.0;

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

    public function loop(bool $loop = true): self
    {
        $this->loop = $loop;

        return $this;
    }

    public function muted(bool $muted = true): self
    {
        $this->muted = $muted;

        return $this;
    }

    public function dimensions(int $width, int $height): self
    {
        $this->width = $width;
        $this->height = $height;

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

    public function quality(string $quality): self
    {
        $this->quality = $quality;

        return $this;
    }

    public function addChapter(string $title, float $time): self
    {
        $this->chapters[] = [
            'title' => $title,
            'time' => $time,
        ];

        return $this;
    }

    public function pip(bool $enabled = true): self
    {
        $this->pip = $enabled;

        return $this;
    }

    public function fullscreen(bool $enabled = true): self
    {
        $this->fullscreen = $enabled;

        return $this;
    }

    public function playbackRate(float $rate): self
    {
        $this->playbackRate = $rate;

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
            'quality' => $this->quality,
            'chapters' => $this->chapters,
            'pip' => $this->pip,
            'fullscreen' => $this->fullscreen,
            'playback_rate' => $this->playbackRate,
        ]);
    }
}

// Usage example of VideoPlayerComponent
$layout2 = LayoutBuilder::create('video-layout', 'video-showcase')
    ->title('Video Player Examples')

    ->section('video-section', function ($section) {
        // Simple video player
        $section->addComponent(
            VideoPlayerComponent::make('intro-video')
                ->videoUrl('/videos/introduction.mp4')
                ->poster('/images/video-poster.jpg')
                ->controls()
                ->dimensions(800, 450)
                ->label('Introduction Video')
        );

        // Advanced video with tracks and chapters
        $section->addComponent(
            VideoPlayerComponent::make('training-video')
                ->videoUrl('/videos/training.mp4')
                ->poster('/images/training-poster.jpg')
                ->controls()
                ->autoplay()
                ->muted()
                ->addTrack('/subtitles/en.vtt', 'subtitles', 'English', 'en')
                ->addTrack('/subtitles/es.vtt', 'subtitles', 'Spanish', 'es')
                ->addChapter('Introduction', 0)
                ->addChapter('Setup', 120)
                ->addChapter('Advanced Features', 300)
                ->quality('1080p')
                ->pip()
                ->fullscreen()
                ->label('Training Video')
        );
    });

// ============================================================================
// APPROACH 3: Creating Custom Section Classes
// ============================================================================
// Best for: Complex layouts, nested structures, component groupings

// This would typically be in: src/Sections/KanbanBoardSection.php
class KanbanBoardSection extends \Litepie\Layout\Sections\BaseSection
{
    protected array $columns = [];

    protected bool $draggable = true;

    protected ?string $onCardMove = null;

    protected ?string $dataUrl = null;

    protected array $cardTemplate = [];

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

    public function dataUrl(string $url): self
    {
        $this->dataUrl = $url;

        return $this;
    }

    public function cardTemplate(array $template): self
    {
        $this->cardTemplate = $template;

        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'columns' => $this->columns,
            'draggable' => $this->draggable,
            'on_card_move' => $this->onCardMove,
            'data_url' => $this->dataUrl,
            'card_template' => $this->cardTemplate,
        ]);
    }
}

// Usage example of KanbanBoardSection
$layout3 = LayoutBuilder::create('project-kanban', 'kanban-view')
    ->title('Project Management Board')

    ->section('kanban', function ($section) {
        $section->addComponent(
            KanbanBoardSection::make('project-board')
                ->addColumn('todo', 'To Do', [
                    ['id' => 1, 'title' => 'Design homepage', 'assignee' => 'John'],
                    ['id' => 2, 'title' => 'Write API docs', 'assignee' => 'Sarah'],
                ], '#94A3B8', 10)
                ->addColumn('in-progress', 'In Progress', [
                    ['id' => 3, 'title' => 'Build user dashboard', 'assignee' => 'Mike'],
                ], '#3B82F6', 5)
                ->addColumn('review', 'Review', [
                    ['id' => 4, 'title' => 'Fix login bug', 'assignee' => 'Emily'],
                ], '#F59E0B', 5)
                ->addColumn('done', 'Done', [
                    ['id' => 5, 'title' => 'Setup CI/CD', 'assignee' => 'John'],
                ], '#10B981')
                ->draggable()
                ->onCardMove('handleCardMove')
                ->dataUrl('/api/kanban/project-123')
                ->cardTemplate([
                    'title' => 'string',
                    'description' => 'text',
                    'assignee' => 'user',
                    'priority' => 'select',
                    'due_date' => 'date',
                ])
                ->label('Project Tasks')
        );
    });

// ============================================================================
// MORE REAL-WORLD EXAMPLES
// ============================================================================

// Example: Chat Component
class ChatComponent extends \Litepie\Layout\Components\BaseComponent
{
    protected ?string $channelId = null;

    protected array $messages = [];

    protected bool $sendButton = true;

    protected bool $attachments = true;

    protected bool $emojis = true;

    protected bool $typing = true;

    protected ?int $maxLength = 1000;

    public function __construct(string $name, string $type = 'chat')
    {
        parent::__construct($name, $type);
    }

    public static function make(string $name): self
    {
        return new static($name);
    }

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

    public function emojis(bool $enabled = true): self
    {
        $this->emojis = $enabled;

        return $this;
    }

    public function typing(bool $enabled = true): self
    {
        $this->typing = $enabled;

        return $this;
    }

    public function maxLength(int $length): self
    {
        $this->maxLength = $length;

        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'channel_id' => $this->channelId,
            'messages' => $this->messages,
            'send_button' => $this->sendButton,
            'attachments' => $this->attachments,
            'emojis' => $this->emojis,
            'typing' => $this->typing,
            'max_length' => $this->maxLength,
        ]);
    }
}

// Example: 3D Model Viewer Component
class ModelViewerComponent extends \Litepie\Layout\Components\BaseComponent
{
    protected ?string $modelUrl = null;

    protected ?string $format = 'gltf'; // gltf, obj, fbx

    protected bool $autoRotate = false;

    protected bool $controls = true;

    protected ?string $backgroundColor = '#ffffff';

    protected array $camera = ['position' => [0, 0, 5]];

    protected array $lighting = ['ambient' => 0.5, 'directional' => 0.8];

    public function __construct(string $name, string $type = '3d-viewer')
    {
        parent::__construct($name, $type);
    }

    public static function make(string $name): self
    {
        return new static($name);
    }

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

    public function controls(bool $enabled = true): self
    {
        $this->controls = $enabled;

        return $this;
    }

    public function backgroundColor(string $color): self
    {
        $this->backgroundColor = $color;

        return $this;
    }

    public function camera(array $config): self
    {
        $this->camera = array_merge($this->camera, $config);

        return $this;
    }

    public function lighting(float $ambient, float $directional): self
    {
        $this->lighting = [
            'ambient' => $ambient,
            'directional' => $directional,
        ];

        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'model_url' => $this->modelUrl,
            'format' => $this->format,
            'auto_rotate' => $this->autoRotate,
            'controls' => $this->controls,
            'background_color' => $this->backgroundColor,
            'camera' => $this->camera,
            'lighting' => $this->lighting,
        ]);
    }
}

// Usage of custom components together
$layout4 = LayoutBuilder::create('mixed-custom', 'custom-showcase')
    ->title('Custom Components Showcase')

    ->section('communication', function ($section) {
        $section->grid('chat-grid')
            ->columns(2)
            ->gap('1.5rem')

            // Chat component
            ->addComponent(
                ChatComponent::make('team-chat')
                    ->channel('team-general')
                    ->attachments()
                    ->emojis()
                    ->typing()
                    ->maxLength(500)
                    ->label('Team Chat')
            )

            // 3D Model viewer
            ->addComponent(
                ModelViewerComponent::make('product-model')
                    ->model('/models/product.gltf', 'gltf')
                    ->autoRotate()
                    ->controls()
                    ->backgroundColor('#f0f0f0')
                    ->camera(['position' => [0, 2, 5]])
                    ->lighting(0.6, 0.9)
                    ->label('Product 3D View')
            );
    });

// ============================================================================
// INTEGRATION WITH SectionContainer (Optional Helper Method)
// ============================================================================

/**
 * To make custom components easier to use, you can add helper methods
 * to SectionContainer. For example, in src/Helpers/SectionContainer.php:
 *
 * public function videoPlayer(string $name): VideoPlayerComponent
 * {
 *     $component = VideoPlayerComponent::make($name);
 *     $this->components[] = $component;
 *     return $component;
 * }
 *
 * public function kanban(string $name): KanbanBoardSection
 * {
 *     $component = KanbanBoardSection::make($name);
 *     $this->components[] = $component;
 *     return $component;
 * }
 *
 * Then use them like this:
 */
$layout5 = LayoutBuilder::create('with-helpers', 'helpers-demo')
    ->title('Using Custom Component Helpers')

    ->section('video-section', function ($section) {
        // If helper method is added to SectionContainer
        // $section->videoPlayer('demo-video')
        //     ->videoUrl('/videos/demo.mp4')
        //     ->controls();

        // Without helper, use addComponent
        $section->addComponent(
            VideoPlayerComponent::make('demo-video')
                ->videoUrl('/videos/demo.mp4')
                ->controls()
        );
    });

// ============================================================================
// FRONTEND INTEGRATION PATTERNS
// ============================================================================

/**
 * React Component Example (components/VideoPlayer.jsx):
 *
 * import React from 'react';
 *
 * export const VideoPlayer = ({
 *   video_url,
 *   poster_url,
 *   autoplay,
 *   controls,
 *   tracks,
 *   chapters
 * }) => {
 *   return (
 *     <div className="video-player">
 *       <video
 *         src={video_url}
 *         poster={poster_url}
 *         autoPlay={autoplay}
 *         controls={controls}
 *       >
 *         {tracks.map((track, idx) => (
 *           <track
 *             key={idx}
 *             src={track.src}
 *             kind={track.kind}
 *             label={track.label}
 *             srcLang={track.srclang}
 *           />
 *         ))}
 *       </video>
 *       {chapters && (
 *         <div className="chapters">
 *           {chapters.map((chapter, idx) => (
 *             <button key={idx} onClick={() => seekTo(chapter.time)}>
 *               {chapter.title}
 *             </button>
 *           ))}
 *         </div>
 *       )}
 *     </div>
 *   );
 * };
 */

/**
 * Vue Component Example (components/VideoPlayer.vue):
 *
 * <template>
 *   <div class="video-player">
 *     <video
 *       :src="video_url"
 *       :poster="poster_url"
 *       :autoplay="autoplay"
 *       :controls="controls"
 *     >
 *       <track
 *         v-for="(track, idx) in tracks"
 *         :key="idx"
 *         :src="track.src"
 *         :kind="track.kind"
 *         :label="track.label"
 *         :srclang="track.srclang"
 *       />
 *     </video>
 *     <div v-if="chapters" class="chapters">
 *       <button
 *         v-for="(chapter, idx) in chapters"
 *         :key="idx"
 *
 *         @click="seekTo(chapter.time)"
 *       >
 *         {{ chapter.title }}
 *       </button>
 *     </div>
 *   </div>
 * </template>
 *
 * <script>
 * export default {
 *   props: ['video_url', 'poster_url', 'autoplay', 'controls', 'tracks', 'chapters'],
 *   methods: {
 *     seekTo(time) {
 *       this.$el.querySelector('video').currentTime = time;
 *     }
 *   }
 * }
 * </script>
 */

// ============================================================================
// BEST PRACTICES SUMMARY
// ============================================================================

/**
 * 1. Component Naming
 *    - Use descriptive, action-oriented names
 *    - Follow PascalCase for class names (VideoPlayerComponent)
 *    - Use kebab-case for component names ('video-player')
 *
 * 2. Method Chaining
 *    - Always return $this from setter methods
 *    - Keep method names concise and clear
 *    - Group related methods logically
 *
 * 3. Data Structure
 *    - Use toArray() to serialize all properties
 *    - Convert snake_case for JSON output
 *    - Include all configurable properties
 *
 * 4. Default Values
 *    - Set sensible defaults in constructor
 *    - Make optional features opt-in
 *    - Document default behavior
 *
 * 5. Validation
 *    - Use Validatable trait for form components
 *    - Add validation rules where appropriate
 *    - Provide clear error messages
 *
 * 6. Permissions
 *    - Use permissions() for access control
 *    - Apply roles() for role-based access
 *    - Consider data-level permissions
 *
 * 7. Data Loading
 *    - Use dataUrl() for dynamic content
 *    - Support dataParams() for filtering
 *    - Implement dataTransform() for processing
 *
 * 8. Frontend Integration
 *    - Match property names in frontend components
 *    - Use component() to specify frontend component
 *    - Provide view() for Blade rendering
 *
 * 9. Documentation
 *    - Add PHPDoc blocks to classes and methods
 *    - Create usage examples in examples/
 *    - Document complex features thoroughly
 *
 * 10. Testing
 *     - Write tests for custom components
 *     - Test edge cases and error conditions
 *     - Use LayoutAssertions trait
 */

// Return the layout
return $layout4;
