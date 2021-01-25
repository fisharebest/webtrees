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

namespace Fisharebest\Webtrees\Factories;

use Fisharebest\Flysystem\Adapter\ChrootAdapter;
use Fisharebest\Webtrees\Contracts\FilesystemFactoryInterface;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Webtrees;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Cached\CachedAdapter;
use League\Flysystem\Cached\Storage\Memory;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;

use function realpath;

/**
 * Make a filesystem.
 */
class FilesystemFactory implements FilesystemFactoryInterface
{
    private const ROOT_DIR = __DIR__ . '/../..';

    /**
     * Create a filesystem for the user's data folder.
     *
     * @return FilesystemInterface
     */
    public function data(): FilesystemInterface
    {
        $data_dir = Site::getPreference('INDEX_DIRECTORY', Webtrees::DATA_DIR);

        return new Filesystem(new CachedAdapter(new Local($data_dir), new Memory()));
    }

    /**
     * Describe a filesystem for the user's data folder.
     *
     * @return string
     */
    public function dataName(): string
    {
        return Site::getPreference('INDEX_DIRECTORY', Webtrees::DATA_DIR);
    }

    /**
     * Create a filesystem for a tree's media folder.
     *
     * @param Tree $tree
     *
     * @return FilesystemInterface
     */
    public function media(Tree $tree): FilesystemInterface
    {
        $media_dir = $tree->getPreference('MEDIA_DIRECTORY', 'media/');
        $adapter   = new ChrootAdapter($this->data(), $media_dir);

        return new Filesystem($adapter);
    }

    /**
     * Create a filesystem for the application's root folder.
     *
     * @return FilesystemInterface
     */
    public function root(): FilesystemInterface
    {
        return new Filesystem(new CachedAdapter(new Local(self::ROOT_DIR), new Memory()));
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
