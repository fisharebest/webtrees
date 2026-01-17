<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\TestCase;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;

use function file_put_contents;

#[CoversClass(MaintenanceModeService::class)]
class MaintenanceModeServiceTest extends TestCase
{
    private const string TEST_DATA_DIR = __DIR__ . '/../../data/';

    public function testInvalidFolder(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(__DIR__ . '/non-existent does not exist');

        new MaintenanceModeService(__DIR__ . '/non-existent');
    }

    public function testOfflineAndOnline(): void
    {
        $service = new MaintenanceModeService(self::TEST_DATA_DIR);

        self::assertFalse($service->isOffline());

        $service->offline();

        self::assertTrue($service->isOffline());

        $service->online();

        self::assertFalse($service->isOffline());
    }

    public function testMessage(): void
    {
        $service = new MaintenanceModeService(self::TEST_DATA_DIR);

        $service->offline('foo bar');

        self::assertSame('foo bar', $service->message());

        $service->online();
    }

    public function testOfflineFileIsAFolder(): void
    {
        $service = new MaintenanceModeService(self::TEST_DATA_DIR);

        mkdir(self::TEST_DATA_DIR . 'offline.txt', 0777, true);

        self::assertTrue($service->isOffline());
        self::assertSame('', $service->message());

        $service->online();

        self::assertFalse($service->isOffline());
    }

    public function testOfflineFileIsUnreadable(): void
    {
        $service = new MaintenanceModeService(self::TEST_DATA_DIR);

        file_put_contents(self::TEST_DATA_DIR . 'offline.txt', 'foo');
        chmod(self::TEST_DATA_DIR . 'offline.txt', 0);

        self::assertTrue($service->isOffline());
        self::assertSame('', $service->message());

        $service->online();

        self::assertFalse($service->isOffline());
    }

    public function testOfflineFileIsSymbolicLink(): void
    {
        $service = new MaintenanceModeService(self::TEST_DATA_DIR);

        file_put_contents(self::TEST_DATA_DIR . 'foo', 'foo');
        symlink(self::TEST_DATA_DIR . 'foo', self::TEST_DATA_DIR . 'offline.txt');

        self::assertTrue($service->isOffline());
        self::assertSame('foo', $service->message());

        unlink(self::TEST_DATA_DIR . 'foo');

        self::assertTrue($service->isOffline());
        self::assertSame('', $service->message());

        $service->online();

        self::assertFalse($service->isOffline());
    }
}
