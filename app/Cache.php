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

namespace Fisharebest\Webtrees;

use Closure;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

/**
 * Wrapper around the symfony PSR6 cache library.
 * Hash the keys to protect against characters that are not allowed in PSR6.
 */
class Cache
{
    /** @var TagAwareCacheInterface */
    private $cache;

    /**
     * Cache constructor.
     *
     * @param TagAwareCacheInterface $cache
     */
    public function __construct(TagAwareCacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Generate a key compatible with PSR-6 requirements
     * @see https://www.php-fig.org/psr/psr-6/
     *
     * @param string $key
     * @return string
     */
    public function safeKey(string $key): string
    {
        return md5($key);
    }

    /**
     * Fetch an item from the cache - or create it where it does not exist.
     *
     * @param string   $key
     * @param Closure  $closure
     * @param int|null $ttl
     * @param string[] $tags
     *
     * @return mixed
     */
    public function remember(string $key, Closure $closure, int $ttl = null, array $tags = [])
    {
        $tags = array_map([$this, 'safeKey'], $tags);
        return $this->cache->get(
            $this->safeKey($key),
            static function (ItemInterface $item) use ($closure, $tags, $ttl) {
                $item->expiresAfter($ttl);
                $item->tag($tags);

                return $closure();
            }
        );
    }

    /**
     * Invalidate cache items based on tags.
     *
     * @param string[] $tags
     * @return bool
     */
    public function invalidateTags(array $tags): bool
    {
        return $this->cache->invalidateTags(array_map([$this, 'safeKey'], $tags));
    }

    /**
     * Remove an item from the cache.
     *
     * @param string $key
     */
    public function forget(string $key): void
    {
        $this->cache->delete($this->safeKey($key));
    }
}
