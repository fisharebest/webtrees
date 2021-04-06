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

use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Component\Cache\CacheItem;

/**
 * Class CacheTest.
 */
class CacheTest extends TestCase
{
    /**
     * @var TagAwareAdapter $tagAwareAdapter
     */
    private $tagAwareAdapter;

    /**
     * @var Cache $cache
     */
    private $cache;

    /**
     * {@inheritDoc}
     * @see \Fisharebest\Webtrees\TestCase::setUp()
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->tagAwareAdapter = new TagAwareAdapter(new ArrayAdapter());
        $this->cache = new Cache($this->tagAwareAdapter);
    }

    /**
     * {@inheritDoc}
     * @see \Fisharebest\Webtrees\TestCase::tearDown()
     */
    public function tearDown(): void
    {
        parent::tearDown();

        unset($this->tagAwareAdapter);
        unset($this->cache);
    }

    /**
     * @covers \Fisharebest\Webtrees\Cache::safeKey
     * @dataProvider keyProvider
     *
     * @param string $key
     */
    public function testSafeKey(string $key): void
    {
        $safeKey = $this->cache->safeKey($key);
        self::assertNotEmpty($safeKey);
        self::assertSame($safeKey, CacheItem::validateKey($safeKey));
    }

    /**
     * Data provider with example of keys
     *
     * @return string[][]
     */
    public function keyProvider(): array
    {
        return [ ['test'], ['I1@3'], [str_repeat('a', 70)], [''] ];
    }

    /**
     * @covers \Fisharebest\Webtrees\Cache::__construct
     * @covers \Fisharebest\Webtrees\Cache::remember
     *
     * @return void
     */
    public function testRemember(): void
    {
        self::assertEquals(10, $this->cache->remember('test', function () {
            return 10;
        }));

        self::assertTrue($this->tagAwareAdapter->hasItem($this->cache->safeKey('test')));
        self::assertEquals(10, $this->cache->remember('test', function () {
            return 15;
        }));
    }

    /**
     * @covers \Fisharebest\Webtrees\Cache::remember
     *
     * @return void
     */
    public function testRememberWithTTL(): void
    {
        self::assertEquals(10, $this->cache->remember('test', function () {
            return 10;
        }, 1, []));
        self::assertTrue($this->tagAwareAdapter->hasItem($this->cache->safeKey('test')));

        sleep(2);
        self::assertFalse($this->tagAwareAdapter->hasItem($this->cache->safeKey('test')));
    }

    /**
     * @covers \Fisharebest\Webtrees\Cache::invalidateTags
     *
     * @return void
     */
    public function testInvalidateTags(): void
    {
        self::assertEquals(10, $this->cache->remember('test', function () {
            return 10;
        }, null, ['test-tag']));
        self::assertTrue($this->cache->invalidateTags(['test-tag']));
        self::assertEquals(15, $this->cache->remember('test', function () {
            return 15;
        }));

        self::assertTrue($this->cache->invalidateTags(['test-tag2']));
    }

    /**
     * @covers \Fisharebest\Webtrees\Cache::forget
     *
     * @return void
     */
    public function testForget(): void
    {
        self::assertEquals(10, $this->cache->remember('test', function () {
            return 10;
        }));
        $this->cache->forget('test');

        self::assertFalse($this->tagAwareAdapter->hasItem($this->cache->safeKey('test')));
    }
}
