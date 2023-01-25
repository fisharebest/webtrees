<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Factories;

use Fisharebest\Webtrees\Contracts\ElementFactoryInterface;
use Fisharebest\Webtrees\Contracts\ElementInterface;
use Fisharebest\Webtrees\Elements\UnknownElement;

use function preg_match;

/**
 * Make a GEDCOM element.
 */
class ElementFactory implements ElementFactoryInterface
{
    /** @var array<string,ElementInterface> */
    private array $elements = [];

    /**
     * Create a GEDCOM element that corresponds to a GEDCOM tag.
     * Finds the correct element for all valid tags.
     * Finds a likely element for custom tags.
     *
     * @param string $tag - Colon delimited hierarchy, e.g. 'INDI:BIRT:PLAC'
     *
     * @return ElementInterface
     */
    public function make(string $tag): ElementInterface
    {
        return $this->elements[$tag] ?? $this->findElementByWildcard($tag) ?? new UnknownElement($tag);
    }

    /**
     * Register GEDCOM tags.
     *
     * @param array<string,ElementInterface> $elements
     */
    public function registerTags(array $elements): void
    {
        $this->elements = $elements + $this->elements;
    }

    /**
     * Register more subtags.
     *
     * @param array<string,array<int,array<int,string>>> $subtags
     */
    public function registerSubTags(array $subtags): void
    {
        foreach ($subtags as $tag => $children) {
            foreach ($children as $child) {
                $this->make($tag)->subtag(...$child);
            }
        }
    }

    /**
     * @param string $tag
     *
     * @return ElementInterface|null
     */
    private function findElementByWildcard(string $tag): ?ElementInterface
    {
        foreach ($this->elements as $tags => $element) {
            if (str_contains($tags, '*')) {
                $regex = '/^' . strtr($tags, ['*' => '[^:]+']) . '$/';

                if (preg_match($regex, $tag)) {
                    return $element;
                }
            }
        }

        return null;
    }
}
