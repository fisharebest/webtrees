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

use Fisharebest\Webtrees\Contracts\FilesystemFactoryInterface;
use Fisharebest\Webtrees\Site;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\PathPrefixing\PathPrefixedAdapter;

use function realpath;

use const DIRECTORY_SEPARATOR;

/**
 * Make a filesystem.
 */
class FilesystemFactory implements FilesystemFactoryInterface
{
    private const string ROOT_DIR = __DIR__ . '/../..';

    /**
     * Create a filesystem for the user's data folder.
     *
     * @param string $path_prefix
     *
     * @return FilesystemOperator
     */
    public function data(string $path_prefix = ''): FilesystemOperator
    {
        $adapter = new LocalFilesystemAdapter(Site::getPreference('INDEX_DIRECTORY'));

        if ($path_prefix !== '') {
            $adapter = new PathPrefixedAdapter($adapter, $path_prefix);
        }

        return new Filesystem($adapter);
    }

    /**
     * Describe a filesystem for the user's data folder.
     *
     * @return string
     */
    public function dataName(): string
    {
        return realpath(Site::getPreference('INDEX_DIRECTORY')) . DIRECTORY_SEPARATOR;
    }

    /**
     * Create a filesystem for the application's root folder.
     *
     * @param string $path_prefix
     *
     * @return FilesystemOperator
     */
    public function root(string $path_prefix = ''): FilesystemOperator
    {
        $adapter = new LocalFilesystemAdapter(self::ROOT_DIR);

        if ($path_prefix !== '') {
            $adapter = new PathPrefixedAdapter($adapter, $path_prefix);
        }

        return new Filesystem($adapter);
    }

    /**
     * Describe a filesystem for the application's root folder.
     *
     * @return string
     */
    public function rootName(): string
    {
        return realpath(self::ROOT_DIR) . '/';
    }
}
