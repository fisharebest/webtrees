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

use Fisharebest\Webtrees\Cache;
use Fisharebest\Webtrees\Contracts\CacheFactoryInterface;
use Fisharebest\Webtrees\Webtrees;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemTagAwareAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;

use function random_int;

/**
 * Make a cache.
 */
class CacheFactory implements CacheFactoryInterface
{
    // How frequently to perform garbage collection.
    private const GC_PROBABILITY = 1000;

    // Filesystem cache parameters.
    private const FILES_TTL = 8640000;
    private const FILES_DIR = Webtrees::DATA_DIR . 'cache/';

    /** @var TagAwareAdapter */
    private $array_adapter;

    /** @var FilesystemTagAwareAdapter */
    private $filesystem_adapter;

    /**
     * CacheFactory constructor.
     */
    public function __construct()
    {
        $this->array_adapter      = new TagAwareAdapter(new ArrayAdapter(0, false));
        $this->filesystem_adapter = new FilesystemTagAwareAdapter('', self::FILES_TTL, self::FILES_DIR);
    }

    /**
     * Create an array-based cache.
     *
     * @return Cache
     */
    public function array(): Cache
    {
        return new Cache($this->array_adapter);
    }

    /**
     * Create an file-based cache.
     *
     * @return Cache
     */
    public function file(): Cache
    {
        return new Cache($this->filesystem_adapter);
    }

    /**
     * Perform garbage collection.
     */
    public function __destruct()
    {
        if (random_int(1, self::GC_PROBABILITY) === 1) {
            $this->filesystem_adapter->prune();
        }
    }
}
