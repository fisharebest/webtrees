<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Cache\Traits;

use Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\CacheItem;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 *
 * @internal
 */
trait AbstractAdapterTrait
{
    use AbstractTrait;

    /**
     * @var \Closure needs to be set by class, signature is function(string <key>, mixed <value>, bool <isHit>)
     */
    private $createCacheItem;

    /**
     * @var \Closure needs to be set by class, signature is function(array <deferred>, string <namespace>, array <&expiredIds>)
     */
    private $mergeByLifetime;

    /**
     * {@inheritdoc}
     */
    public function getItem($key)
    {
        if ($this->deferred) {
            $this->commit();
        }
        $id = $this->getId($key);

        $f = $this->createCacheItem;
        $isHit = false;
        $value = null;

        try {
            foreach ($this->doFetch([$id]) as $value) {
                $isHit = true;
            }
        } catch (\Exception $e) {
            CacheItem::log($this->logger, 'Failed to fetch key "{key}": '.$e->getMessage(), ['key' => $key, 'exception' => $e]);
        }

        return $f($key, $value, $isHit);
    }

    /**
     * {@inheritdoc}
     */
    public function getItems(array $keys = [])
    {
        if ($this->deferred) {
            $this->commit();
        }
        $ids = [];

        foreach ($keys as $key) {
            $ids[] = $this->getId($key);
        }
        try {
            $items = $this->doFetch($ids);
        } catch (\Exception $e) {
            CacheItem::log($this->logger, 'Failed to fetch items: '.$e->getMessage(), ['keys' => $keys, 'exception' => $e]);
            $items = [];
        }
        $ids = array_combine($ids, $keys);

        return $this->generateItems($items, $ids);
    }

    /**
     * {@inheritdoc}
     */
    public function save(CacheItemInterface $item)
    {
        if (!$item instanceof CacheItem) {
            return false;
        }
        $this->deferred[$item->getKey()] = $item;

        return $this->commit();
    }

    /**
     * {@inheritdoc}
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        if (!$item instanceof CacheItem) {
            return false;
        }
        $this->deferred[$item->getKey()] = $item;

        return true;
    }

    public function __destruct()
    {
        if ($this->deferred) {
            $this->commit();
        }
    }

    private function generateItems($items, &$keys)
    {
        $f = $this->createCacheItem;

        try {
            foreach ($items as $id => $value) {
                if (!isset($keys[$id])) {
                    $id = key($keys);
                }
                $key = $keys[$id];
                unset($keys[$id]);
                yield $key => $f($key, $value, true);
            }
        } catch (\Exception $e) {
            CacheItem::log($this->logger, 'Failed to fetch items: '.$e->getMessage(), ['keys' => array_values($keys), 'exception' => $e]);
        }

        foreach ($keys as $key) {
            yield $key => $f($key, null, false);
        }
    }
}
