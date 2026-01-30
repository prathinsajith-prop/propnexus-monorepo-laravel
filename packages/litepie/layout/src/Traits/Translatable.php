<?php

namespace Litepie\Layout\Traits;

use Litepie\Layout\I18n\Translator;

trait Translatable
{
    protected ?Translator $translator = null;

    protected ?string $locale = null;

    protected bool $autoTranslate = false;

    /**
     * Set locale for this component
     */
    public function locale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Enable auto-translation of title, subtitle, etc.
     */
    public function autoTranslate(bool $enabled = true): self
    {
        $this->autoTranslate = $enabled;

        return $this;
    }

    /**
     * Get translator instance
     */
    protected function getTranslator(): Translator
    {
        if ($this->translator === null) {
            $this->translator = new Translator($this->locale);
        }

        return $this->translator;
    }

    /**
     * Translate a string
     */
    protected function trans(string $key, array $replace = []): string
    {
        return $this->getTranslator()->translate($key, $replace, $this->locale);
    }

    /**
     * Translate with choice
     */
    protected function transChoice(string $key, int $count, array $replace = []): string
    {
        return $this->getTranslator()->choice($key, $count, $replace, $this->locale);
    }

    /**
     * Translate properties if auto-translate is enabled
     */
    protected function translateProperties(): void
    {
        if (! $this->autoTranslate) {
            return;
        }

        // Translate title if it looks like a translation key
        if ($this->title && str_contains($this->title, '.')) {
            $translated = $this->trans($this->title);
            if ($translated !== $this->title) {
                $this->title = $translated;
            }
        }

        // Translate subtitle
        if ($this->subtitle && str_contains($this->subtitle, '.')) {
            $translated = $this->trans($this->subtitle);
            if ($translated !== $this->subtitle) {
                $this->subtitle = $translated;
            }
        }
    }

    /**
     * Add locale to output array
     */
    protected function addLocaleToArray(array $array): array
    {
        if ($this->locale) {
            $array['locale'] = $this->locale;
        }

        return $array;
    }
}
