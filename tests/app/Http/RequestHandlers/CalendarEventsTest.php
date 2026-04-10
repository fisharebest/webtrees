<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Services\CalendarService;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CalendarEvents::class)]
class CalendarEventsTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(CalendarEvents::class));
    }

    public function testHandleReturnsResponse(): void
    {
        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('test', 'Test');

        $calendar_service = $this->createMock(CalendarService::class);
        $calendar_service->method('getAnniversaryEvents')->willReturn([]);

        $handler  = new CalendarEvents($calendar_service);
        $request  = self::createRequest(
            RequestMethodInterface::METHOD_GET,
            [
                'cal'      => '@#DGREGORIAN@',
                'day'      => '1',
                'month'    => 'JAN',
                'year'     => '2000',
                'filterev' => '',
                'filterof' => '',
                'filtersx' => '',
            ],
            [],
            [],
            ['tree' => $tree, 'view' => 'day'],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        self::assertNotEmpty((string) $response->getBody());
    }
}
