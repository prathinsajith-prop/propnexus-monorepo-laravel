<?php

namespace Litepie\Layout\Components;

class MediaComponent extends BaseComponent
{
    protected string $mediaType = 'image'; // image, video, gallery, audio

    protected string $layout = 'grid'; // grid, masonry, carousel

    protected int $mediaColumns = 3;

    protected string $aspectRatio = '16:9';

    protected bool $lightbox = true;

    protected bool $captions = true;

    protected array $items = []; // Item configurations

    // Media source URLs for different types
    protected ?string $imageUrl = null;

    protected ?string $videoUrl = null;

    protected ?string $audioUrl = null;

    protected ?string $posterUrl = null;

    protected ?string $altText = null;

    protected ?string $title = null;

    protected bool|string $rounded = false;

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

    protected float $playbackRate = 1.0;

    protected ?float $volume = 0.8;

    protected string $preload = 'metadata'; // none, metadata, auto

    protected bool $responsive = true;

    protected ?string $thumbnailUrl = null;

    protected array $sources = []; // Multiple source formats

    protected bool $downloadable = false;

    protected ?string $downloadUrl = null;

    protected bool $showDuration = true;

    protected bool $showCurrentTime = true;

    protected bool $showProgress = true;

    protected bool $showVolume = true;

    protected bool $keyboard = true; // Keyboard controls

    protected ?string $crossOrigin = null; // anonymous, use-credentials

    protected array $config = []; // Custom player config

    public function __construct(string $name)
    {
        parent::__construct($name, 'media');
    }

    public static function make(string $name): self
    {
        return new static($name);
    }

    public function mediaType(string $type): self
    {
        $this->mediaType = $type;

        return $this;
    }

    public function image(): self
    {
        return $this->mediaType('image');
    }

    public function video(): self
    {
        return $this->mediaType('video');
    }

    public function gallery(): self
    {
        return $this->mediaType('gallery');
    }

    public function audio(): self
    {
        return $this->mediaType('audio');
    }

    public function layout(string $layout): self
    {
        $this->layout = $layout;

        return $this;
    }

    public function grid(): self
    {
        return $this->layout('grid');
    }

    public function masonry(): self
    {
        return $this->layout('masonry');
    }

    public function carousel(): self
    {
        return $this->layout('carousel');
    }

    public function columns(int $columns): self
    {
        $this->mediaColumns = $columns;

        return $this;
    }

    public function aspectRatio(string $ratio): self
    {
        $this->aspectRatio = $ratio;

        return $this;
    }

    public function lightbox(bool $lightbox = true): self
    {
        $this->lightbox = $lightbox;

        return $this;
    }

    public function captions(bool $captions = true): self
    {
        $this->captions = $captions;

        return $this;
    }

    /**
     * Add media item configuration
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

    // ===== Video Player Methods =====

    public function videoUrl(string $url): self
    {
        $this->videoUrl = $url;
        $this->mediaType = 'video'; // Auto-set media type to video

        return $this;
    }

    /**
     * Universal source method - sets the appropriate URL based on media type
     * Works for images, videos, and audio
     */
    public function src(string $url): self
    {
        // Set the appropriate URL based on current media type
        switch ($this->mediaType) {
            case 'image':
            case 'gallery':
                $this->imageUrl = $url;
                break;
            case 'video':
                $this->videoUrl = $url;
                break;
            case 'audio':
                $this->audioUrl = $url;
                break;
            default:
                // Default to image if type not set
                $this->imageUrl = $url;
                $this->mediaType = 'image';
                break;
        }

        return $this;
    }

    /**
     * Set image URL (for image media type)
     */
    public function imageUrl(string $url): self
    {
        $this->imageUrl = $url;
        $this->mediaType = 'image';

        return $this;
    }

    /**
     * Set audio URL (for audio media type)
     */
    public function audioUrl(string $url): self
    {
        $this->audioUrl = $url;
        $this->mediaType = 'audio';

        return $this;
    }

    public function poster(string $url): self
    {
        $this->posterUrl = $url;

        return $this;
    }

    public function thumbnail(string $url): self
    {
        $this->thumbnailUrl = $url;

        return $this;
    }

    /**
     * Set alt text for accessibility (important for images)
     */
    public function alt(string $text): self
    {
        $this->altText = $text;

        return $this;
    }

    /**
     * Set title attribute
     */
    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Set rounded corners/border radius for the media element
     * 
     * @param bool|string $size Can be true for default rounding, or a size string like 'sm', 'md', 'lg', 'xl', 'full', 'none'
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

