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
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\GuestUser;
use Fisharebest\Webtrees\Http\Controllers\SiteLogs;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

use function date;

#[CoversClass(SiteLogs::class)]
class SiteLogsTest extends TestCase
{
    public function testDateRangeUsesFirstAndLastLogs(): void
    {
        self::createDatabase();

        $controller = new SiteLogs(
            new GuestUser(),
            new TreeService(new GedcomImportService()),
            new UserService(new SystemClock()),
        );
        $request = self::createRequest();

        DB::query(table: 'log')->delete();
        $empty_response = $controller->get($request);
        $today          = date('Y-m-d');
        self::assertStringContainsString('max="' . $today . '" min="' . $today . '"', (string) $empty_response->getBody());

        DB::query(table: 'log')->delete();
        DB::query(table: 'log')->insertBatch([
            ['log_time' => '2024-06-15 12:00:00', 'log_type' => 'debug', 'log_message' => 'Later', 'ip_address' => '127.0.0.1'],
            ['log_time' => '2020-03-10 09:00:00', 'log_type' => 'debug', 'log_message' => 'Earlier', 'ip_address' => '127.0.0.1'],
        ]);

        $response = $controller->get($request);

        self::assertStringContainsString('max="2024-06-15" min="2020-03-10"', (string) $response->getBody());
        self::assertStringContainsString('name="from" value="2020-03-10"', (string) $response->getBody());
        self::assertStringContainsString('name="to" value="2024-06-15"', (string) $response->getBody());
    }
}
