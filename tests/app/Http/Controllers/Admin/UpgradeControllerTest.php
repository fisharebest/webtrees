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
use Illuminate\Support\Collection;
use League\Flysystem\Filesystem;
use League\Flysystem\Memory\MemoryAdapter;
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
        $controller = new UpgradeController(
            new Filesystem(new MemoryAdapter()),
            new TimeoutService(microtime(true)),
            new UpgradeService(new TimeoutService(microtime(true)))
        );

        $response = $controller->wizard(new Request());

        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * @return void
     */
    public function testWizardContinue(): void
    {
        $this->importTree('demo.ged');

        $controller = new UpgradeController(
            new Filesystem(new MemoryAdapter()),
            new TimeoutService(microtime(true)),
            new UpgradeService(new TimeoutService(microtime(true)))
        );

        $response = $controller->wizard(new Request(['continue' => '1']));

        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * @return void
     */
    public function testStepCheck(): void
    {
        $controller = new UpgradeController(
            new Filesystem(new MemoryAdapter()),
            new TimeoutService(microtime(true)),
            new UpgradeService(new TimeoutService(microtime(true)))
        );

        $response = $controller->step(new Request(['step' => 'Check']), null);

        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * @return void
     */
    public function testStepPending(): void
    {
        $controller = new UpgradeController(
            new Filesystem(new MemoryAdapter()),
            new TimeoutService(microtime(true)),
            new UpgradeService(new TimeoutService(microtime(true)))
        );

        $response = $controller->step(new Request(['step' => 'Pending']), null);

        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * @return void
     */
    public function testStepExport(): void
    {
        $tree       = $this->importTree('demo.ged');
        $controller = new UpgradeController(
            new Filesystem(new MemoryAdapter()),
            new TimeoutService(microtime(true)),
            new UpgradeService(new TimeoutService(microtime(true)))
        );

        $response = $controller->step(new Request(['step' => 'Export']), $tree);

        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * @return void
     */
    public function testStepDownload(): void
    {
        $mock_upgrade_service = $this->createMock(UpgradeService::class);
        $mock_upgrade_service->method('downloadFile')->willReturn(123456);
        $controller = new UpgradeController(
            new Filesystem(new MemoryAdapter()),
            new TimeoutService(microtime(true)),
            $mock_upgrade_service
        );

        $response = $controller->step(new Request(['step' => 'Download']), null);

        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * @return void
     */
    public function testStepUnzip(): void
    {
        $mock_upgrade_service = $this->createMock(UpgradeService::class);
        $mock_upgrade_service->method('webtreesZipContents')->willReturn(new Collection([]));

        $controller = new UpgradeController(
            new Filesystem(new MemoryAdapter()),
            new TimeoutService(microtime(true)),
            $mock_upgrade_service
        );

        $response = $controller->step(new Request(['step' => 'Unzip']), null);

        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * @return void
     */
    public function testStepCopy(): void
    {
        $controller = new UpgradeController(
            new Filesystem(new MemoryAdapter()),
            new TimeoutService(microtime(true)),
            new UpgradeService(new TimeoutService(microtime(true)))
        );

        $response = $controller->step(new Request(['step' => 'Copy']), null);

        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * @return void
     */
    public function testStepCleanup(): void
    {
        $controller = new UpgradeController(
            new Filesystem(new MemoryAdapter()),
            new TimeoutService(microtime(true)),
            new UpgradeService(new TimeoutService(microtime(true)))
        );

        $response = $controller->step(new Request(['step' => 'Cleanup']), null);

        $this->assertInstanceOf(Response::class, $response);
    }
}
