<?php

namespace App\Support\Website;

use Illuminate\Support\Collection;

/**
 * A resolved navigation item handed to templates.
 */
class MenuLink
{
    /**
     * @param  Collection<int, MenuLink>  $children
     */
    public function __construct(
        public readonly string $title,
        public readonly ?string $url,
        public readonly bool $newTab = false,
        public readonly Collection $children = new Collection,
        public readonly bool $active = false,
    ) {}

    public function hasChildren(): bool
    {
        return $this->children->isNotEmpty();
    }

    /** Attributes for the <a> tag: href + target/rel. */
    public function attributes(): string
    {
        $href = e($this->url ?? '#');

        return 'href="'.$href.'"'.($this->newTab ? ' target="_blank" rel="noopener noreferrer"' : '');
    }
}
