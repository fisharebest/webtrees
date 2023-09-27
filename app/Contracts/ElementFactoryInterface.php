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

namespace Fisharebest\Webtrees\Contracts;

/**
 * Make a GEDCOM primitive element.
 */
interface ElementFactoryInterface
{
    /**
     * Create a GEDCOM primitive object.
     *
     * @param string $tag
     *
     * @return ElementInterface
     */
    public function make(string $tag): ElementInterface;

    /**
     * Register GEDCOM tags.
     *
     * @param array<string,ElementInterface> $elements
     */
    public function registerTags(array $elements): void;

    /**
     * Register more subtags.
     *
     * @param array<string,array<int,array<int,string>>> $subtags
     */
    public function registerSubTags(array $subtags): void;
}
