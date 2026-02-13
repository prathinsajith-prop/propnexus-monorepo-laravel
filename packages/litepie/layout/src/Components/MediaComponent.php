<?php

namespace Litepie\Layout\Components;

/**
 * MediaComponent
 * 
 * Versatile media component supporting images, videos, audio, and galleries.
 * Provides comprehensive configuration for display, playback, and editing.
 * 
 * @package Litepie\Layout\Components
 */
class MediaComponent extends BaseComponent
{
    /** @var string Media type: image, video, audio, gallery */
    protected string $mediaType = 'image';

    /** @var string Layout type: grid, masonry, carousel */
    protected string $layout = 'grid';

    /** @var int Number of columns for grid layout */
    protected int $mediaColumns = 3;

    /** @var string Aspect ratio (e.g., 16:9, 4:3, 1:1) */
    protected string $aspectRatio = '16:9';

    /** @var bool Enable lightbox modal for images */
    protected bool $lightbox = true;

    /** @var bool Show captions for media items */
    protected bool $captions = true;

    /** @var array Gallery items configuration */
    protected array $items = [];

    // ===== COMMON PROPERTIES =====

    /** @var string|null Alternative text for accessibility */
    protected ?string $altText = null;

    /** @var string|null Media title */
    protected ?string $title = null;

    /** @var bool|string Border radius */
    protected bool|string $rounded = false;

    /** @var int|null Media width in pixels */
    protected ?int $width = null;

    /** @var int|null Media height in pixels */
    protected ?int $height = null;

    /** @var bool Responsive sizing */
    protected bool $responsive = true;

    // ===== IMAGE PROPERTIES =====

    /** @var string|null Image source URL */
    protected ?string $imageUrl = null;

    /** @var string|null Thumbnail URL for preview */
    protected ?string $thumbnailUrl = null;

    // ===== VIDEO PROPERTIES =====

    /** @var string|null Video source URL */
    protected ?string $videoUrl = null;

    /** @var string|null Poster image URL */
    protected ?string $posterUrl = null;

    /** @var bool Auto-play video */
    protected bool $autoplay = false;

    /** @var bool Show video controls */
    protected bool $controls = true;

    /** @var bool Loop playback */
    protected bool $loop = false;

    /** @var bool Mute audio */
    protected bool $muted = false;

    /** @var array Subtitle/caption tracks */
    protected array $tracks = [];

    /** @var string Video quality setting */
    protected ?string $quality = 'auto';

    /** @var array Chapter markers */
    protected array $chapters = [];

    /** @var bool Enable picture-in-picture */
    protected bool $pip = true;

    /** @var bool Enable fullscreen mode */
    protected bool $fullscreen = true;

    /** @var float Playback speed multiplier */
    protected float $playbackRate = 1.0;

    /** @var float|null Volume level (0.0 to 1.0) */
    protected ?float $volume = 0.8;

    /** @var string Preload strategy: none, metadata, auto */
    protected string $preload = 'metadata';

    /** @var array Multiple video source formats */
    protected array $sources = [];

    /** @var bool Allow media download */
    protected bool $downloadable = false;

    /** @var string|null Download URL */
    protected ?string $downloadUrl = null;

    /** @var bool Show video duration */
    protected bool $showDuration = true;

    /** @var bool Show current time */
    protected bool $showCurrentTime = true;

    /** @var bool Show progress bar */
    protected bool $showProgress = true;

    /** @var bool Show volume control */
    protected bool $showVolume = true;

    /** @var bool Enable keyboard controls */
    protected bool $keyboard = true;

    /** @var string|null CORS setting: anonymous, use-credentials */
    protected ?string $crossOrigin = null;

    /** @var array Custom player configuration */
    protected array $config = [];

    // ===== AUDIO PROPERTIES =====

    /** @var string|null Audio source URL */
    protected ?string $audioUrl = null;

    // ===== EDIT FORM =====

    /** @var FormComponent|null Form component for editing media */
    protected ?FormComponent $editForm = null;

    // ===== CONSTRUCTOR & FACTORY =====

    public function __construct(string $name)
    {
        parent::__construct($name, 'media');
    }

    public static function make(string $name): self
    {
        return new static($name);
    }

