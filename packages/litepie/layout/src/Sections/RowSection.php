<?php

namespace Litepie\Layout\Sections;

/**
 * RowSection
 *
 * Flexbox row layout section that arranges components horizontally.
 * Directly contains components (no slots).
 *
 * Example:
 *   $row = RowSection::make('side-by-side')
 *       ->gap('md')
 *       ->align('center');
 *   
 *   $row->add($avatar);
 *   $row->add($text);
 *
 * Or with callback:
 *   $section->row('profile', function($row) {
 *       $row->add($avatar);
 *       $row->add($text);
 *   });
 */
class RowSection extends BaseSection
{
    protected string $gap = 'md';

    protected string $align = 'start'; // start, center, end, stretch, baseline

    protected string $justify = 'start'; // start, center, end, between, around, evenly

    protected bool $wrap = false;

    public function __construct(string $name)
    {
        parent::__construct($name, 'row');
    }

    public static function make(string $name): self
    {
        return new static($name);
    }

    /**
     * Set the gap/spacing between items
     */
    public function gap(string $gap): self
    {
        $this->gap = $gap;

        return $this;
    }

    /**
     * Set vertical alignment of items (align-items)
     */
    public function align(string $align): self
    {
        $this->align = $align;

        return $this;
    }

    /**
     * Set horizontal distribution of items (justify-content)
     */
    public function justify(string $justify): self
    {
        $this->justify = $justify;

        return $this;
    }

    /**
     * Enable/disable flex wrap
     */
    public function wrap(bool $wrap = true): self
    {
        $this->wrap = $wrap;

        return $this;
    }

    public function toArray(): array
    {
        $data = $this->getCommonProperties();
        
        return array_merge($data, [
            'gap' => $this->gap,
            'align' => $this->align,
            'justify' => $this->justify,
            'wrap' => $this->wrap,
            'permissions' => $this->permissions ?? [],
            'roles' => $this->roles ?? [],
            'authorized_to_see' => $this->authorizedToSee ?? null,
        ]);
    }
}
