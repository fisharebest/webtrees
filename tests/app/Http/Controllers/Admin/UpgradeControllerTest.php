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
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Services\TimeoutService;
use Fisharebest\Webtrees\Services\UpgradeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use League\Flysystem\Filesystem;
use League\Flysystem\Memory\MemoryAdapter;

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
            new Filesystem(new MemoryAdapter()),
            new UpgradeService(new TimeoutService())
        );

        $request  = self::createRequest('GET', ['route' => 'wizard']);
        $response = $controller->wizard($request);

        $this->assertSame(self::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testWizardContinue(): void
    {
        $this->importTree('demo.ged');

        $controller = new UpgradeController(
            new Filesystem(new MemoryAdapter()),
            new UpgradeService(new TimeoutService())
        );

        $request  = self::createRequest('GET', ['route' => 'wizard', 'continue' => '1']);
        $response = $controller->wizard($request);

        $this->assertSame(self::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return void
     */
    public function testStepInvalid(): void
    {
        $controller = new UpgradeController(
            new Filesystem(new MemoryAdapter()),
            new UpgradeService(new TimeoutService())
        );

        $request = self::createRequest('POST', ['route' => 'wizard'], ['step' => 'Invalid']);
        $controller->step($request, null);
    }

    /**
     * @return void
     */
    public function testStepCheckOK(): void
    {
        $mock_upgrade_service = $this->createMock(UpgradeService::class);
        $mock_upgrade_service->method('latestVersion')->willReturn('999.999.999');
        $controller = new UpgradeController(
            new Filesystem(new MemoryAdapter()),
            $mock_upgrade_service
        );

        $request  = self::createRequest('POST', ['route' => 'wizard'], ['step' => 'Check']);
        $response = $controller->step($request, null);

        $this->assertSame(self::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @expectedException \Fisharebest\Webtrees\Exceptions\InternalServerErrorException
     * @return void
     */
    public function testStepCheckUnavailable(): void
    {
        $mock_upgrade_service = $this->createMock(UpgradeService::class);
        $mock_upgrade_service->method('latestVersion')->willReturn('');
        $controller = new UpgradeController(
            new Filesystem(new MemoryAdapter()),
            $mock_upgrade_service
        );

        $request = self::createRequest('POST', ['route' => 'wizard'], ['step' => 'Check']);
        $controller->step($request, null);
    }

    /**
     * @expectedException \Fisharebest\Webtrees\Exceptions\InternalServerErrorException
     * @return void
     */
    public function testStepCheckFail(): void
    {
        $mock_upgrade_service = $this->createMock(UpgradeService::class);
        $mock_upgrade_service->method('latestVersion')->willReturn('0.0.0');
        $controller = new UpgradeController(
            new Filesystem(new MemoryAdapter()),
            $mock_upgrade_service
        );

        $request = self::createRequest('POST', ['route' => 'wizard'], ['step' => 'Check']);
        $controller->step($request, null);
    }

    /**
     * @return void
     */
    public function testStepPrepare(): void
    {
        $controller = new UpgradeController(
            new Filesystem(new MemoryAdapter()),
            new UpgradeService(new TimeoutService())
        );

        $request  = self::createRequest('POST', ['route' => 'wizard'], ['step' => 'Prepare']);
        $response = $controller->step($request, null);

        $this->assertSame(self::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testStepPending(): void
    {
        $controller = new UpgradeController(
            new Filesystem(new MemoryAdapter()),
            new UpgradeService(new TimeoutService())
        );

        $request  = self::createRequest('POST', ['route' => 'wizard'], ['step' => 'Pending']);
        $response = $controller->step($request, null);

        $this->assertSame(self::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @expectedException \Fisharebest\Webtrees\Exceptions\InternalServerErrorException
     * @return void
     */
    public function testStepPendingExist(): void
    {
        $tree = Tree::create('name', 'title');
        $user = (new UserService)->create('user', 'name', 'email', 'password');
        Auth::login($user);
        $tree->createIndividual("0 @@ INDI\n1 NAME Joe Bloggs");

        $controller = new UpgradeController(
            new Filesystem(new MemoryAdapter()),
            new UpgradeService(new TimeoutService())
        );

        $request = self::createRequest('POST', ['route' => 'wizard'], ['step' => 'Pending']);
        $controller->step($request, null);
    }

    /**
     * @return void
     */
    public function testStepExport(): void
    {
        $tree       = $this->importTree('demo.ged');
        $controller = new UpgradeController(
            new Filesystem(new MemoryAdapter()),
            new UpgradeService(new TimeoutService())
        );

        $request  = self::createRequest('POST', ['route' => 'wizard'], ['step' => 'Export']);
        $response = $controller->step($request, $tree);

        $this->assertSame(self::STATUS_OK, $response->getStatusCode());

        // Now overwrite the file we just created
        $response = $controller->step($request, $tree);

        $this->assertSame(self::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @expectedException \Fisharebest\Webtrees\Exceptions\InternalServerErrorException
     * @return void
     */
    public function testStepDownloadFails(): void
    {
        $mock_upgrade_service = $this->createMock(UpgradeService::class);
        $mock_upgrade_service->method('downloadFile')->will($this->throwException(new Exception()));
        $controller = new UpgradeController(
            new Filesystem(new MemoryAdapter()),
            $mock_upgrade_service
        );

        $request = self::createRequest('POST', ['route' => 'wizard'], ['step' => 'Download']);
        $controller->step($request, null);
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
            $mock_upgrade_service
        );

        $request  = self::createRequest('POST', ['route' => 'wizard'], ['step' => 'Download']);
        $response = $controller->step($request, null);

        $this->assertSame(self::STATUS_OK, $response->getStatusCode());
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
            $mock_upgrade_service
        );

        $request  = self::createRequest('POST', ['route' => 'wizard'], ['step' => 'Unzip']);
        $response = $controller->step($request, null);

        $this->assertSame(self::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testStepCopy(): void
    {
        $controller = new UpgradeController(
            new Filesystem(new MemoryAdapter()),
            new UpgradeService(new TimeoutService())
        );

        $request  = self::createRequest('POST', ['route' => 'wizard'], ['step' => 'Copy']);
        $response = $controller->step($request, null);

        $this->assertSame(self::STATUS_OK, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testStepCleanup(): void
    {
        $mock_upgrade_service = $this->createMock(UpgradeService::class);
        $controller           = new UpgradeController(
            new Filesystem(new MemoryAdapter()),
            $mock_upgrade_service
        );

        $request  = self::createRequest('POST', ['route' => 'wizard'], ['step' => 'Cleanup']);
        $response = $controller->step($request, null);

        $this->assertSame(self::STATUS_OK, $response->getStatusCode());
    }
}
