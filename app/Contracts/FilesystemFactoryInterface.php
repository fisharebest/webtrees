<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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
use League\Flysystem\FilesystemInterface;

/**
 * Make a filesystem.
 */
interface FilesystemFactoryInterface
{
    /**
     * Describe a filesystem for the user's data folder.
     *
     * @return string
     */
    public function dataName(): string;

    /**
     * Create a filesystem for the user's data folder.
     *
     * @return FilesystemInterface
     */
    public function data(): FilesystemInterface;

    /**
     * Create a filesystem for a tree's media folder.
     *
     * @param Tree $tree
     *
     * @return FilesystemInterface
     */
    public function media(Tree $tree): FilesystemInterface;

    /**
     * Create a filesystem for the application's root folder.
     *
     * @return FilesystemInterface
     */
    public function root(): FilesystemInterface;

    /**
     * Describe a filesystem for the application's root folder.
     *
     * @return string
     */
    public function rootName(): string;
}
