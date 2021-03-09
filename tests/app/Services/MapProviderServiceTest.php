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

use Fisharebest\Webtrees\Contracts\CacheFactoryInterface;
use Fisharebest\Webtrees\Services\MapProviderService;
use Symfony\Component\Cache\Adapter\NullAdapter;

/**
 * Test the MapProviderService class
 */
class MapProviderServiceTest extends TestCase
{
    protected static $uses_database = true;

    public function setUp(): void
    {
        parent::setUp();

        $cache_factory = self::createMock(CacheFactoryInterface::class);
        $cache_factory->method('array')->willReturn(new Cache(new NullAdapter()));
        Registry::cache($cache_factory);
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\MapProviderService::providers
     * @return void
     */
    public function testProviders(): void
    {
        $map_provider_service = new MapProviderService();
        $providers            = $map_provider_service->providers();

        self::assertTrue($providers->has('openstreetmap'));
    }

    /**
     *
     * @covers \Fisharebest\Webtrees\Services\MapProviderService::styles
     * @return void
     */
    public function testStyles(): void
    {
        $map_provider_service = new MapProviderService();
        $styles               = $map_provider_service->styles('openstreetmap');

        self::assertTrue($styles->has('mapnik'));
    }

    /**
     *
     * @covers \Fisharebest\Webtrees\Services\MapProviderService::userParameters
     * @return void
     */
    public function testUserParameters(): void
    {
        $map_provider_service = new MapProviderService();
        $user_parameters      = $map_provider_service->userParameters('openstreetmap');

        self::assertTrue($user_parameters->isEmpty());
    }

    /**
     *
     * @covers \Fisharebest\Webtrees\Services\MapProviderService::providerLayers
     * @return void
     */
    public function testProviderLayers(): void
    {
        $map_provider_service = new MapProviderService();
        $layers = $map_provider_service->providerLayers();

        self::assertCount(1, $layers);
    }
}
