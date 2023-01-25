<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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

use Exception;
use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Http\Exceptions\HttpServerErrorException;
use Fisharebest\Webtrees\Services\GedcomExportService;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TimeoutService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UpgradeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;
use Nyholm\Psr7\Factory\Psr17Factory;

/**
 * Test UpgradeController class.
 *
 * @covers \Fisharebest\Webtrees\Http\RequestHandlers\UpgradeWizardStep
 */
class UpgradeWizardStepTest extends TestCase
{
    protected static bool $uses_database = true;

    /**
     * @return void
     */
    public function testIgnoreStepInvalid(): void
    {
        $handler = new UpgradeWizardStep(
            new GedcomExportService(new Psr17Factory(), new Psr17Factory()),
            new TreeService(new GedcomImportService()),
            new UpgradeService(new TimeoutService())
        );

        $request = self::createRequest(RequestMethodInterface::METHOD_POST, ['step' => 'Invalid']);

        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_NO_CONTENT, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testStepCheckOK(): void
    {
        $mock_upgrade_service = $this->createMock(UpgradeService::class);
        $mock_upgrade_service->method('latestVersion')->willReturn('999.999.999');
        $handler = new UpgradeWizardStep(
            new GedcomExportService(new Psr17Factory(), new Psr17Factory()),
            new TreeService(new GedcomImportService()),
            $mock_upgrade_service
        );

        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, ['step' => 'Check']);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testStepCheckUnavailable(): void
    {
        $this->expectException(HttpServerErrorException::class);

        $mock_upgrade_service = $this->createMock(UpgradeService::class);
        $mock_upgrade_service->method('latestVersion')->willReturn('');
        $handler = new UpgradeWizardStep(
            new GedcomExportService(new Psr17Factory(), new Psr17Factory()),
            new TreeService(new GedcomImportService()),
            $mock_upgrade_service
        );

        $request = self::createRequest(RequestMethodInterface::METHOD_POST, ['step' => 'Check']);
        $handler->handle($request);
    }

    /**
     * @return void
     */
    public function testStepCheckFail(): void
    {
        $this->expectException(HttpServerErrorException::class);

        $mock_upgrade_service = $this->createMock(UpgradeService::class);
        $mock_upgrade_service->method('latestVersion')->willReturn('0.0.0');
        $handler = new UpgradeWizardStep(
            new GedcomExportService(new Psr17Factory(), new Psr17Factory()),
            new TreeService(new GedcomImportService()),
            $mock_upgrade_service
        );

        $request = self::createRequest(RequestMethodInterface::METHOD_POST, ['step' => 'Check']);
        $handler->handle($request);
    }

    /**
     * @return void
     */
    public function testStepPrepare(): void
    {
        $handler = new UpgradeWizardStep(
            new GedcomExportService(new Psr17Factory(), new Psr17Factory()),
            new TreeService(new GedcomImportService()),
            new UpgradeService(new TimeoutService())
        );

        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, ['step' => 'Prepare']);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testStepPending(): void
    {
        $handler = new UpgradeWizardStep(
            new GedcomExportService(new Psr17Factory(), new Psr17Factory()),
            new TreeService(new GedcomImportService()),
            new UpgradeService(new TimeoutService())
        );

        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, ['step' => 'Pending']);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testStepPendingExist(): void
    {
        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('name', 'title');
        $user         = (new UserService())->create('user', 'name', 'email', 'password');

        Auth::login($user);
        $tree->createIndividual("0 @@ INDI\n1 NAME Joe Bloggs");

        $handler = new UpgradeWizardStep(
            new GedcomExportService(new Psr17Factory(), new Psr17Factory()),
            new TreeService(new GedcomImportService()),
            new UpgradeService(new TimeoutService())
        );

        $request = self::createRequest(RequestMethodInterface::METHOD_POST, ['step' => 'Pending']);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR, $response->getStatusCode());
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

        $handler = new UpgradeWizardStep(
            new GedcomExportService(new Psr17Factory(), new Psr17Factory()),
            $tree_service,
            new UpgradeService(new TimeoutService())
        );

        $request  = self::createRequest()->withQueryParams(['step' => 'Export', 'tree' => $tree->name()]);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());

        // Now overwrite the file we just created
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testStepDownloadFails(): void
    {
        $this->expectException(HttpServerErrorException::class);

        $mock_upgrade_service = $this->createMock(UpgradeService::class);
        $mock_upgrade_service->method('downloadFile')->will(self::throwException(new Exception()));
        $handler = new UpgradeWizardStep(
            new GedcomExportService(new Psr17Factory(), new Psr17Factory()),
            new TreeService(new GedcomImportService()),
            $mock_upgrade_service
        );

        $request = self::createRequest(RequestMethodInterface::METHOD_POST, ['step' => 'Download']);
        $handler->handle($request);
    }

    /**
     * @return void
     */
    public function testStepDownload(): void
    {
        $mock_upgrade_service = $this->createMock(UpgradeService::class);
        $mock_upgrade_service->method('downloadFile')->willReturn(123456);
        $handler = new UpgradeWizardStep(
            new GedcomExportService(new Psr17Factory(), new Psr17Factory()),
            new TreeService(new GedcomImportService()),
            $mock_upgrade_service
        );

        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, ['step' => 'Download']);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testStepUnzip(): void
    {
        $mock_upgrade_service = $this->createMock(UpgradeService::class);
        $mock_upgrade_service->method('webtreesZipContents')->willReturn(new Collection());
        $handler = new UpgradeWizardStep(
            new GedcomExportService(new Psr17Factory(), new Psr17Factory()),
            new TreeService(new GedcomImportService()),
            $mock_upgrade_service
        );

        $request  = self::createRequest(RequestMethodInterface::METHOD_POST, ['step' => 'Unzip']);
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }
}
