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

use Fisharebest\Localization\Locale\LocaleEnUs;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Module\SiteMapModule;
use Fisharebest\Webtrees\Services\CalendarService;
use Fisharebest\Webtrees\Services\LocalizationService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Test the module controller
 *
 * @covers \Fisharebest\Webtrees\Http\Controllers\AbstractBaseController
 * @covers \Fisharebest\Webtrees\Http\Controllers\CalendarController
 * @covers \Fisharebest\Webtrees\Services\CalendarService
 */
class CalendarControllerTest extends \Fisharebest\Webtrees\TestCase
{
    protected static $uses_database = true;

    /**
     * @return void
     */
    public function testCalendar(): void
    {
        $tree = $this->importTree('demo.ged');
        app()->instance(Tree::class, $tree);

        $calendar_service     = new CalendarService();
        $localization_service = new LocalizationService(new LocaleEnUs());
        $controller           = new CalendarController($calendar_service, $localization_service);

        $request  = new Request(['view' => 'day']);
        $response = $controller->page($request, $tree);
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $response = $controller->calendar($request, $tree);
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());

        $request  = new Request(['view' => 'month']);
        $response = $controller->page($request, $tree);
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $response = $controller->calendar($request, $tree);
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());

        $request  = new Request(['view' => 'year']);
        $response = $controller->page($request, $tree);
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $response = $controller->calendar($request, $tree);
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }
}

