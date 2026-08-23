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

namespace Fisharebest\Webtrees\Tests\Unit\Http\Controllers;

use Exception;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Clock\SystemClock;
use Fisharebest\Webtrees\Enums\HttpRequestMethod;
use Fisharebest\Webtrees\Enums\HttpStatusCode;
use Fisharebest\Webtrees\Http\Controllers\UpgradeWizardStep;
use Fisharebest\Webtrees\Http\Exceptions\HttpInternalServerErrorException;
use Fisharebest\Webtrees\Services\GedcomExportService;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\MaintenanceModeService;
use Fisharebest\Webtrees\Services\PendingChangesService;
use Fisharebest\Webtrees\Services\PhpService;
use Fisharebest\Webtrees\Services\TimeoutService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UpgradeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tests\TestCase;
use Illuminate\Support\Collection;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

#[CoversClass(UpgradeWizardStep::class)]
class UpgradeWizardStepTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::createDatabase();
    }

    private function upgradeService(): UpgradeService
    {
        return new UpgradeService(
            self::createStub(ClientInterface::class),
            self::createStub(RequestFactoryInterface::class),
            new TimeoutService(php_service: new PhpService(), clock: new SystemClock()),
            new SystemClock(),
        );
    }

    public function testIgnoreStepInvalid(): void
    {
        $controller = new UpgradeWizardStep(
            new GedcomExportService(new Psr17Factory(), new Psr17Factory()),
            new MaintenanceModeService(__DIR__ . '/../../../data/'),
            new PendingChangesService(new GedcomImportService()),
            new TreeService(new GedcomImportService()),
            $this->upgradeService(),
            new SystemClock(),
        );

        $request = self::createRequest(HttpRequestMethod::POST->value, ['step' => 'Invalid']);

        $response = $controller->post($request);

        self::assertSame(HttpStatusCode::NoContent->value, $response->getStatusCode());
    }

    public function testStepCheckOK(): void
    {
        $mock_upgrade_service = self::createStub(UpgradeService::class);
        $mock_upgrade_service->method('latestVersion')->willReturn('999.999.999');
        $controller = new UpgradeWizardStep(
            new GedcomExportService(new Psr17Factory(), new Psr17Factory()),
            new MaintenanceModeService(__DIR__ . '/../../../data/'),
            new PendingChangesService(new GedcomImportService()),
            new TreeService(new GedcomImportService()),
            $mock_upgrade_service,
            new SystemClock(),
        );

        $request  = self::createRequest(HttpRequestMethod::POST->value, ['step' => 'Check']);
        $response = $controller->post($request);

        self::assertSame(HttpStatusCode::OK->value, $response->getStatusCode());
    }

    public function testStepCheckUnavailable(): void
    {
        $this->expectException(HttpInternalServerErrorException::class);

        $mock_upgrade_service = self::createStub(UpgradeService::class);
        $mock_upgrade_service->method('latestVersion')->willReturn('');
        $controller = new UpgradeWizardStep(
            new GedcomExportService(new Psr17Factory(), new Psr17Factory()),
            new MaintenanceModeService(__DIR__ . '/../../../data/'),
            new PendingChangesService(new GedcomImportService()),
            new TreeService(new GedcomImportService()),
            $mock_upgrade_service,
            new SystemClock(),
        );

        $request = self::createRequest(HttpRequestMethod::POST->value, ['step' => 'Check']);
        $controller->post($request);
    }

    public function testStepCheckFail(): void
    {
        $this->expectException(HttpInternalServerErrorException::class);

        $mock_upgrade_service = self::createStub(UpgradeService::class);
        $mock_upgrade_service->method('latestVersion')->willReturn('0.0.0');
        $controller = new UpgradeWizardStep(
            new GedcomExportService(new Psr17Factory(), new Psr17Factory()),
            new MaintenanceModeService(__DIR__ . '/../../../data/'),
            new PendingChangesService(new GedcomImportService()),
            new TreeService(new GedcomImportService()),
            $mock_upgrade_service,
            new SystemClock(),
        );

        $request = self::createRequest(HttpRequestMethod::POST->value, ['step' => 'Check']);
        $controller->post($request);
    }

    public function testStepPrepare(): void
    {
        $controller = new UpgradeWizardStep(
            new GedcomExportService(new Psr17Factory(), new Psr17Factory()),
            new MaintenanceModeService(__DIR__ . '/../../../data/'),
            new PendingChangesService(new GedcomImportService()),
            new TreeService(new GedcomImportService()),
            $this->upgradeService(),
            new SystemClock(),
        );

        $request  = self::createRequest(HttpRequestMethod::POST->value, ['step' => 'Prepare']);
        $response = $controller->post($request);

        self::assertSame(HttpStatusCode::OK->value, $response->getStatusCode());
    }

    public function testStepPending(): void
    {
        $controller = new UpgradeWizardStep(
            new GedcomExportService(new Psr17Factory(), new Psr17Factory()),
            new MaintenanceModeService(__DIR__ . '/../../../data/'),
            new PendingChangesService(new GedcomImportService()),
            new TreeService(new GedcomImportService()),
            $this->upgradeService(),
            new SystemClock(),
        );

        $request  = self::createRequest(HttpRequestMethod::POST->value, ['step' => 'Pending']);
        $response = $controller->post($request);

        self::assertSame(HttpStatusCode::OK->value, $response->getStatusCode());
    }

    public function testStepPendingExist(): void
    {
        $tree_service = new TreeService(new GedcomImportService());
        $tree         = $tree_service->create('name', 'title');
        $user         = (new UserService(new SystemClock()))->create('user', 'name', 'email', 'password');

        Auth::login($user);
        $tree->createIndividual("0 @@ INDI\n1 NAME Joe Bloggs");

        $controller = new UpgradeWizardStep(
            new GedcomExportService(new Psr17Factory(), new Psr17Factory()),
            new MaintenanceModeService(__DIR__ . '/../../../data/'),
            new PendingChangesService(new GedcomImportService()),
            new TreeService(new GedcomImportService()),
            $this->upgradeService(),
            new SystemClock(),
        );

        $request  = self::createRequest(HttpRequestMethod::POST->value, ['step' => 'Pending']);
        $response = $controller->post($request);

        self::assertSame(HttpStatusCode::InternalServerError->value, $response->getStatusCode());
    }

    public function testStepExport(): void
    {
        $tree         = $this->importTree('demo.ged');
        $all_trees    = Collection::make([$tree->name() => $tree]);
        $tree_service = self::createStub(TreeService::class);
        $tree_service->method('all')->willReturn($all_trees);

        $controller = new UpgradeWizardStep(
            new GedcomExportService(new Psr17Factory(), new Psr17Factory()),
            new MaintenanceModeService(__DIR__ . '/../../../data/'),
            new PendingChangesService(new GedcomImportService()),
            $tree_service,
            $this->upgradeService(),
            new SystemClock(),
        );

        $request  = self::createRequest()->withQueryParams(['step' => 'Export', 'tree' => $tree->name()]);
        $response = $controller->post($request);

        self::assertSame(HttpStatusCode::OK->value, $response->getStatusCode());

        // Now overwrite the file we just created
        $response = $controller->post($request);

        self::assertSame(HttpStatusCode::OK->value, $response->getStatusCode());
    }

    public function testStepDownloadFails(): void
    {
        $this->expectException(HttpInternalServerErrorException::class);

        $mock_upgrade_service = self::createStub(UpgradeService::class);
        $mock_upgrade_service->method('downloadFile')->will($this->throwException(new Exception()));
        $controller = new UpgradeWizardStep(
            new GedcomExportService(new Psr17Factory(), new Psr17Factory()),
            new MaintenanceModeService(__DIR__ . '/../../../data/'),
            new PendingChangesService(new GedcomImportService()),
            new TreeService(new GedcomImportService()),
            $mock_upgrade_service,
            new SystemClock(),
        );

        $request = self::createRequest(HttpRequestMethod::POST->value, ['step' => 'Download']);
        $controller->post($request);
    }

    public function testStepDownload(): void
    {
        $mock_upgrade_service = self::createStub(UpgradeService::class);
        $mock_upgrade_service->method('downloadFile')->willReturn(123456);
        $controller = new UpgradeWizardStep(
            new GedcomExportService(new Psr17Factory(), new Psr17Factory()),
            new MaintenanceModeService(__DIR__ . '/../../../data/'),
            new PendingChangesService(new GedcomImportService()),
            new TreeService(new GedcomImportService()),
            $mock_upgrade_service,
            new SystemClock(),
        );

        $request  = self::createRequest(HttpRequestMethod::POST->value, ['step' => 'Download']);
        $response = $controller->post($request);

        self::assertSame(HttpStatusCode::OK->value, $response->getStatusCode());
    }

    public function testStepUnzip(): void
    {
        $mock_upgrade_service = self::createStub(UpgradeService::class);
        $mock_upgrade_service->method('webtreesZipContents')->willReturn(new Collection());
        $controller = new UpgradeWizardStep(
            new GedcomExportService(new Psr17Factory(), new Psr17Factory()),
            new MaintenanceModeService(__DIR__ . '/../../../data/'),
            new PendingChangesService(new GedcomImportService()),
            new TreeService(new GedcomImportService()),
            $mock_upgrade_service,
            new SystemClock(),
        );

        $request  = self::createRequest(HttpRequestMethod::POST->value, ['step' => 'Unzip']);
        $response = $controller->post($request);

        self::assertSame(HttpStatusCode::OK->value, $response->getStatusCode());
    }
}
