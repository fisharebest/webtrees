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

namespace Fisharebest\Webtrees;

use Closure;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Wrapper around the symfony PSR6 cache library.
 * Hash the keys to protect against characters that are not allowed in PSR6.
 */
class Cache
{
    private CacheInterface $cache;

    /**
     * Cache constructor.
     *
     * @param CacheInterface $cache
     */
    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Fetch an item from the cache - or create it where it does not exist.
     *
     * @template T
     *
     * @param string       $key
     * @param Closure(): T $closure
     * @param int|null     $ttl
     *
     * @return T
     */
    public function remember(string $key, Closure $closure, int $ttl = null)
    {
        return $this->cache->get(md5($key), static function (ItemInterface $item) use ($closure, $ttl) {
            $item->expiresAfter($ttl);

            return $closure();
        });
    }

    /**
     * Remove an item from the cache.
     *
     * @param string $key
     */
    public function forget(string $key): void
    {
        $this->cache->delete(md5($key));
    }
}
