<?php

namespace Litepie\Layout\I18n;

use Illuminate\Support\Facades\App;

class Translator
{
    protected ?string $locale = null;

    protected ?string $fallbackLocale = null;

    protected string $namespace = 'layout';

    public function __construct(?string $locale = null, ?string $fallbackLocale = null)
    {
        $this->locale = $locale ?? App::getLocale();
        $this->fallbackLocale = $fallbackLocale ?? config('app.fallback_locale', 'en');
    }

    /**
     * Translate a key
     */
    public function translate(string $key, array $replace = [], ?string $locale = null): string
    {
        $locale = $locale ?? $this->locale;

        // If key contains namespace, use it
        if (str_contains($key, '::')) {
            return __($key, $replace, $locale);
        }

        // Otherwise, use layout namespace
        return __("{$this->namespace}::{$key}", $replace, $locale);
    }

    /**
     * Translate with choice
     */
    public function choice(string $key, int $count, array $replace = [], ?string $locale = null): string
    {
        $locale = $locale ?? $this->locale;

        if (str_contains($key, '::')) {
            return trans_choice($key, $count, $replace, $locale);
        }

        return trans_choice("{$this->namespace}::{$key}", $count, $replace, $locale);
    }

    /**
     * Set locale
     */
    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get locale
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * Set namespace
     */
    public function setNamespace(string $namespace): self
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * Check if translation exists
     */
    public function has(string $key, ?string $locale = null): bool
    {
        $locale = $locale ?? $this->locale;

        if (str_contains($key, '::')) {
            return __($key, [], $locale) !== $key;
        }

        $fullKey = "{$this->namespace}::{$key}";

        return __($fullKey, [], $locale) !== $fullKey;
    }
}
