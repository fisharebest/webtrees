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

use Fisharebest\Webtrees\Services\TimeoutService;
use Fisharebest\Webtrees\Services\UpgradeService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Test UpgradeController class.
 *
 * @covers \Fisharebest\Webtrees\Http\Controllers\Admin\UpgradeController
 */
class UpgradeControllerTest extends \Fisharebest\Webtrees\TestCase
{
    protected static $uses_database = true;

    /**
     * @return void
     */
    public function testWizard(): void
    {
        $mock_timeout_service = $this->createMock(TimeoutService::class);
        app()->instance(TimeoutService::class, $mock_timeout_service);

        $mock_upgrade_service = $this->createMock(UpgradeService::class);
        app()->instance(UpgradeService::class, $mock_upgrade_service);

        $controller = app()->make(UpgradeController::class);
        $response   = app()->dispatch($controller, 'wizard');

        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * @return void
     */
    public function testStepCheck(): void
    {
        $mock_timeout_service = $this->createMock(TimeoutService::class);
        app()->instance(TimeoutService::class, $mock_timeout_service);

        $mock_upgrade_service = $this->createMock(UpgradeService::class);
        app()->instance(UpgradeService::class, $mock_upgrade_service);

        app()->instance(Request::class, new Request(['step' => 'Check']));
        $controller = app()->make(UpgradeController::class);
        $response   = app()->dispatch($controller, 'step');

        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * @return void
     */
    public function testStepPending(): void
    {
        $mock_timeout_service = $this->createMock(TimeoutService::class);
        app()->instance(TimeoutService::class, $mock_timeout_service);

        $mock_upgrade_service = $this->createMock(UpgradeService::class);
        app()->instance(UpgradeService::class, $mock_upgrade_service);

        app()->instance(Request::class, new Request(['step' => 'Pending']));
        $controller = app()->make(UpgradeController::class);
        $response   = app()->dispatch($controller, 'step');

        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * @return void
     */
    public function testStepExport(): void
    {
        $mock_timeout_service = $this->createMock(TimeoutService::class);
        app()->instance(TimeoutService::class, $mock_timeout_service);

        $mock_upgrade_service = $this->createMock(UpgradeService::class);
        app()->instance(UpgradeService::class, $mock_upgrade_service);

        app()->instance(Request::class, new Request(['step' => 'Export']));
        $controller = app()->make(UpgradeController::class);
        $response   = app()->dispatch($controller, 'step');

        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * @return void
     */
    public function testStepDownload(): void
    {
        $mock_timeout_service = $this->createMock(TimeoutService::class);
        app()->instance(TimeoutService::class, $mock_timeout_service);

        $mock_upgrade_service = $this->createMock(UpgradeService::class);
        app()->instance(UpgradeService::class, $mock_upgrade_service);

        app()->instance(Request::class, new Request(['step' => 'Download']));
        $controller = app()->make(UpgradeController::class);
        $response   = app()->dispatch($controller, 'step');

        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * @return void
     */
    public function testStepUnzip(): void
    {
        $mock_timeout_service = $this->createMock(TimeoutService::class);
        app()->instance(TimeoutService::class, $mock_timeout_service);

        $mock_upgrade_service = $this->createMock(UpgradeService::class);
        app()->instance(UpgradeService::class, $mock_upgrade_service);

        app()->instance(Request::class, new Request(['step' => 'Unzip']));
        $controller = app()->make(UpgradeController::class);
        $response   = app()->dispatch($controller, 'step');

        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * @return void
     */
    public function testStepCopy(): void
    {
        $mock_timeout_service = $this->createMock(TimeoutService::class);
        app()->instance(TimeoutService::class, $mock_timeout_service);

        $mock_upgrade_service = $this->createMock(UpgradeService::class);
        app()->instance(UpgradeService::class, $mock_upgrade_service);

        app()->instance(Request::class, new Request(['step' => 'Copy']));
        $controller = app()->make(UpgradeController::class);
        $response   = app()->dispatch($controller, 'step');

        $this->assertInstanceOf(Response::class, $response);
    }
}
