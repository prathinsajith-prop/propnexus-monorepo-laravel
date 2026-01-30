<?php

namespace Litepie\Layout\Components;

/**
 * CodeComponent
 *
 * Component for displaying syntax-highlighted code blocks with various programming languages.
 * Supports features like line numbers, highlighting specific lines, themes, and copy-to-clipboard.
 */
class CodeComponent extends BaseComponent
{
    protected ?string $content = null;

    protected ?string $language = null;

    protected ?string $theme = null;

    protected bool $lineNumbers = true;

    protected array $highlightLines = [];

    protected bool $copyButton = true;

    protected ?string $filename = null;

    protected bool $wrap = false;

    protected ?int $maxHeight = null;

    protected ?int $startLine = null;

    public function __construct(string $name)
    {
        parent::__construct($name, 'code');
    }

    public static function make(string $name): self
    {
        return new static($name);
    }

    /**
     * Set the code content
     *
     * @param string $content The code to display
     * @return self
     */
    public function content(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Set the programming language for syntax highlighting
     *
     * @param string $language Language identifier (e.g., 'php', 'javascript', 'python', 'bash', 'sql')
     * @return self
     */
    public function language(string $language): self
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Set the color theme for syntax highlighting
     *
     * @param string $theme Theme name (e.g., 'light', 'dark', 'monokai', 'github', 'dracula')
     * @return self
     */
    public function theme(string $theme): self
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Enable or disable line numbers
     *
     * @param bool $lineNumbers Whether to show line numbers
     * @return self
     */
    public function lineNumbers(bool $lineNumbers = true): self
    {
        $this->lineNumbers = $lineNumbers;

        return $this;
    }

    /**
     * Highlight specific lines
     *
     * @param array|int $lines Line numbers to highlight (1-based)
     * @return self
     */
    public function highlightLines(array|int $lines): self
    {
        $this->highlightLines = is_array($lines) ? $lines : [$lines];

        return $this;
    }

    /**
     * Enable or disable copy-to-clipboard button
     *
     * @param bool $copyButton Whether to show copy button
     * @return self
     */
    public function copyButton(bool $copyButton = true): self
    {
        $this->copyButton = $copyButton;

        return $this;
    }

    /**
     * Set the filename to display
     *
     * @param string $filename Filename or path to display
     * @return self
     */
    public function filename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Enable or disable line wrapping
     *
     * @param bool $wrap Whether to wrap long lines
     * @return self
     */
    public function wrap(bool $wrap = true): self
    {
        $this->wrap = $wrap;

        return $this;
    }

    /**
     * Set maximum height for scrollable code blocks
     *
     * @param int $maxHeight Maximum height in pixels
     * @return self
     */
    public function maxHeight(int $maxHeight): self
    {
        $this->maxHeight = $maxHeight;

        return $this;
    }

    /**
     * Set the starting line number
     *
     * @param int $startLine Starting line number (useful for code snippets)
     * @return self
     */
    public function startLine(int $startLine): self
    {
        $this->startLine = $startLine;

        return $this;
    }

    /**
     * Shorthand methods for common languages
     */
    public function php(): self
    {
        return $this->language('php');
    }

    public function javascript(): self
    {
        return $this->language('javascript');
    }

    public function typescript(): self
    {
        return $this->language('typescript');
    }

    public function python(): self
    {
        return $this->language('python');
    }

    public function java(): self
    {
        return $this->language('java');
    }

    public function csharp(): self
    {
        return $this->language('csharp');
    }

    public function cpp(): self
    {
        return $this->language('cpp');
    }

    public function go(): self
    {
        return $this->language('go');
    }

    public function rust(): self
    {
        return $this->language('rust');
    }

    public function bash(): self
    {
        return $this->language('bash');
    }

    public function shell(): self
    {
        return $this->language('shell');
    }

    public function sql(): self
    {
        return $this->language('sql');
    }

    public function json(): self
    {
        return $this->language('json');
    }

    public function xml(): self
    {
        return $this->language('xml');
    }

    public function yaml(): self
    {
        return $this->language('yaml');
    }

    public function html(): self
    {
        return $this->language('html');
    }

    public function css(): self
    {
        return $this->language('css');
    }

    public function markdown(): self
    {
        return $this->language('markdown');
    }

    public function toArray(): array
    {
        return array_merge($this->getCommonProperties(), $this->filterNullValues([
            'content' => $this->content,
            'language' => $this->language,
            'theme' => $this->theme,
            'lineNumbers' => $this->lineNumbers,
            'highlightLines' => !empty($this->highlightLines) ? $this->highlightLines : null,
            'copyButton' => $this->copyButton,
            'filename' => $this->filename,
            'wrap' => $this->wrap,
            'maxHeight' => $this->maxHeight,
            'startLine' => $this->startLine,
        ]));
    }
}
