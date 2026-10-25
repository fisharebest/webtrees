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

use Fisharebest\Webtrees\Clock\SystemClock;
use Fisharebest\Webtrees\Enums\HttpStatusCode;
use Fisharebest\Webtrees\Http\Controllers\ControlPanel;
use Fisharebest\Webtrees\Services\AdminService;
use Fisharebest\Webtrees\Services\EmailService;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\HousekeepingService;
use Fisharebest\Webtrees\Services\MessageService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\PhpService;
use Fisharebest\Webtrees\Services\ServerCheckService;
use Fisharebest\Webtrees\Services\TimeoutService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UpgradeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

#[CoversClass(ControlPanel::class)]
class ControlPanelControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::createDatabase();
    }

    public function testControlPanel(): void
    {
        $admin_service         = new AdminService();
        $message_service       = new MessageService(email_service: new EmailService(), user_service: new UserService(new SystemClock()));
        $module_service        = new ModuleService();
        $housekeeping_service  = new HousekeepingService(new SystemClock());
        $server_check_service  = new ServerCheckService(php_service: new PhpService());
        $timeout_service       = new TimeoutService(php_service: new PhpService(), clock: new SystemClock());
        $gedcom_import_service = new GedcomImportService();
        $tree_service          = new TreeService($gedcom_import_service);
        $upgrade_service       = new UpgradeService(
            self::createStub(ClientInterface::class),
            self::createStub(RequestFactoryInterface::class),
            $timeout_service,
            new SystemClock(),
        );
        $user_service          = new UserService(new SystemClock());
        $controller            = new ControlPanel(
            $admin_service,
            $housekeeping_service,
            message_service: $message_service,
            module_service: $module_service,
            server_check_service: $server_check_service,
            tree_service: $tree_service,
            upgrade_service: $upgrade_service,
            user_service: $user_service,
            clock: new SystemClock(),
        );
        $request               = self::createRequest();
        $response              = $controller->get();

        self::assertSame(HttpStatusCode::OK->value, $response->getStatusCode());
    }
}
