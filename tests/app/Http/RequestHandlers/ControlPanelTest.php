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
use Fisharebest\Webtrees\Contracts\TimestampInterface;
use Fisharebest\Webtrees\Services\AdminService;
use Fisharebest\Webtrees\Services\HousekeepingService;
use Fisharebest\Webtrees\Services\MessageService;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\ServerCheckService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UpgradeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\TestCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ControlPanel::class)]
class ControlPanelTest extends TestCase
{
    protected static bool $uses_database = true;

    public function testClass(): void
    {
        self::assertTrue(class_exists(ControlPanel::class));
    }

    public function testHandleReturnsOkResponse(): void
    {
        $admin_service = self::createStub(AdminService::class);
        $admin_service->method('multipleTreeThreshold')->willReturn(100);
        $admin_service->method('gedcomFiles')->willReturn(new Collection());

        $housekeeping_service = self::createStub(HousekeepingService::class);
        $housekeeping_service->method('deleteOldWebtreesFiles')->willReturn([]);

        $message_service = self::createStub(MessageService::class);
        $message_service->method('recipientTypes')->willReturn([]);

        $module_service = self::createStub(ModuleService::class);
        $module_service->method('findByInterface')->willReturn(new Collection());
        $module_service->method('all')->willReturn(new Collection());
        $module_service->method('deletedModules')->willReturn(new Collection());
        $module_service->method('otherModules')->willReturn(new Collection());

        $server_check_service = self::createStub(ServerCheckService::class);
        $server_check_service->method('serverErrors')->willReturn(new Collection());
        $server_check_service->method('serverWarnings')->willReturn(new Collection());

        $tree_service = self::createStub(TreeService::class);
        $tree_service->method('all')->willReturn(new Collection());

        $timestamp = self::createStub(TimestampInterface::class);

        $upgrade_service = self::createStub(UpgradeService::class);
        $upgrade_service->method('latestVersion')->willReturn('');
        $upgrade_service->method('latestVersionError')->willReturn('');
        $upgrade_service->method('latestVersionTimestamp')->willReturn($timestamp);

        $user_service = self::createStub(UserService::class);
        $user_service->method('all')->willReturn(new Collection());
        $user_service->method('administrators')->willReturn(new Collection());
        $user_service->method('managers')->willReturn(new Collection());
        $user_service->method('moderators')->willReturn(new Collection());
        $user_service->method('unapproved')->willReturn(new Collection());
        $user_service->method('unverified')->willReturn(new Collection());

        $handler = new ControlPanel(
            $admin_service,
            $housekeeping_service,
            $message_service,
            $module_service,
            $server_check_service,
            $tree_service,
            $upgrade_service,
            $user_service,
        );

        $request  = self::createRequest();
        $response = $handler->handle($request);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }
}
