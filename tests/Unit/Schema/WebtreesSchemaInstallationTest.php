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

namespace Fisharebest\Webtrees\Tests\Unit\Schema;

use DateTimeImmutable;
use DateTimeZone;
use Fisharebest\Database\Schema\MigrationMode;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Schema\WebtreesSchema;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tests\TestCase;
use Fisharebest\Webtrees\Webtrees;
use PDO;
use Pdo\Sqlite;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(WebtreesSchema::class)]
class WebtreesSchemaInstallationTest extends TestCase
{
    public function testClass(): void
    {
        self::assertTrue(class_exists(WebtreesSchema::class));
    }

    public function testLatestVersionReturnsCurrentSchema(): void
    {
        $webtrees_schema = new WebtreesSchema();

        self::assertEquals($webtrees_schema->version46(), $webtrees_schema->latestVersion());
    }

    public function testExistingInstallationUsesVersionedSchema(): void
    {
        $pdo = new Sqlite('sqlite::memory:');
        $pdo->exec('PRAGMA foreign_keys = ON');
        DB::attach(pdo: $pdo, prefix: 'wt_');

        DB::migrate(
            target: (new WebtreesSchema())->version37(),
            mode: new MigrationMode(strict_not_null: true),
            execute: true,
        );
        DB::query(table: 'site_setting')->insert(values: [
            'setting_name'  => 'WT_SCHEMA_VERSION',
            'setting_value' => '37',
        ]);

        $preferences      = Site::$preferences;
        Site::$preferences = ['WT_SCHEMA_VERSION' => '37'];

        try {
            $webtrees_schema = new WebtreesSchema();
            self::assertTrue($webtrees_schema->updateSchema());
            self::assertSame(
                (string) Webtrees::SCHEMA_VERSION,
                $pdo->query("SELECT setting_value FROM wt_site_setting WHERE setting_name = 'WT_SCHEMA_VERSION'")->fetchColumn(),
            );
            self::assertFalse($webtrees_schema->updateSchema());
        } finally {
            Site::$preferences = $preferences;
        }
    }

    public function testNewInstallationUsesLatestVersionedSchema(): void
    {
        $pdo = new Sqlite('sqlite::memory:');
        $pdo->exec('PRAGMA foreign_keys = ON');
        DB::attach($pdo);

        $webtrees_schema = new WebtreesSchema();
        Site::$preferences = ['WT_SCHEMA_VERSION' => '0'];

        self::assertTrue($webtrees_schema->updateSchema());
        self::assertSame(
            (string) Webtrees::SCHEMA_VERSION,
            $pdo->query("SELECT setting_value FROM site_setting WHERE setting_name = 'WT_SCHEMA_VERSION'")->fetchColumn(),
        );
        self::assertTrue(DB::introspect()->hasTable(name: 'media_file'));
        self::assertFalse(DB::introspect()->hasTable(name: 'media_mapping'));
        self::assertSame((string) Webtrees::SCHEMA_VERSION, Site::getPreference('WT_SCHEMA_VERSION'));
        self::assertFalse($webtrees_schema->updateSchema());
    }

    public function testSeedDatabaseRestoresDefaultTreeWithoutDuplicatingIt(): void
    {
        self::createDatabase();

        self::assertSame('DEFAULT_TREE', DB::query()
            ->from('gedcom')
            ->whereEqual('gedcom_id', -1)
            ->value('gedcom_name'));

        DB::query()
            ->from('gedcom')
            ->whereEqual('gedcom_id', -1)
            ->update(['gedcom_name' => 'Changed']);

        $webtrees_schema = new WebtreesSchema();
        $webtrees_schema->seedDatabase();
        $webtrees_schema->seedDatabase();

        self::assertSame('DEFAULT_TREE', DB::query()
            ->from('gedcom')
            ->whereEqual('gedcom_id', -1)
            ->value('gedcom_name'));
        self::assertSame(1, DB::query()
            ->from('gedcom')
            ->whereEqual('gedcom_id', -1)
            ->count());
    }

    public function testSeedDatabaseRestoresDefaultUserWithoutDuplicatingIt(): void
    {
        self::createDatabase();

        self::assertSame('DEFAULT_USER', DB::query()
            ->from('user')
            ->whereEqual('user_id', -1)
            ->value('user_name'));

        DB::query()
            ->from('user')
            ->whereEqual('user_id', -1)
            ->update(['real_name' => 'Changed']);

        $webtrees_schema = new WebtreesSchema();
        $webtrees_schema->seedDatabase();
        $webtrees_schema->seedDatabase();

        self::assertSame('DEFAULT_USER', DB::query()
            ->from('user')
            ->whereEqual('user_id', -1)
            ->value('real_name'));
        self::assertSame(1, DB::query()
            ->from('user')
            ->whereEqual('user_id', -1)
            ->count());
    }

    public function testSeedDatabaseRestoresPrivacyDefaultsWithoutDuplicatingThem(): void
    {
        $before = (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s.u');
        self::createDatabase();
        $after = (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s.u');

        $timestamps = DB::query()
            ->from('default_resn')
            ->orderBy(DB::ascending('default_resn_id'))
            ->pluck('updated');

        self::assertCount(5, $timestamps);
        foreach ($timestamps as $timestamp) {
            self::assertGreaterThanOrEqual($before, $timestamp);
            self::assertLessThanOrEqual($after, $timestamp);
        }

        DB::query()
            ->from('default_resn')
            ->whereEqual('default_resn_id', 1)
            ->update(['resn' => 'hidden', 'updated' => '2000-01-01 00:00:00']);
        DB::query(table: 'default_resn')->insert([
            'gedcom_id' => -1,
            'tag_type'  => 'BIRT',
            'resn'      => 'none',
            'updated'   => $after,
        ]);

        $webtrees_schema = new WebtreesSchema();
        $before_update = (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s.u');
        $webtrees_schema->seedDatabase();
        $webtrees_schema->seedDatabase();
        $after_update = (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s.u');

        $updated = DB::query()->from('default_resn')->whereEqual('default_resn_id', 1)->value('updated');
        self::assertGreaterThanOrEqual($before_update, $updated);
        self::assertLessThanOrEqual($after_update, $updated);

        self::assertSame([
            1 => 'SSN',
            2 => 'SOUR',
            3 => 'REPO',
            4 => 'SUBM',
            5 => 'SUBN',
            6 => 'BIRT',
        ], DB::query()
            ->from('default_resn')
            ->orderBy(DB::ascending('default_resn_id'))
            ->pluck('tag_type', 'default_resn_id'));
        self::assertSame([
            1 => 'confidential',
            2 => 'privacy',
            3 => 'privacy',
            4 => 'confidential',
            5 => 'confidential',
            6 => 'none',
        ], DB::query()
            ->from('default_resn')
            ->orderBy(DB::ascending('default_resn_id'))
            ->pluck('resn', 'default_resn_id'));
    }
}
