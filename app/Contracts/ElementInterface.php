<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

use Fisharebest\Webtrees\Tree;

/**
 * A GEDCOM element is a tag/primitive in a GEDCOM file.
 */
interface ElementInterface
{
    /**
     * Convert a value to a canonical form.
     */
    public function canonical(string $value): string;

    /**
     * Should we collapse the children of this element when editing?
     */
    public function collapseChildren(): bool;

    /**
     * Create a default value for this element.
     */
    public function default(Tree $tree): string;

    /**
     * An edit control for this data.
     */
    public function edit(string $id, string $name, string $value, Tree $tree): string;

    /**
     * Escape @ signs in a GEDCOM export.
     */
    public function escape(string $value): string;

    /**
     * Name for this GEDCOM primitive.
     */
    public function label(): string;

    /**
     * Create a label/value pair for this element.
     */
    public function labelValue(string $value, Tree $tree): string;

    /**
     * Set, remove or replace a subtag.
     */
    public function subtag(string $subtag, string $repeat, string $before = ''): void;

    /**
     * @return array<string,string>
     */
    public function subtags(): array;

    /**
     * Display the value of this type of element.
     */
    public function value(string $value, Tree $tree): string;

    /**
     * A list of controlled values for this element
     *
     * @return array<int|string,string>
     */
    public function values(): array;
}