    public function width(int $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function height(int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function addTrack(string $src, string $kind = 'subtitles', string $label = '', string $srclang = ''): self
    {
        $this->tracks[] = [
            'src' => $src,
            'kind' => $kind, // subtitles, captions, descriptions, chapters, metadata
            'label' => $label,
            'srclang' => $srclang,
        ];

        return $this;
    }

    public function tracks(array $tracks): self
    {
        $this->tracks = $tracks;

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

    public function chapters(array $chapters): self
    {
        $this->chapters = $chapters;

        return $this;
    }

    public function pip(bool $pip = true): self
    {
        $this->pip = $pip;

        return $this;
    }

    public function fullscreen(bool $fullscreen = true): self
    {
        $this->fullscreen = $fullscreen;

        return $this;
    }

    public function playbackRate(float $rate): self
    {
        $this->playbackRate = $rate;

        return $this;
    }

    public function volume(float $volume): self
    {
        $this->volume = max(0, min(1, $volume)); // Clamp between 0 and 1

        return $this;
    }

    public function preload(string $preload): self
    {
        if (!in_array($preload, ['none', 'metadata', 'auto'])) {
            throw new \InvalidArgumentException("Preload must be 'none', 'metadata', or 'auto'");
        }

        $this->preload = $preload;

        return $this;
    }

    public function responsive(bool $responsive = true): self
    {
        $this->responsive = $responsive;

        return $this;
    }

    public function addSource(string $src, string $type, ?string $quality = null): self
    {
        $this->sources[] = [
            'src' => $src,
            'type' => $type, // video/mp4, video/webm, video/ogg
            'quality' => $quality, // 1080p, 720p, 480p, 360p
        ];

        return $this;
    }

    public function sources(array $sources): self
    {
        $this->sources = $sources;

        return $this;
    }

    public function downloadable(bool $downloadable = true): self
    {
        $this->downloadable = $downloadable;

        return $this;
    }

    public function downloadUrl(string $url): self
    {
        $this->downloadUrl = $url;
        $this->downloadable = true;

        return $this;
    }

    public function showDuration(bool $show = true): self
    {
        $this->showDuration = $show;

        return $this;
    }

    public function showCurrentTime(bool $show = true): self
    {
        $this->showCurrentTime = $show;

        return $this;
    }

    public function showProgress(bool $show = true): self
    {
        $this->showProgress = $show;

        return $this;
    }

    public function showVolume(bool $show = true): self
    {
        $this->showVolume = $show;

        return $this;
    }

    public function keyboard(bool $keyboard = true): self
    {
        $this->keyboard = $keyboard;

        return $this;
    }

    public function crossOrigin(?string $crossOrigin): self
    {
        if ($crossOrigin !== null && !in_array($crossOrigin, ['anonymous', 'use-credentials'])) {
            throw new \InvalidArgumentException("crossOrigin must be 'anonymous' or 'use-credentials'");
        }

        $this->crossOrigin = $crossOrigin;

        return $this;
    }

    public function config(array $config): self
    {
        $this->config = array_merge($this->config, $config);

        return $this;
    }

    public function toArray(): array
    {
        $baseArray = array_merge($this->getCommonProperties(), $this->filterNullValues([
            'media_type' => $this->mediaType,
            'layout' => $this->layout,
            'columns' => $this->mediaColumns,
            'aspect_ratio' => $this->aspectRatio,
            'lightbox' => $this->lightbox,
            'captions' => $this->captions,
            'items' => $this->items,
            'alt_text' => $this->altText,
            'title' => $this->title,
            'rounded' => $this->rounded,
        ]));

        // Add type-specific URLs
        if ($this->mediaType === 'image' || $this->mediaType === 'gallery') {
            $baseArray = array_merge($baseArray, $this->filterNullValues([
                'image_url' => $this->imageUrl,
                'width' => $this->width,
                'height' => $this->height,
            ]));
        }

        // Add video-specific properties if media type is video
        if ($this->mediaType === 'video') {
            $baseArray = array_merge($baseArray, $this->filterNullValues([
                'video_url' => $this->videoUrl,
                'poster_url' => $this->posterUrl,
                'thumbnail_url' => $this->thumbnailUrl,
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
                'volume' => $this->volume,
                'preload' => $this->preload,
                'responsive' => $this->responsive,
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
        }

        // Add audio-specific properties if media type is audio
        if ($this->mediaType === 'audio') {
            $baseArray = array_merge($baseArray, $this->filterNullValues([
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
        }

        return $baseArray;
    }
}
