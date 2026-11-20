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

namespace Fisharebest\Webtrees\Tests\Migration;

use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Schema\WebtreesSchema;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Webtrees;
use PDO;
use Pdo\Sqlite;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function array_map;
use function basename;
use function file_get_contents;
use function glob;
use function preg_match;
use function sprintf;

#[CoversNothing]
final class SchemaFixtureTest extends TestCase
{
    private const array FIXTURES = [
        '1.0.0.mysql.sql',
        '1.0.4.mysql.sql',
        '1.0.5.mysql.sql',
        '1.0.6.mysql.sql',
        '1.1.0.mysql.sql',
        '1.1.2.mysql.sql',
        '1.2.0.mysql.sql',
        '1.2.1.mysql.sql',
        '1.2.4.mysql.sql',
        '1.2.7.mysql.sql',
        '1.3.0.mysql.sql',
        '1.3.1.mysql.sql',
        '1.3.2.mysql.sql',
        '1.4.0.mysql.sql',
        '1.4.2.mysql.sql',
        '1.4.5.mysql.sql',
        '1.5.1.mysql.sql',
        '1.6.1.mysql.sql',
        '1.7.0.mysql.sql',
        '1.7.2.mysql.sql',
        '1.7.3.mysql.sql',
        '1.7.5.mysql.sql',
        '1.7.8.mysql.sql',
        '2.0.0.mysql.sql',
        '2.0.12.mysql.sql',
        '2.1.0.mariadb.sql',
        '2.1.0.postgresql.sql',
        '2.1.0.sqlite.sql',
        '2.2.0.sqlserver.sql',
        '2.2.6.mariadb.sql',
        '2.2.6.mysql.sql',
        '2.2.6.postgresql.sql',
        '2.2.6.sqlite.sql',
        '2.2.6.sqlserver.sql',
    ];

    /**
     * @return iterable<string,array{string}>
     */
    public static function fixtures(): iterable
    {
        foreach (self::FIXTURES as $fixture) {
            yield $fixture => [__DIR__ . '/fixtures/' . $fixture];
        }
    }

    public function testFixtureCollectionMatchesReleaseSchemas(): void
    {
        $fixtures = glob(__DIR__ . '/fixtures/*.sql');
        self::assertNotFalse($fixtures);
        self::assertSame(
            self::FIXTURES,
            array_map(basename(...), $fixtures),
        );
    }

    public function testTaggedSQLiteSchemaUpgradesThroughWebtreesSchema(): void
    {
        $fixture = file_get_contents(__DIR__ . '/fixtures/2.1.0.sqlite.sql');
        self::assertNotFalse($fixture);

        $pdo = new Sqlite('sqlite::memory:');
        $pdo->exec('PRAGMA foreign_keys = ON');
        self::assertNotFalse($pdo->exec($fixture));
        DB::attach(pdo: $pdo, prefix: 'wt_');
        DB::query(table: 'site_setting')->insert(values: [
            'setting_name'  => 'WT_SCHEMA_VERSION',
            'setting_value' => '45',
        ]);
        DB::query(table: 'user')->insert(values: [
            'user_name' => 'fixture-user',
            'real_name' => 'Fixture User',
            'email'     => 'fixture@example.com',
            'password'  => 'test',
        ]);

        $preferences      = Site::$preferences;
        Site::$preferences = [];

        try {
            $webtrees_schema = new WebtreesSchema();
            self::assertTrue($webtrees_schema->updateSchema());
            self::assertSame(
                (string) Webtrees::SCHEMA_VERSION,
                $pdo->query("SELECT setting_value FROM wt_site_setting WHERE setting_name = 'WT_SCHEMA_VERSION'")->fetchColumn(),
            );
            self::assertSame('fixture-user', $pdo->query('SELECT user_name FROM wt_user WHERE user_id = 1')->fetchColumn());
            self::assertTrue(DB::introspect()->getTable(name: 'gedcom')->hasColumn(name: 'media_folder'));
            self::assertFalse($webtrees_schema->updateSchema());
        } finally {
            Site::$preferences = $preferences;
        }
    }

    #[DataProvider('fixtures')]
    public function testSchemaFixture(string $fixture): void
    {
        self::assertFileExists($fixture);
        $sql = file_get_contents($fixture);
        self::assertNotFalse($sql);
        self::assertSame(1, preg_match('/^\d+\.\d+\.\d+\.(mysql|mariadb|sqlite|sqlserver|postgresql)\.sql$/', basename($fixture)));
        self::assertMatchesRegularExpression('/\bCREATE\s+TABLE\b/i', $sql);

        if (preg_match('/\.sqlite\.sql$/', $fixture) !== 1) {
            return;
        }

        $pdo = new Sqlite('sqlite::memory:');
        self::assertNotFalse($pdo->exec($sql), sprintf('Could not load %s: %s', basename($fixture), $pdo->errorInfo()[2]));
        self::assertGreaterThan(
            0,
            (int) $pdo->query("SELECT COUNT(*) FROM sqlite_master WHERE type = 'table' AND name LIKE 'wt_%'")->fetchColumn(),
        );
    }
}