    // ===== MEDIA TYPE METHODS =====

    /**
     * Set media type
     */
    public function mediaType(string $type): self
    {
        $this->mediaType = $type;
        return $this;
    }

    /**
     * Set media type to image
     */
    public function image(): self
    {
        return $this->mediaType('image');
    }

    /**
     * Set media type to video
     */
    public function video(): self
    {
        return $this->mediaType('video');
    }

    /**
     * Set media type to gallery
     */
    public function gallery(): self
    {
        return $this->mediaType('gallery');
    }

    /**
     * Set media type to audio
     */
    public function audio(): self
    {
        return $this->mediaType('audio');
    }

    // ===== LAYOUT METHODS =====

    /**
     * Set layout type
     */
    public function layout(string $layout): self
    {
        $this->layout = $layout;
        return $this;
    }

    /**
     * Set layout to grid
     */
    public function grid(): self
    {
        return $this->layout('grid');
    }

    /**
     * Set layout to masonry
     */
    public function masonry(): self
    {
        return $this->layout('masonry');
    }

    /**
     * Set layout to carousel
     */
    public function carousel(): self
    {
        return $this->layout('carousel');
    }

    /**
     * Set number of columns for grid layout
     */
    public function columns(int $columns): self
    {
        $this->mediaColumns = $columns;
        return $this;
    }

    // ===== COMMON MEDIA METHODS =====

    /**
     * Universal source method - auto-detects media type
     */
    public function src(string $url): self
    {
        switch ($this->mediaType) {
            case 'video':
                $this->videoUrl = $url;
                break;
            case 'audio':
                $this->audioUrl = $url;
                break;
            default:
                $this->imageUrl = $url;
                $this->mediaType = 'image';
                break;
        }
        return $this;
    }

    /**
     * Set aspect ratio
     */
    public function aspectRatio(string $ratio): self
    {
        $this->aspectRatio = $ratio;
        return $this;
    }

    /**
     * Enable/disable lightbox modal
     */
    public function lightbox(bool $lightbox = true): self
    {
        $this->lightbox = $lightbox;
        return $this;
    }

    /**
     * Enable/disable captions
     */
    public function captions(bool $captions = true): self
    {
        $this->captions = $captions;
        return $this;
    }

    /**
     * Set alt text for accessibility
     */
    public function alt(string $text): self
    {
        $this->altText = $text;
        return $this;
    }

