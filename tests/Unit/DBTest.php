<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Tests\Unit;

use DateTimeImmutable;
use DateTimeZone;
use Fisharebest\Database\Driver;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Tests\TestCase;
use Pdo\Sqlite;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(DB::class)]
class DBTest extends TestCase
{
    public function testConnectUsesDirectPdoAndEnablesForeignKeys(): void
    {
        DB::connect(
            driver: Driver::SQLite->value,
            host: '',
            port: '',
            database: ':memory:',
            username: '',
            password: '',
            prefix: 'wt_',
            key: '',
            certificate: '',
            ca: '',
            verify_certificate: false,
        );

        self::assertInstanceOf(Sqlite::class, DB::connection()->pdo());
        self::assertSame(1, (int) DB::connection()->queryOne('PRAGMA foreign_keys'));
        self::assertSame(5000, (int) DB::connection()->queryOne('PRAGMA busy_timeout'));
        self::assertSame(-16000, (int) DB::connection()->queryOne('PRAGMA cache_size'));
        DB::connection()->exec('CREATE TABLE wt_probe (id INTEGER PRIMARY KEY)');
        self::assertTrue(DB::introspect()->hasTable(name: 'probe'));
    }

    public function testNowCanBeUsedInInsertAndUpdate(): void
    {
        self::createDatabase();

        $before = new DateTimeImmutable('now', new DateTimeZone('UTC'));
        DB::query(table: 'log')->insert([
            'log_type'    => 'debug',
            'log_message' => 'now',
            'ip_address'  => '127.0.0.1',
            'log_time'    => DB::now(),
        ]);
        $log_time = DB::query(table: 'log')->value('log_time');
        self::assertMatchesRegularExpression('/^\d{4}-\d\d-\d\d \d\d:\d\d:\d\d\.\d{6}$/', $log_time);
        $inserted = DateTimeImmutable::createFromFormat('!Y-m-d H:i:s.u', $log_time, new DateTimeZone('UTC'));
        self::assertInstanceOf(DateTimeImmutable::class, $inserted);
        self::assertGreaterThanOrEqual($before, $inserted);
        self::assertLessThanOrEqual(new DateTimeImmutable('now', new DateTimeZone('UTC')), $inserted);

        DB::query(table: 'log')->whereEqual('log_message', 'now')->update(['log_time' => DB::now()]);
        $updated = DB::query(table: 'log')->value('log_time');
        self::assertMatchesRegularExpression('/^\d{4}-\d\d-\d\d \d\d:\d\d:\d\d\.\d{6}$/', $updated);
        self::assertGreaterThanOrEqual($log_time, $updated);
        self::assertLessThanOrEqual(
            (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s.u'),
            $updated,
        );
    }
}
