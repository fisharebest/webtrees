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

namespace Fisharebest\Webtrees\Http\Controllers\Admin;

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Services\HousekeepingService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\ServerCheckService;
use Fisharebest\Webtrees\Services\TimeoutService;
use Fisharebest\Webtrees\Services\UpgradeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;

/**
 * Test the control panel controller
 *
 * @covers \Fisharebest\Webtrees\Http\Controllers\Admin\ControlPanelController
 */
class ControlPanelControllerTest extends TestCase
{
    protected static $uses_database = true;

    /**
     * @return void
     */
    public function testControlPanel(): void
    {
        $module_service       = new ModuleService();
        $housekeeping_service = new HousekeepingService();
        $server_check_service = new ServerCheckService();
        $timeout_service      = new TimeoutService();
        $upgrade_service      = new UpgradeService($timeout_service);
        $user_service         = new UserService();
        $controller           = new ControlPanelController($housekeeping_service, $module_service, $server_check_service, $upgrade_service, $user_service);
        $request              = self::createRequest();
        $response             = $controller->controlPanel($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testControlPanelManager(): void
    {
        $module_service       = new ModuleService();
        $housekeeping_service = new HousekeepingService();
        $server_check_service = new ServerCheckService();
        $timeout_service      = new TimeoutService();
        $upgrade_service      = new UpgradeService($timeout_service);
        $user_service         = new UserService();
        $controller           = new ControlPanelController($housekeeping_service, $module_service, $server_check_service, $upgrade_service, $user_service);
        $request              = self::createRequest();
        $response             = $controller->controlPanelManager($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }
}
