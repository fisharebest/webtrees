<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\Controllers;

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Services\CalendarService;
use Fisharebest\Webtrees\Services\LocalizationService;
use Fisharebest\Webtrees\TestCase;

/**
 * Test the module controller
 *
 * @covers \Fisharebest\Webtrees\Http\Controllers\AbstractBaseController
 * @covers \Fisharebest\Webtrees\Http\Controllers\CalendarController
 * @covers \Fisharebest\Webtrees\Services\CalendarService
 */
class CalendarControllerTest extends TestCase
{
    protected static $uses_database = true;

    /**
     * @return void
     */
    public function testCalendar(): void
    {
        $tree = $this->importTree('demo.ged');

        $calendar_service     = new CalendarService();
        $localization_service = new LocalizationService();
        $controller           = new CalendarController($calendar_service, $localization_service);

        $request  = self::createRequest(RequestMethodInterface::METHOD_GET, [], [], [], ['tree' => $tree, 'view' => 'day']);
        $response = $controller->calendar($request);
        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());

        $request  = self::createRequest(RequestMethodInterface::METHOD_GET, [], [], [], ['tree' => $tree, 'view' => 'month']);
        $response = $controller->page($request);
        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());

        $request  = self::createRequest(RequestMethodInterface::METHOD_GET, [], [], [], ['tree' => $tree, 'view' => 'year']);
        $response = $controller->page($request);
        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }
}
