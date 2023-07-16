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

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Module\IndividualListModule;

/**
 * Test the individual lists.
 *
 * @coversNothing
 */
class IndividualListTest extends TestCase
{
    protected static bool $uses_database = true;

    /**
     * @covers \Fisharebest\Webtrees\Module\IndividualListModule
     * @return void
     */
    public function testIndividualList(): void
    {
        $tree        = $this->importTree('demo.ged');
        $list_module = new IndividualListModule();

        $request  = self::createRequest(RequestMethodInterface::METHOD_GET, [], [], [], ['tree' => $tree]);
        $response = $list_module->handle($request);
        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());

        $request  = self::createRequest(RequestMethodInterface::METHOD_GET, ['alpha' => 'B'], [], [], ['tree' => $tree]);
        $response = $list_module->handle($request);
        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());

        $request  = self::createRequest(RequestMethodInterface::METHOD_GET, ['alpha' => ','], [], [], ['tree' => $tree]);
        $response = $list_module->handle($request);
        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());

        $request  = self::createRequest(RequestMethodInterface::METHOD_GET, ['alpha' => '@'], [], [], ['tree' => $tree]);
        $response = $list_module->handle($request);
        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());

        $request  = self::createRequest(RequestMethodInterface::METHOD_GET, ['surname' => 'BRAUN'], [], [], ['tree' => $tree]);
        $response = $list_module->handle($request);
        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }
}
