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

use Exception;
use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Exceptions\HttpServerErrorException;
use Fisharebest\Webtrees\Services\GedcomExportService;
use Fisharebest\Webtrees\Services\TimeoutService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UpgradeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;
use League\Flysystem\Adapter\NullAdapter;
use League\Flysystem\Filesystem;

/**
 * Test UpgradeController class.
 *
 * @covers \Fisharebest\Webtrees\Http\Controllers\Admin\UpgradeController
 */
class UpgradeControllerTest extends TestCase
{
    protected static $uses_database = true;

    /**
     * @return void
     */
    public function testWizard(): void
    {
        $controller = new UpgradeController(
            new GedcomExportService(),
            new TreeService(),
            new UpgradeService(new TimeoutService())
        );

        $request  = self::createRequest();
        $response = $controller->wizard($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testWizardContinue(): void
    {
        $this->importTree('demo.ged');

        $controller = new UpgradeController(
            new GedcomExportService(),
            new TreeService(),
            new UpgradeService(new TimeoutService())
        );

        $request  = self::createRequest(RequestMethodInterface::METHOD_GET, ['continue' => '1'])
            ->withAttribute('filesystem.data', new Filesystem(new NullAdapter()))
            ->withAttribute('filesystem.root', new Filesystem(new NullAdapter()));
        $response = $controller->wizard($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testIgnoreStepInvalid(): void
    {
        $controller = new UpgradeController(
            new GedcomExportService(),
            new TreeService(),
            new UpgradeService(new TimeoutService())
        );

        $request = self::createRequest(RequestMethodInterface::METHOD_POST, ['step' => 'Invalid'])
            ->withAttribute('filesystem.data', new Filesystem(new NullAdapter()))
            ->withAttribute('filesystem.root', new Filesystem(new NullAdapter()));

        $response = $controller->step($request);

        $this->assertSame(StatusCodeInterface::STATUS_NO_CONTENT, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testStepCheckOK(): void
    {
        $mock_upgrade_service = $this->createMock(UpgradeService::class);
        $mock_upgrade_service->method('latestVersion')->willReturn('999.999.999');
        $controller = new UpgradeController(
            new GedcomExportService(),
            new TreeService(),
            $mock_upgrade_service
        );

        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, ['step' => 'Check'])
            ->withAttribute('filesystem.data', new Filesystem(new NullAdapter()))
            ->withAttribute('filesystem.root', new Filesystem(new NullAdapter()));
        $response = $controller->step($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testStepCheckUnavailable(): void
    {
        $this->expectException(HttpServerErrorException::class);

        $mock_upgrade_service = $this->createMock(UpgradeService::class);
        $mock_upgrade_service->method('latestVersion')->willReturn('');
        $controller = new UpgradeController(
            new GedcomExportService(),
            new TreeService(),
            $mock_upgrade_service
        );

        $request = self::createRequest(RequestMethodInterface::METHOD_POST, ['step' => 'Check'])
            ->withAttribute('filesystem.data', new Filesystem(new NullAdapter()))
            ->withAttribute('filesystem.root', new Filesystem(new NullAdapter()));
        $controller->step($request);
    }

    /**
     * @return void
     */
    public function testStepCheckFail(): void
    {
        $this->expectException(HttpServerErrorException::class);

        $mock_upgrade_service = $this->createMock(UpgradeService::class);
        $mock_upgrade_service->method('latestVersion')->willReturn('0.0.0');
        $controller = new UpgradeController(
            new GedcomExportService(),
            new TreeService(),
            $mock_upgrade_service
        );

        $request = self::createRequest(RequestMethodInterface::METHOD_POST, ['step' => 'Check'])
            ->withAttribute('filesystem.data', new Filesystem(new NullAdapter()))
            ->withAttribute('filesystem.root', new Filesystem(new NullAdapter()));
        $controller->step($request);
    }

    /**
     * @return void
     */
    public function testStepPrepare(): void
    {
        $controller = new UpgradeController(
            new GedcomExportService(),
            new TreeService(),
            new UpgradeService(new TimeoutService())
        );

        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, ['step' => 'Prepare'])
            ->withAttribute('filesystem.data', new Filesystem(new NullAdapter()))
            ->withAttribute('filesystem.root', new Filesystem(new NullAdapter()));
        $response = $controller->step($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testStepPending(): void
    {
        $controller = new UpgradeController(
            new GedcomExportService(),
            new TreeService(),
            new UpgradeService(new TimeoutService())
        );

        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, ['step' => 'Pending'])
            ->withAttribute('filesystem.data', new Filesystem(new NullAdapter()))
            ->withAttribute('filesystem.root', new Filesystem(new NullAdapter()));
        $response = $controller->step($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testStepPendingExist(): void
    {
        $this->expectException(HttpServerErrorException::class);

        $tree_service = new TreeService();
        $tree         = $tree_service->create('name', 'title');
        $user         = (new UserService())->create('user', 'name', 'email', 'password');

        Auth::login($user);
        $tree->createIndividual("0 @@ INDI\n1 NAME Joe Bloggs");

        $controller = new UpgradeController(
            new GedcomExportService(),
            new TreeService(),
            new UpgradeService(new TimeoutService())
        );

        $request = self::createRequest(RequestMethodInterface::METHOD_POST, ['step' => 'Pending'])
            ->withAttribute('filesystem.data', new Filesystem(new NullAdapter()))
            ->withAttribute('filesystem.root', new Filesystem(new NullAdapter()));
        $controller->step($request);
    }

    /**
     * @return void
     */
    public function testStepExport(): void
    {
        $tree            = $this->importTree('demo.ged');
        $all_trees       = Collection::make([$tree->name() => $tree]);
        $tree_service    = $this->createMock(TreeService::class);
        $tree_service->method('all')->willReturn($all_trees);

        $controller = new UpgradeController(
            new GedcomExportService(),
            $tree_service,
            new UpgradeService(new TimeoutService())
        );

        $request  = self::createRequest()
            ->withQueryParams(['step' => 'Export', 'tree' => $tree->name()])
            ->withAttribute('filesystem.data', new Filesystem(new NullAdapter()))
            ->withAttribute('filesystem.root', new Filesystem(new NullAdapter()));
        $response = $controller->step($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());

        // Now overwrite the file we just created
        $response = $controller->step($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testStepDownloadFails(): void
    {
        $this->expectException(HttpServerErrorException::class);

        $mock_upgrade_service = $this->createMock(UpgradeService::class);
        $mock_upgrade_service->method('downloadFile')->will($this->throwException(new Exception()));
        $controller = new UpgradeController(
            new GedcomExportService(),
            new TreeService(),
            $mock_upgrade_service
        );

        $request = self::createRequest(RequestMethodInterface::METHOD_POST, ['step' => 'Download'])
            ->withAttribute('filesystem.data', new Filesystem(new NullAdapter()))
            ->withAttribute('filesystem.root', new Filesystem(new NullAdapter()));
        $controller->step($request);
    }

    /**
     * @return void
     */
    public function testStepDownload(): void
    {
        $mock_upgrade_service = $this->createMock(UpgradeService::class);
        $mock_upgrade_service->method('downloadFile')->willReturn(123456);
        $controller = new UpgradeController(
            new GedcomExportService(),
            new TreeService(),
            $mock_upgrade_service
        );

        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, ['step' => 'Download'])
            ->withAttribute('filesystem.data', new Filesystem(new NullAdapter()))
            ->withAttribute('filesystem.root', new Filesystem(new NullAdapter()));
        $response = $controller->step($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testStepUnzip(): void
    {
        $mock_upgrade_service = $this->createMock(UpgradeService::class);
        $mock_upgrade_service->method('webtreesZipContents')->willReturn(new Collection());
        $controller = new UpgradeController(
            new GedcomExportService(),
            new TreeService(),
            $mock_upgrade_service
        );

        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, ['step' => 'Unzip'])
            ->withAttribute('filesystem.data', new Filesystem(new NullAdapter()))
            ->withAttribute('filesystem.root', new Filesystem(new NullAdapter()));
        $response = $controller->step($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testStepCopyAndCleanUp(): void
    {
        $controller = new UpgradeController(
            new GedcomExportService(),
            new TreeService(),
            new UpgradeService(new TimeoutService())
        );

        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, ['step' => 'Copy'])
            ->withAttribute('filesystem.data', new Filesystem(new NullAdapter()))
            ->withAttribute('filesystem.root', new Filesystem(new NullAdapter()));
        $response = $controller->step($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }
}
