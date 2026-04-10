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

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Services\CalendarService;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CalendarPage::class)]
class CalendarPageTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(CalendarPage::class));
    }

    /**
     * The day view renders with STATUS_OK.
     */
    public function testHandleDayView(): void
    {
        $gedcom_import_service = new GedcomImportService();
        $tree_service          = new TreeService($gedcom_import_service);
        $tree                  = $tree_service->create('name', 'title');
        $calendar_service      = new CalendarService();

        $handler  = new CalendarPage($calendar_service);
        $request  = self::createRequest(
            query: ['cal' => '@#DGREGORIAN@', 'day' => '1', 'month' => 'JAN', 'year' => '2000'],
            attributes: ['tree' => $tree, 'view' => 'day'],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());

        $body = (string) $response->getBody();
        self::assertNotEmpty($body);
    }

    /**
     * The month view renders with STATUS_OK.
     */
    public function testHandleMonthView(): void
    {
        $gedcom_import_service = new GedcomImportService();
        $tree_service          = new TreeService($gedcom_import_service);
        $tree                  = $tree_service->create('name', 'title');
        $calendar_service      = new CalendarService();

        $handler  = new CalendarPage($calendar_service);
        $request  = self::createRequest(
            query: ['cal' => '@#DGREGORIAN@', 'month' => 'JAN', 'year' => '2000'],
            attributes: ['tree' => $tree, 'view' => 'month'],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * The year view renders with STATUS_OK.
     */
    public function testHandleYearView(): void
    {
        $gedcom_import_service = new GedcomImportService();
        $tree_service          = new TreeService($gedcom_import_service);
        $tree                  = $tree_service->create('name', 'title');
        $calendar_service      = new CalendarService();

        $handler  = new CalendarPage($calendar_service);
        $request  = self::createRequest(
            query: ['cal' => '@#DGREGORIAN@', 'year' => '2000'],
            attributes: ['tree' => $tree, 'view' => 'year'],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * When no date parameters are supplied, the handler picks defaults and renders.
     */
    public function testHandleWithDefaultDate(): void
    {
        $gedcom_import_service = new GedcomImportService();
        $tree_service          = new TreeService($gedcom_import_service);
        $tree                  = $tree_service->create('name', 'title');
        $calendar_service      = new CalendarService();

        $handler  = new CalendarPage($calendar_service);
        $request  = self::createRequest(
            attributes: ['tree' => $tree, 'view' => 'day'],
        );
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }
}
