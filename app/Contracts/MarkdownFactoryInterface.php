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

use Fisharebest\Webtrees\Tree;

/**
 * Create a markdown converter.
 */
interface MarkdownFactoryInterface
{
    /**
     * @param string    $markdown
     * @param Tree|null $tree
     *
     * @return string
     */
    public function autolink(string $markdown, Tree|null $tree = null): string;

    /**
     * @param string    $markdown
     * @param Tree|null $tree
     *
     * @return string
     */
    public function markdown(string $markdown, Tree|null $tree = null): string;
}