    /**
     * Set media title
     */
    public function title(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Set border radius
     * 
     * @param bool|string $size true, false, or size (sm, md, lg, xl, full)
     */
    public function rounded(bool|string $size = true): self
    {
        if ($size === true) {
            $this->rounded = 'default';
        } elseif ($size === false) {
            $this->rounded = false;
        } else {
            $this->rounded = $size;
        }
        return $this;
    }

    /**
     * Set width and height
     */
    public function dimensions(int $width, int $height): self
    {
        $this->width = $width;
        $this->height = $height;
        return $this;
    }

    /**
     * Set width
     */
    public function width(int $width): self
    {
        $this->width = $width;
        return $this;
    }

    /**
     * Set height
     */
    public function height(int $height): self
    {
        $this->height = $height;
        return $this;
    }

    /**
     * Enable/disable responsive sizing
     */
    public function responsive(bool $responsive = true): self
    {
        $this->responsive = $responsive;
        return $this;
    }

    // ===== IMAGE-SPECIFIC METHODS =====

    /**
     * Set image URL
     */
    public function imageUrl(string $url): self
    {
        $this->imageUrl = $url;
        $this->mediaType = 'image';
        return $this;
    }

    /**
     * Set thumbnail URL
     */
    public function thumbnail(string $url): self
    {
        $this->thumbnailUrl = $url;
        return $this;
    }

    // ===== VIDEO-SPECIFIC METHODS =====

    /**
     * Set video URL
     */
    public function videoUrl(string $url): self
    {
        $this->videoUrl = $url;
        $this->mediaType = 'video';
        return $this;
    }

    /**
     * Set poster image
     */
    public function poster(string $url): self
    {
        $this->posterUrl = $url;
        return $this;
    }

    /**
     * Enable/disable autoplay
     */
    public function autoplay(bool $autoplay = true): self
    {
        $this->autoplay = $autoplay;
        return $this;
    }

    /**
     * Enable/disable video controls
     */
    public function controls(bool $controls = true): self
    {
        $this->controls = $controls;
        return $this;
    }

    /**
     * Enable/disable loop
     */
    public function loop(bool $loop = true): self
    {
        $this->loop = $loop;
        return $this;
    }

    /**
     * Enable/disable mute
     */
    public function muted(bool $muted = true): self
    {
        $this->muted = $muted;
        return $this;
    }

    /**
     * Add subtitle/caption track
     */
    public function addTrack(string $src, string $kind = 'subtitles', string $label = '', string $srclang = ''): self
    {
        $this->tracks[] = [
            'src' => $src,
            'kind' => $kind,
            'label' => $label,
            'srclang' => $srclang,
        ];
        return $this;
    }

    /**
     * Set all tracks
     */
    public function tracks(array $tracks): self
    {
        $this->tracks = $tracks;
        return $this;
    }

    /**
     * Set video quality
     */
    public function quality(string $quality): self
    {
        $this->quality = $quality;
        return $this;
    }

    /**
     * Add chapter marker
     */
    public function addChapter(string $title, float $time): self
    {
        $this->chapters[] = [
            'title' => $title,
            'time' => $time,
        ];
        return $this;
    }

    /**
     * Set all chapters
     */
    public function chapters(array $chapters): self
    {
        $this->chapters = $chapters;
        return $this;
    }

    /**
     * Enable/disable picture-in-picture
     */
    public function pip(bool $pip = true): self
    {
        $this->pip = $pip;
        return $this;
    }

    /**
     * Enable/disable fullscreen
     */
    public function fullscreen(bool $fullscreen = true): self
    {
        $this->fullscreen = $fullscreen;
        return $this;
    }

    /**
     * Set playback speed
     */
    public function playbackRate(float $rate): self
    {
        $this->playbackRate = $rate;
        return $this;
    }

    /**
     * Set volume level (0.0 to 1.0)
     */
    public function volume(float $volume): self
    {
        $this->volume = max(0, min(1, $volume));
        return $this;
    }

    /**
     * Set preload strategy
     */
    public function preload(string $preload): self
    {
        if (!in_array($preload, ['none', 'metadata', 'auto'])) {
            throw new \InvalidArgumentException("Preload must be 'none', 'metadata', or 'auto'");
        }
        $this->preload = $preload;
        return $this;
    }

    /**
     * Add video source format
     */
    public function addSource(string $src, string $type, ?string $quality = null): self
    {
        $this->sources[] = [
            'src' => $src,
            'type' => $type,
            'quality' => $quality,
        ];
        return $this;
    }

    /**
     * Set all video sources
     */
    public function sources(array $sources): self
    {
        $this->sources = $sources;
        return $this;
    }

    /**
     * Enable/disable download
     */
    public function downloadable(bool $downloadable = true): self
    {
        $this->downloadable = $downloadable;
        return $this;
    }

    /**
     * Set download URL
     */
    public function downloadUrl(string $url): self
    {
        $this->downloadUrl = $url;
        $this->downloadable = true;
        return $this;
    }

    /**
     * Show/hide duration display
     */
    public function showDuration(bool $show = true): self
    {
        $this->showDuration = $show;
        return $this;
    }

    /**
     * Show/hide current time display
     */
    public function showCurrentTime(bool $show = true): self
    {
        $this->showCurrentTime = $show;
        return $this;
    }

    /**
     * Show/hide progress bar
     */
    public function showProgress(bool $show = true): self
    {
        $this->showProgress = $show;
        return $this;
    }

    /**
     * Show/hide volume control
     */
    public function showVolume(bool $show = true): self
    {
        $this->showVolume = $show;
        return $this;
    }

    /**
     * Enable/disable keyboard controls
     */
    public function keyboard(bool $keyboard = true): self
    {
        $this->keyboard = $keyboard;
        return $this;
    }

    /**
     * Set CORS policy
     */
    public function crossOrigin(?string $crossOrigin): self
    {
        if ($crossOrigin !== null && !in_array($crossOrigin, ['anonymous', 'use-credentials'])) {
            throw new \InvalidArgumentException("crossOrigin must be 'anonymous' or 'use-credentials'");
        }
        $this->crossOrigin = $crossOrigin;
        return $this;
    }

    /**
     * Set custom player configuration
     */
    public function config(array $config): self
    {
        $this->config = array_merge($this->config, $config);
        return $this;
    }

    // ===== AUDIO-SPECIFIC METHODS =====

    /**
     * Set audio URL
     */
    public function audioUrl(string $url): self
    {
        $this->audioUrl = $url;
        $this->mediaType = 'audio';
        return $this;
    }

    // ===== GALLERY-SPECIFIC METHODS =====

    /**
     * Add gallery item
     */
    public function addItem(string $key, array $options = []): self
    {
        $this->items[] = [
            'key' => $key,
            'alt' => $options['alt'] ?? null,
            'caption' => $options['caption'] ?? null,
        ];
        return $this;
    }

    // ===== EDIT FORM METHODS =====

    /**
     * Attach a FormComponent for editing this media
     * 
     * @param FormComponent $form The form component to use for editing
     * @return self
     */
    public function edit(FormComponent $form): self
    {
        $this->editForm = $form;
        return $this;
    }

    // ===== SERIALIZATION =====

    /**
     * Convert component to array
     */
    public function toArray(): array
    {
        // Base properties
        $data = array_merge($this->getCommonProperties(), [
            'media_type' => $this->mediaType,
            'layout' => $this->layout,
            'columns' => $this->mediaColumns,
            'aspect_ratio' => $this->aspectRatio,
            'lightbox' => $this->lightbox,
            'captions' => $this->captions,
            'rounded' => $this->rounded,
            'responsive' => $this->responsive,
        ]);

        // Add common optional properties
        $data = array_merge($data, $this->filterNullValues([
            'alt_text' => $this->altText,
            'title' => $this->title,
            'width' => $this->width,
            'height' => $this->height,
        ]));

        // Add type-specific properties
        switch ($this->mediaType) {
            case 'image':
                $data = array_merge($data, $this->filterNullValues([
                    'image_url' => $this->imageUrl,
                    'thumbnail_url' => $this->thumbnailUrl,
                ]));
                break;

            case 'gallery':
                $data = array_merge($data, $this->filterNullValues([
                    'items' => $this->items,
                    'image_url' => $this->imageUrl,
                ]));
                break;

            case 'video':
                $data = array_merge($data, $this->filterNullValues([
                    'video_url' => $this->videoUrl,
                    'poster_url' => $this->posterUrl,
                    'thumbnail_url' => $this->thumbnailUrl,
                    'autoplay' => $this->autoplay,
                    'controls' => $this->controls,
                    'loop' => $this->loop,
                    'muted' => $this->muted,
                    'tracks' => $this->tracks,
                    'quality' => $this->quality,
                    'chapters' => $this->chapters,
                    'pip' => $this->pip,
                    'fullscreen' => $this->fullscreen,
                    'playback_rate' => $this->playbackRate,
                    'volume' => $this->volume,
                    'preload' => $this->preload,
                    'sources' => $this->sources,
                    'downloadable' => $this->downloadable,
                    'download_url' => $this->downloadUrl,
                    'show_duration' => $this->showDuration,
                    'show_current_time' => $this->showCurrentTime,
                    'show_progress' => $this->showProgress,
                    'show_volume' => $this->showVolume,
                    'keyboard' => $this->keyboard,
                    'cross_origin' => $this->crossOrigin,
                    'config' => $this->config,
                ]));
                break;

            case 'audio':
                $data = array_merge($data, $this->filterNullValues([
                    'audio_url' => $this->audioUrl,
                    'autoplay' => $this->autoplay,
                    'controls' => $this->controls,
                    'loop' => $this->loop,
                    'muted' => $this->muted,
                    'volume' => $this->volume,
                    'preload' => $this->preload,
                    'sources' => $this->sources,
                    'downloadable' => $this->downloadable,
                    'download_url' => $this->downloadUrl,
                ]));
                break;
        }

        // Add edit form if present
        if ($this->editForm) {
            $data['edit'] = $this->editForm->toArray();
        }

        return $data;
    }
}
