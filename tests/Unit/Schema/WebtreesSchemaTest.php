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

use Fisharebest\Database\Schema\Action;
use Fisharebest\Database\Schema\ColumnInterface;
use Fisharebest\Database\Schema\DatetimeType;
use Fisharebest\Database\Schema\Index;
use Fisharebest\Database\Schema\IntegerType;
use Fisharebest\Database\Schema\MigrationMode;
use Fisharebest\Database\Schema\Table;
use Fisharebest\Database\Schema\VarcharType;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Module\ModuleSidebarInterface;
use Fisharebest\Webtrees\Module\ModuleTabInterface;
use Fisharebest\Webtrees\Schema\WebtreesDataMigrations;
use Fisharebest\Webtrees\Schema\WebtreesSchema;
use Fisharebest\Webtrees\Tests\TestCase;
use Fisharebest\Webtrees\Webtrees;
use PDO;
use Pdo\Sqlite;
use PHPUnit\Framework\Attributes\CoversClass;
use RuntimeException;
use UnexpectedValueException;

use function array_keys;
use function array_map;
use function str_repeat;
use function strlen;

#[CoversClass(WebtreesSchema::class)]
class WebtreesSchemaTest extends TestCase
{
    public function testUpdateRejectsUnmappedModulePrivacyData(): void
    {
        $pdo = new Sqlite('sqlite::memory:');
        $pdo->exec('PRAGMA foreign_keys = ON');
        DB::attach($pdo);

        $webtrees_schema = new WebtreesSchema();

        DB::migrate(
            target: $webtrees_schema->version42(),
            mode: new MigrationMode(strict_not_null: true),
            execute: true,
        );
        DB::query(table: 'gedcom')->insert(values: ['gedcom_name' => 'example']);
        DB::query(table: 'module')->insert(values: ['module_name' => 'example']);
        DB::query(table: 'module_privacy')->insert(values: [
            'module_name'  => 'example',
            'gedcom_id'    => 1,
            'component'    => 'unknown',
            'access_level' => 2,
        ]);
        DB::query(table: 'site_setting')->insert(values: [
            'setting_name'  => 'WT_SCHEMA_VERSION',
            'setting_value' => '42',
        ]);

        try {
            $webtrees_schema->updateSchema();
            self::fail('Unmapped components must not be discarded');
        } catch (UnexpectedValueException $exception) {
            self::assertSame('Unknown module privacy component "unknown"', $exception->getMessage());
        }

        self::assertSame(
            ['component' => 'unknown', 'id' => null, 'interface' => null],
            $pdo->query('SELECT component, id, interface FROM module_privacy')->fetch(PDO::FETCH_ASSOC),
        );
        self::assertSame(
            '42',
            $pdo->query("SELECT setting_value FROM site_setting WHERE setting_name = 'WT_SCHEMA_VERSION'")->fetchColumn(),
        );

        DB::query(table: 'module_privacy')
            ->whereEqual(column: 'component', value: 'unknown')
            ->update(values: ['component' => 'sidebar']);
        $webtrees_schema->updateSchema();

        self::assertSame(
            ModuleSidebarInterface::class,
            $pdo->query('SELECT interface FROM module_privacy')->fetchColumn(),
        );
    }

    public function testUpdateDoesNotDiscardUnmigratedPlaces(): void
    {
        $pdo = new Sqlite('sqlite::memory:');
        $pdo->exec('PRAGMA foreign_keys = ON');
        DB::attach($pdo);

        $webtrees_schema = new WebtreesSchema();

        DB::migrate(
            target: $webtrees_schema->version44(),
            mode: new MigrationMode(strict_not_null: true),
            execute: true,
        );
        $pdo->exec('CREATE TABLE placelocation (pl_id INTEGER PRIMARY KEY, pl_place VARCHAR(255) NOT NULL)');
        $pdo->exec("INSERT INTO placelocation (pl_id, pl_place) VALUES (1, 'London')");
        DB::query(table: 'site_setting')->insert(values: [
            'setting_name'  => 'WT_SCHEMA_VERSION',
            'setting_value' => '44',
        ]);

        try {
            $webtrees_schema->updateSchema();
            self::fail('A non-MySQL legacy place table must not be discarded');
        } catch (RuntimeException $exception) {
            self::assertSame(
                'Legacy placelocation contains data on a non-MySQL database; cannot import it safely',
                $exception->getMessage(),
            );
        }

        self::assertSame('London', $pdo->query('SELECT pl_place FROM placelocation')->fetchColumn());
        self::assertSame(
            '44',
            $pdo->query("SELECT setting_value FROM site_setting WHERE setting_name = 'WT_SCHEMA_VERSION'")->fetchColumn(),
        );
    }

    public function testUpdateCreatesLatestSchema(): void
    {
        $pdo = new Sqlite('sqlite::memory:');
        $pdo->exec('PRAGMA foreign_keys = ON');
        DB::attach(pdo: $pdo, prefix: 'wt_');

        $webtrees_schema = new WebtreesSchema();

        self::assertTrue($webtrees_schema->updateSchema());

        self::assertSame(
            (string) Webtrees::SCHEMA_VERSION,
            $pdo->query("SELECT setting_value FROM wt_site_setting WHERE setting_name = 'WT_SCHEMA_VERSION'")->fetchColumn(),
        );
        $schema = DB::introspect();
        self::assertTrue($schema->hasTable(name: 'media_file'));
        self::assertTrue($schema->hasTable(name: 'favorite'));
        self::assertTrue($schema->hasTable(name: 'news'));
        self::assertTrue($schema->hasTable(name: 'place_location'));
        self::assertFalse($schema->hasTable(name: 'next_id'));
        self::assertFalse($schema->hasTable(name: 'site_access_rule'));
        self::assertFalse($schema->hasTable(name: 'ip_address'));
        self::assertFalse($schema->hasTable(name: 'media_mapping'));
        self::assertTrue($schema->getTable(name: 'gedcom')->hasColumn(name: 'media_folder'));
        self::assertNull($schema->getTable(name: 'dates')->getPrimaryKey());
        self::assertSame(1, (int) $pdo->query('PRAGMA foreign_keys')->fetchColumn());

        self::assertFalse($webtrees_schema->updateSchema());
    }

    public function testVersion47IndexesChangeQueries(): void
    {
        $pdo = new Sqlite('sqlite::memory:');
        $pdo->exec('PRAGMA foreign_keys = ON');
        DB::attach($pdo);

        $webtrees_schema = new WebtreesSchema();

        DB::migrate(
            target: $webtrees_schema->version46(),
            mode: new MigrationMode(strict_not_null: true),
            execute: true,
        );
        DB::query(table: 'gedcom')->insert(['gedcom_id' => 1, 'gedcom_name' => 'example']);
        DB::query(table: 'user')->insert(['user_id' => 1, 'user_name' => 'example', 'real_name' => 'Example', 'email' => 'example@example.com', 'password' => '']);
        DB::query(table: 'change')->insert([
            'change_time' => '2026-01-01 00:00:00',
            'status'      => 'accepted',
            'gedcom_id'   => 1,
            'xref'        => 'I1',
            'old_gedcom' => '',
            'new_gedcom' => '0 @I1@ INDI',
            'user_id'     => 1,
        ]);

        DB::migrate(
            target: $webtrees_schema->version47(),
            mode: new MigrationMode(strict_not_null: true),
            execute: true,
        );

        $change = DB::introspect()->getTable(name: 'change');
        self::assertSame(
            ['change_ix1', 'change_ix2', 'change_ix3', 'change_ix4', 'change_ix5', 'change_ix6', 'change_ix7'],
            array_map(static fn (Index $index): string => $index->getName(), $change->getIndexes()),
        );
        self::assertSame([
            ['gedcom_id', 'status', 'xref'],
            ['user_id'],
            ['status'],
            ['xref'],
            ['change_time'],
            ['gedcom_id', 'change_time'],
            ['gedcom_id', 'status', 'change_id'],
        ], array_map(static fn (Index $index): array => $index->getColumns(), $change->getIndexes()));
        self::assertSame('I1', $pdo->query('SELECT xref FROM change WHERE change_id = 1')->fetchColumn());

        foreach (
            [
                "SELECT xref FROM change WHERE gedcom_id = 1 AND status = 'pending' GROUP BY xref" => 'change_ix1',
                "SELECT 1 FROM change WHERE status = 'pending' LIMIT 1" => 'change_ix3',
                "SELECT 1 FROM change WHERE xref = 'I1' LIMIT 1" => 'change_ix4',
                'SELECT change_time FROM change ORDER BY change_time LIMIT 1' => 'change_ix5',
                "SELECT 1 FROM change WHERE gedcom_id = 1 AND change_time >= '2026-01-01' LIMIT 1" => 'change_ix6',
                "SELECT change_time FROM change WHERE gedcom_id = 1 AND status = 'accepted' ORDER BY change_id DESC LIMIT 1" => 'change_ix7',
            ] as $query => $index
        ) {
            $plan = $pdo->query('EXPLAIN QUERY PLAN ' . $query)->fetchAll(PDO::FETCH_COLUMN, 3);
            self::assertStringContainsString($index, implode(' ', $plan), $query);
        }
    }

    public function testHistoricalMediaIsTransferredBeforeReplacingItsColumnsAndPrimaryKey(): void
    {
        $pdo = new Sqlite('sqlite::memory:');
        $pdo->exec('PRAGMA foreign_keys = ON');
        DB::attach($pdo);

        $webtrees_schema = new WebtreesSchema();

        DB::migrate(
            target: $webtrees_schema->version20(),
            mode: new MigrationMode(strict_not_null: true),
            execute: true,
        );
        DB::query(table: 'media')->insert(values: [
            'm_media'   => 'M1',
            'm_file'    => 'photo.jpg',
            'm_gedfile' => 1,
            'm_ext'     => 'jpg',
            'm_titl'    => 'Portrait',
            'm_gedrec'  => "0 @M1@ OBJE\n3 TYPE portrait",
        ]);
        DB::query(table: 'media')->insert(values: [
            'm_media'   => 'M1',
            'm_file'    => 'other.jpg',
            'm_gedfile' => 2,
            'm_ext'     => 'jpg',
            'm_titl'    => 'Other',
            'm_gedrec'  => '0 @M1@ OBJE',
        ]);
        DB::query(table: 'site_setting')->insert(values: [
            'setting_name'  => 'WT_SCHEMA_VERSION',
            'setting_value' => '20',
        ]);

        $server_name = $_SERVER['SERVER_NAME'] ?? null;
        unset($_SERVER['SERVER_NAME']);

        try {
            try {
                $webtrees_schema->updateSchema();
                self::fail('The later email migration requires a configured server name');
            } catch (RuntimeException $exception) {
                self::assertStringContainsString('Version 31 requires SERVER_NAME', $exception->getMessage());
            }
        } finally {
            if ($server_name !== null) {
                $_SERVER['SERVER_NAME'] = $server_name;
            }
        }

        self::assertSame('30', $pdo->query("SELECT setting_value FROM site_setting WHERE setting_name = 'WT_SCHEMA_VERSION'")->fetchColumn());
        self::assertSame([
            ['m_id' => 'M1', 'm_file' => 1, 'm_filename' => 'photo.jpg', 'm_gedcom' => "0 @M1@ OBJE\n3 TYPE portrait", 'm_type' => 'portrait'],
            ['m_id' => 'M1', 'm_file' => 2, 'm_filename' => 'other.jpg', 'm_gedcom' => '0 @M1@ OBJE', 'm_type' => null],
        ], $pdo->query('SELECT m_id, m_file, m_filename, m_gedcom, m_type FROM media ORDER BY m_file')->fetchAll(PDO::FETCH_ASSOC));
        self::assertSame(['m_file', 'm_id'], DB::introspect()->getTable(name: 'media')->getPrimaryKey()?->getColumns());
        self::assertFalse(DB::introspect()->getTable(name: 'media')->hasColumn(name: 'm_media'));
        self::assertFalse(DB::introspect()->getTable(name: 'media')->hasColumn(name: 'm_id_21'));
        self::assertFalse(DB::introspect()->getTable(name: 'media')->hasColumn(name: 'm_file_21'));
    }

    public function testHistoricalMediaRejectsDuplicateIdentifiersBeforeStaging(): void
    {
        $pdo = new Sqlite('sqlite::memory:');
        $pdo->exec('PRAGMA foreign_keys = ON');
        DB::attach($pdo);

        $webtrees_schema = new WebtreesSchema();

        DB::migrate(
            target: $webtrees_schema->version20(),
            mode: new MigrationMode(strict_not_null: true),
            execute: true,
        );
        DB::query(table: 'media')->insert(values: ['m_media' => 'M1', 'm_file' => 'one.jpg', 'm_gedfile' => 1]);
        DB::query(table: 'media')->insert(values: ['m_media' => 'M1', 'm_file' => 'two.jpg', 'm_gedfile' => 1]);
        DB::query(table: 'site_setting')->insert(values: ['setting_name' => 'WT_SCHEMA_VERSION', 'setting_value' => '20']);

        try {
            $webtrees_schema->updateSchema();
            self::fail('Duplicate identifiers cannot become a composite primary key');
        } catch (RuntimeException $exception) {
            self::assertSame('Legacy media contains duplicate identifiers within a tree', $exception->getMessage());
        }

        self::assertSame('20', $pdo->query("SELECT setting_value FROM site_setting WHERE setting_name = 'WT_SCHEMA_VERSION'")->fetchColumn());
        self::assertTrue(DB::introspect()->getTable(name: 'media')->hasColumn(name: 'm_media'));
        self::assertSame(2, (int) $pdo->query('SELECT COUNT(*) FROM media')->fetchColumn());
    }

    public function testHistoricalUpgradeStopsAtUnregisteredModules(): void
    {
        $pdo = new Sqlite('sqlite::memory:');
        $pdo->exec('PRAGMA foreign_keys = ON');
        DB::attach($pdo);

        $webtrees_schema = new WebtreesSchema();

        DB::migrate(
            target: $webtrees_schema->version1(),
            mode: new MigrationMode(strict_not_null: true),
            execute: true,
        );
        DB::query(table: 'gedcom')->insert(values: [
            'gedcom_name'   => 'example',
            'import_gedcom' => 'Interrupted import',
            'import_offset' => 42,
        ]);
        DB::query(table: 'remotelinks')->insert(values: [
            'r_gid'  => 'I1',
            'r_file' => 1,
        ]);
        DB::query(table: 'site_setting')->insert(values: [
            'setting_name'  => 'WT_SCHEMA_VERSION',
            'setting_value' => '1',
        ]);

        try {
            $webtrees_schema->updateSchema();
            self::fail('Historical module defaults must not be silently skipped');
        } catch (RuntimeException $exception) {
            self::assertStringContainsString('Version 17 requires legacy module', $exception->getMessage());
        }

        self::assertSame('16', $pdo->query("SELECT setting_value FROM site_setting WHERE setting_name = 'WT_SCHEMA_VERSION'")->fetchColumn());
        self::assertTrue(DB::introspect()->hasTable(name: 'session'));
        self::assertTrue(DB::introspect()->hasTable(name: 'gedcom_chunk'));
        self::assertFalse(DB::introspect()->getTable(name: 'gedcom')->hasColumn(name: 'import_gedcom'));
        self::assertFalse(DB::introspect()->getTable(name: 'gedcom')->hasColumn(name: 'import_offset'));
        self::assertSame('example', $pdo->query('SELECT gedcom_name FROM gedcom')->fetchColumn());
        self::assertFalse(DB::introspect()->hasTable(name: 'remotelinks'));
        self::assertTrue(DB::introspect()->getTable(name: 'media')->hasColumn(name: 'm_media'));
    }

    public function testHistoricalVersionSchemasAreValid(): void
    {
        $pdo = new Sqlite('sqlite::memory:');
        $pdo->exec('PRAGMA foreign_keys = ON');
        DB::attach($pdo);

        $webtrees_schema = new WebtreesSchema();

        $schemas         = [
            $webtrees_schema->version1(),
            $webtrees_schema->version2(),
            $webtrees_schema->version3(),
            $webtrees_schema->version7(),
            $webtrees_schema->version18(),
            $webtrees_schema->version20(),
            $webtrees_schema->version21(),
            $webtrees_schema->version24(),
            $webtrees_schema->version28(),
            $webtrees_schema->version30(),
            $webtrees_schema->version37(),
        ];

        foreach ($schemas as $schema) {
            DB::migrate(
                target: $schema,
                mode: new MigrationMode(strict_not_null: false, drop_orphan_columns: true, drop_orphan_tables: true),
                execute: true,
            );
            self::assertSame($schema->hasTable(name: 'media_mapping'), DB::introspect()->hasTable(name: 'media_mapping'));
        }
    }

    public function testEmptyHistoricalMediaCanUpgradeUntilModuleDiscovery(): void
    {
        $pdo = new Sqlite('sqlite::memory:');
        $pdo->exec('PRAGMA foreign_keys = ON');
        DB::attach($pdo);

        $webtrees_schema = new WebtreesSchema();

        DB::migrate(
            target: $webtrees_schema->version20(),
            mode: new MigrationMode(strict_not_null: true),
            execute: true,
        );
        DB::query(table: 'gedcom')->insert(values: ['gedcom_id' => -1, 'gedcom_name' => 'DEFAULT_TREE']);
        DB::query(table: 'media_mapping')->insert(values: [
            'mm_media'   => 'M1',
            'mm_gid'     => 'I1',
            'mm_gedfile' => 1,
            'mm_gedrec'  => '',
        ]);
        DB::query(table: 'ip_address')->insert(values: [
            'ip_address' => '192.0.2.1',
            'category'   => 'allow',
            'comment'    => 'Obsolete rule',
        ]);
        DB::query(table: 'site_setting')->insert(values: [
            'setting_name'  => 'WT_SCHEMA_VERSION',
            'setting_value' => '20',
        ]);
        $server_name = $_SERVER['SERVER_NAME'] ?? null;
        $_SERVER['SERVER_NAME'] = 'example.test';

        try {
            try {
                $webtrees_schema->updateSchema();
                self::fail('Historical module discovery must not be silently skipped');
            } catch (RuntimeException $exception) {
                self::assertStringContainsString('Version 35 requires registration', $exception->getMessage());
            }
        } finally {
            if ($server_name === null) {
                unset($_SERVER['SERVER_NAME']);
            } else {
                $_SERVER['SERVER_NAME'] = $server_name;
            }
        }

        self::assertSame('34', $pdo->query("SELECT setting_value FROM site_setting WHERE setting_name = 'WT_SCHEMA_VERSION'")->fetchColumn());
        self::assertFalse(DB::introspect()->getTable(name: 'media')->hasColumn(name: 'm_media'));
        self::assertFalse(DB::introspect()->hasTable(name: 'media_mapping'));
        self::assertFalse(DB::introspect()->hasTable(name: 'ip_address'));
        self::assertTrue(DB::introspect()->hasTable(name: 'site_access_rule'));
    }

    public function testVersion14DropsDerivedDeathFlag(): void
    {
        $pdo = new Sqlite('sqlite::memory:');
        $pdo->exec('PRAGMA foreign_keys = ON');
        DB::attach($pdo);

        $webtrees_schema = new WebtreesSchema();

        DB::migrate(
            target: $webtrees_schema->version13(),
            mode: new MigrationMode(strict_not_null: true),
            execute: true,
        );
        DB::query(table: 'gedcom')->insert(['gedcom_id' => 1, 'gedcom_name' => 'example']);
        DB::query(table: 'individuals')->insert([
            'i_id' => 'I1', 'i_file' => 1, 'i_rin' => '', 'i_sex' => 'U',
            'i_gedcom' => "0 @I1@ INDI\n1 DEAT Y", 'i_isdead' => 1,
        ]);

        DB::migrate(
            target: $webtrees_schema->version14(),
            mode: new MigrationMode(strict_not_null: true, drop_orphan_columns: true),
            execute: true,
        );

        self::assertFalse(DB::hasColumn(table: 'individuals', column: 'i_isdead'));
        self::assertSame("0 @I1@ INDI\n1 DEAT Y", DB::query()
            ->from('individuals')
            ->whereEqual('i_id', 'I1')
            ->value('i_gedcom'));
    }

    public function testVersion4MigratesRelationshipLimitsOverExistingSettings(): void
    {
        $pdo = new Sqlite('sqlite::memory:');
        $pdo->exec('PRAGMA foreign_keys = ON');
        DB::attach($pdo);

        $webtrees_schema = new WebtreesSchema();

        DB::migrate(
            target: $webtrees_schema->version3(),
            mode: new MigrationMode(strict_not_null: true),
            execute: true,
        );
        DB::query(table: 'user')->insert([
            'user_id' => 1, 'user_name' => 'example', 'real_name' => 'Example',
            'email' => 'example@example.com', 'password' => 'password',
        ]);
        DB::query(table: 'gedcom')->insert(['gedcom_id' => 1, 'gedcom_name' => 'first']);
        DB::query(table: 'gedcom')->insert(['gedcom_id' => 2, 'gedcom_name' => 'second']);
        DB::query(table: 'user_setting')->insert(['user_id' => 1, 'setting_name' => 'max_relation_path', 'setting_value' => '5']);
        DB::query(table: 'user_setting')->insert(['user_id' => 1, 'setting_name' => 'relationship_privacy', 'setting_value' => '1']);
        foreach ([1 => '3', 2 => '4'] as $tree_id => $limit) {
            DB::query(table: 'gedcom_setting')->insert(['gedcom_id' => $tree_id, 'setting_name' => 'MAX_RELATION_PATH_LENGTH', 'setting_value' => $limit]);
            DB::query(table: 'gedcom_setting')->insert(['gedcom_id' => $tree_id, 'setting_name' => 'USE_RELATIONSHIP_PRIVACY', 'setting_value' => '1']);
        }
        DB::query(table: 'user_gedcom_setting')->insert([
            'user_id' => 1, 'gedcom_id' => 2, 'setting_name' => 'RELATIONSHIP_PATH_LENGTH', 'setting_value' => '2',
        ]);

        $webtrees_schema->migrateToVersion4();

        self::assertSame('3', DB::query()
            ->from('user_gedcom_setting')
            ->whereEqual('user_id', 1)
            ->whereEqual('gedcom_id', 1)
            ->whereEqual('setting_name', 'RELATIONSHIP_PATH_LENGTH')
            ->value('setting_value'));
        self::assertSame('4', DB::query()
            ->from('user_gedcom_setting')
            ->whereEqual('user_id', 1)
            ->whereEqual('gedcom_id', 2)
            ->whereEqual('setting_name', 'RELATIONSHIP_PATH_LENGTH')
            ->value('setting_value'));
    }

    public function testVersion4NormalizesMc0Surnames(): void
    {
        $pdo = new Sqlite('sqlite::memory:');
        $pdo->exec('PRAGMA foreign_keys = ON');
        DB::attach($pdo);

        $webtrees_schema = new WebtreesSchema();

        DB::migrate(
            target: $webtrees_schema->version3(),
            mode: new MigrationMode(strict_not_null: true),
            execute: true,
        );
        DB::query(table: 'gedcom')->insert(['gedcom_id' => 1, 'gedcom_name' => 'example']);
        foreach (['MC0DONALD', 'MC0', 'MC0MC0', 'MAC0'] as $number => $surname) {
            DB::query(table: 'name')->insert([
                'n_file' => 1, 'n_id' => 'I1', 'n_num' => $number, 'n_type' => 'NAME',
                'n_sort' => $surname, 'n_full' => $surname, 'n_surn' => $surname, 'n_list' => '',
            ]);
        }

        $webtrees_schema->migrateToVersion4();

        self::assertSame(['MCDONALD', 'MC', 'MCMC0', 'MAC0'], DB::query()
            ->from('name')
            ->orderBy(DB::ascending('n_num'))
            ->pluck('n_surn'));
    }

    public function testVersion8NormalizesThemeDirectories(): void
    {
        $pdo = new Sqlite('sqlite::memory:');
        $pdo->exec('PRAGMA foreign_keys = ON');
        DB::attach($pdo);

        $webtrees_schema = new WebtreesSchema();

        DB::migrate(
            target: $webtrees_schema->version7(),
            mode: new MigrationMode(strict_not_null: true),
            execute: true,
        );
        DB::query(table: 'gedcom')->insert(['gedcom_id' => 1, 'gedcom_name' => 'first']);
        DB::query(table: 'gedcom')->insert(['gedcom_id' => 2, 'gedcom_name' => 'second']);
        DB::query(table: 'user')->insert([
            'user_id' => 1, 'user_name' => 'example', 'real_name' => 'Example',
            'email' => 'example@example.com', 'password' => 'password',
        ]);
        DB::query(table: 'gedcom_setting')->insert(['gedcom_id' => 1, 'setting_name' => 'THEME_DIR', 'setting_value' => 'themes/Été/']);
        DB::query(table: 'gedcom_setting')->insert(['gedcom_id' => 2, 'setting_name' => 'THEME_DIR', 'setting_value' => 'custom/']);
        DB::query(table: 'user_setting')->insert(['user_id' => 1, 'setting_name' => 'THEME_DIR', 'setting_value' => 'themes/']);
        DB::query(table: 'user_setting')->insert(['user_id' => 1, 'setting_name' => 'OTHER', 'setting_value' => 'themes/keep/']);
        DB::query(table: 'user_gedcom_setting')->insert(['user_id' => 1, 'gedcom_id' => 1, 'setting_name' => 'THEME_DIR', 'setting_value' => 'THEMES/Mixed/']);

        $webtrees_schema->migrateToVersion8();

        self::assertSame([1 => 'Été', 2 => 'custom'], DB::query()
            ->from('gedcom_setting')
            ->whereEqual('setting_name', 'THEME_DIR')
            ->pluck('setting_value', 'gedcom_id'));
        self::assertSame('themes', DB::query()
            ->from('user_setting')
            ->whereEqual('setting_name', 'THEME_DIR')
            ->value('setting_value'));
        self::assertSame('themes/keep/', DB::query()
            ->from('user_setting')
            ->whereEqual('setting_name', 'OTHER')
            ->value('setting_value'));
        self::assertSame('THEMES/Mixed', DB::query()
            ->from('user_gedcom_setting')
            ->whereEqual('setting_name', 'THEME_DIR')
            ->value('setting_value'));
    }

    public function testMigratedSettingsReplaceExistingValues(): void
    {
        $pdo = new Sqlite('sqlite::memory:');
        $pdo->exec('PRAGMA foreign_keys = ON');
        DB::attach($pdo);

        $webtrees_schema = new WebtreesSchema();

        DB::migrate(
            target: $webtrees_schema->version20(),
            mode: new MigrationMode(strict_not_null: true),
            execute: true,
        );
        DB::query(table: 'gedcom')->insert(['gedcom_id' => 1, 'gedcom_name' => 'first']);
        DB::query(table: 'gedcom')->insert(['gedcom_id' => 2, 'gedcom_name' => 'second']);
        DB::query(table: 'gedcom_setting')->insert(['gedcom_id' => 1, 'setting_name' => 'MULTI_MEDIA', 'setting_value' => 'old']);
        DB::query(table: 'gedcom_setting')->insert(['gedcom_id' => 1, 'setting_name' => 'MEDIA_UPLOAD', 'setting_value' => 'new']);
        DB::query(table: 'gedcom_setting')->insert(['gedcom_id' => 1, 'setting_name' => 'WELCOME_TEXT_AUTH_MODE_en_GB', 'setting_value' => 'first old']);
        DB::query(table: 'gedcom_setting')->insert(['gedcom_id' => 2, 'setting_name' => 'WELCOME_TEXT_AUTH_MODE_en_GB', 'setting_value' => 'second old']);
        DB::query(table: 'gedcom_setting')->insert(['gedcom_id' => 1, 'setting_name' => 'WELCOME_TEXT_CUST_HEAD', 'setting_value' => 'old heading']);
        DB::query(table: 'site_setting')->insert(['setting_name' => 'WELCOME_TEXT_AUTH_MODE_en_GB', 'setting_value' => 'new']);
        DB::query(table: 'site_setting')->insert(['setting_name' => 'WELCOME_TEXT_AUTH_MODE_en-GB', 'setting_value' => 'renamed']);
        DB::query(table: 'site_setting')->insert(['setting_name' => 'WELCOME_TEXT_CUST_HEAD', 'setting_value' => 'new heading']);

        $webtrees_schema->migrateToVersion13();
        $webtrees_schema->migrateToVersion25();

        self::assertSame('old', DB::query()
            ->from('gedcom_setting')
            ->whereEqual('gedcom_id', 1)
            ->whereEqual('setting_name', 'MEDIA_UPLOAD')
            ->value('setting_value'));
        $welcome_text = DB::query()
            ->from('site_setting')
            ->whereEqual('setting_name', 'WELCOME_TEXT_AUTH_MODE_en_GB')
            ->value('setting_value');
        self::assertContains($welcome_text, ['first old', 'second old']);
        self::assertSame('old heading', DB::query()
            ->from('site_setting')
            ->whereEqual('setting_name', 'WELCOME_TEXT_CUST_HEAD')
            ->value('setting_value'));

        $webtrees_schema->migrateToVersion26();
        $webtrees_schema->migrateToVersion32();

        self::assertSame($welcome_text, DB::query()
            ->from('site_setting')
            ->whereEqual('setting_name', 'WELCOME_TEXT_AUTH_MODE_en-GB')
            ->value('setting_value'));
        self::assertSame(0, DB::query()
            ->from('site_setting')
            ->whereEqual('setting_name', 'WELCOME_TEXT_AUTH_MODE_en_GB')
            ->count());
        self::assertSame(0, DB::query()
            ->from('site_setting')
            ->whereEqual('setting_name', 'WELCOME_TEXT_CUST_HEAD')
            ->count());
    }

    public function testVersion31DefaultsReplaceExistingSettings(): void
    {
        $pdo = new Sqlite('sqlite::memory:');
        $pdo->exec('PRAGMA foreign_keys = ON');
        DB::attach($pdo);

        $webtrees_schema = new WebtreesSchema();

        DB::migrate(
            target: $webtrees_schema->version30(),
            mode: new MigrationMode(strict_not_null: true),
            execute: true,
        );
        DB::query(table: 'gedcom')->insert(['gedcom_id' => -1, 'gedcom_name' => 'default']);
        DB::query(table: 'gedcom')->insert(['gedcom_id' => 1, 'gedcom_name' => 'first']);
        DB::query(table: 'gedcom_setting')->insert(['gedcom_id' => -1, 'setting_name' => 'MEDIA_UPLOAD', 'setting_value' => '0']);
        DB::query(table: 'gedcom_setting')->insert(['gedcom_id' => 1, 'setting_name' => 'WEBTREES_EMAIL', 'setting_value' => 'old@example.com']);

        $server_name = $_SERVER['SERVER_NAME'] ?? null;
        try {
            $_SERVER['SERVER_NAME'] = 'example.com';
            $webtrees_schema->migrateToVersion31();
        } finally {
            if ($server_name === null) {
                unset($_SERVER['SERVER_NAME']);
            } else {
                $_SERVER['SERVER_NAME'] = $server_name;
            }
        }

        self::assertSame('1', DB::query()
            ->from('gedcom_setting')
            ->whereEqual('gedcom_id', -1)
            ->whereEqual('setting_name', 'MEDIA_UPLOAD')
            ->value('setting_value'));
        self::assertSame('webtrees-noreply@example.com', DB::query()
            ->from('gedcom_setting')
            ->whereEqual('gedcom_id', 1)
            ->whereEqual('setting_name', 'WEBTREES_EMAIL')
            ->value('setting_value'));
    }

    public function testHistoricalDataMigrationsPreserveNamesSettingsAndDates(): void
    {
        $pdo = new Sqlite('sqlite::memory:');
        $pdo->exec('PRAGMA foreign_keys = ON');
        DB::attach($pdo);

        $webtrees_schema = new WebtreesSchema();

        DB::migrate(
            target: $webtrees_schema->version20(),
            mode: new MigrationMode(strict_not_null: true),
            execute: true,
        );
        DB::query(table: 'gedcom')->insert(values: ['gedcom_name' => 'example']);
        DB::query(table: 'gedcom_setting')->insert(values: [
            'gedcom_id'     => 1,
            'setting_name'  => 'WELCOME_TEXT_AUTH_MODE_en_GB',
            'setting_value' => 'Welcome',
        ]);
        DB::query(table: 'name')->insert(values: [
            'n_file' => 1, 'n_id' => 'I1', 'n_num' => 1, 'n_type' => 'NAME',
            'n_sort' => 'AB', 'n_full' => 'A/B', 'n_surn' => 'B',
        ]);
        DB::query(table: 'dates')->insert(values: [
            'd_day' => 1, 'd_mon' => 6, 'd_year' => 5783, 'd_julianday1' => 1,
            'd_julianday2' => 1, 'd_fact' => 'BIRT', 'd_gid' => 'I1', 'd_file' => 1,
            'd_type' => '@#DHEBREW@',
        ]);

        $webtrees_schema->migrateToVersion12();
        $webtrees_schema->migrateToVersion25();
        $webtrees_schema->migrateToVersion29();

        self::assertSame('AB', $pdo->query("SELECT n_full FROM name WHERE n_id = 'I1'")->fetchColumn());
        self::assertSame('Welcome', $pdo->query("SELECT setting_value FROM site_setting WHERE setting_name = 'WELCOME_TEXT_AUTH_MODE_en_GB'")->fetchColumn());
        self::assertSame(0, (int) $pdo->query("SELECT COUNT(*) FROM gedcom_setting WHERE setting_name = 'WELCOME_TEXT_AUTH_MODE_en_GB'")->fetchColumn());
        self::assertSame(7, (int) $pdo->query('SELECT d_mon FROM dates')->fetchColumn());
    }

    public function testVersion38TruncatesMediaMetadata(): void
    {
        $pdo = new Sqlite('sqlite::memory:');
        $pdo->exec('PRAGMA foreign_keys = ON');
        DB::attach($pdo);

        $webtrees_schema = new WebtreesSchema();

        DB::migrate(
            target: $webtrees_schema->version37(),
            mode: new MigrationMode(strict_not_null: true),
            execute: true,
        );
        DB::query(table: 'media')->insert(values: [
            'm_id' => 'M1', 'm_file' => 1, 'm_filename' => 'é' . str_repeat('a', 248),
            'm_ext' => 'jpegxx', 'm_type' => 'abcdefghijklmnop',
            'm_titl' => str_repeat('t', 249), 'm_gedcom' => '',
        ]);

        $target = $webtrees_schema->version38();
        DB::migrate(target: $target, mode: new MigrationMode(strict_not_null: false, drop_orphan_columns: false), execute: true);
        $webtrees_schema->migrateToVersion38();
        DB::migrate(target: $target, mode: new MigrationMode(strict_not_null: true, drop_orphan_columns: true), execute: true);

        self::assertSame([
            'multimedia_file_refn' => 'é' . str_repeat('a', 247),
            'multimedia_format'    => 'jpeg',
            'source_media_type'    => 'abcdefghijklmno',
            'descriptive_title'    => str_repeat('t', 248),
        ], $pdo->query('SELECT multimedia_file_refn, multimedia_format, source_media_type, descriptive_title FROM media_file')->fetch(PDO::FETCH_ASSOC));
        self::assertFalse(DB::hasColumn(table: 'media', column: 'm_filename'));
    }

    public function testHistoricalAccessRuleConflictsKeepFirstRow(): void
    {
        $pdo = new Sqlite('sqlite::memory:');
        $pdo->exec('PRAGMA foreign_keys = ON');
        DB::attach($pdo);

        $webtrees_schema = new WebtreesSchema();

        $source = $webtrees_schema->version27()->alterTable(
            name: 'site_access_rule',
            callback: static fn (Table $table): Table => $table->dropIndex(name: 'site_access_rule_ux1'),
        );
        DB::migrate(target: $source, mode: new MigrationMode(strict_not_null: true), execute: true);
        DB::query(table: 'site_access_rule')->insertBatch(rows: [
            ['user_agent_pattern' => 'example', 'rule' => 'allow', 'comment' => 'First'],
            ['user_agent_pattern' => 'example', 'rule' => 'deny', 'comment' => 'Second'],
            ['user_agent_pattern' => 'example', 'rule' => 'allow', 'comment' => 'Third'],
            ['user_agent_pattern' => 'other', 'rule' => 'deny', 'comment' => 'Different'],
        ]);

        $webtrees_schema->migrateToVersion28();
        DB::migrate(target: $webtrees_schema->version28(), mode: new MigrationMode(strict_not_null: true), execute: true);

        self::assertSame([
            ['user_agent_pattern' => 'example', 'rule' => 'allow', 'comment' => 'First'],
            ['user_agent_pattern' => 'other', 'rule' => 'deny', 'comment' => 'Different'],
        ], $pdo->query('SELECT user_agent_pattern, rule, comment FROM site_access_rule ORDER BY site_access_rule_id')->fetchAll(PDO::FETCH_ASSOC));
    }

    public function testHistoricalUpgradeConvertsUnsupportedLegacyColumnTypes(): void
    {
        $pdo = new Sqlite('sqlite::memory:');
        $pdo->exec('PRAGMA foreign_keys = ON');
        DB::attach($pdo);

        $webtrees_schema = new WebtreesSchema();

        DB::migrate(
            target: $webtrees_schema->version20(),
            mode: new MigrationMode(strict_not_null: true),
            execute: true,
        );
        $pdo->exec('ALTER TABLE site_setting DROP COLUMN setting_value');
        $pdo->exec('ALTER TABLE site_setting ADD COLUMN setting_value BLOB');
        DB::query(table: 'site_setting')->insert(values: [
            'setting_name' => 'WT_SCHEMA_VERSION', 'setting_value' => '20',
        ]);
        DB::query(table: 'site_setting')->insert(values: [
            'setting_name' => 'CUSTOM_LEGACY_SETTING', 'setting_value' => 'retained',
        ]);

        $server_name = $_SERVER['SERVER_NAME'] ?? null;
        unset($_SERVER['SERVER_NAME']);

        try {
            try {
                $webtrees_schema->updateSchema();
                self::fail('The later email migration requires a configured server name');
            } catch (RuntimeException $exception) {
                self::assertStringContainsString('Version 31 requires SERVER_NAME', $exception->getMessage());
            }
        } finally {
            if ($server_name !== null) {
                $_SERVER['SERVER_NAME'] = $server_name;
            }
        }

        self::assertInstanceOf(VarcharType::class, DB::introspect()->getTable(name: 'site_setting')->getColumn(name: 'setting_value'));
        self::assertSame('retained', $pdo->query("SELECT setting_value FROM site_setting WHERE setting_name = 'CUSTOM_LEGACY_SETTING'")->fetchColumn());
        self::assertSame('30', $pdo->query("SELECT setting_value FROM site_setting WHERE setting_name = 'WT_SCHEMA_VERSION'")->fetchColumn());
    }

    public function testUpdatePreservesModulePrivacyData(): void
    {
        $pdo = new Sqlite('sqlite::memory:');
        $pdo->exec('PRAGMA foreign_keys = ON');
        DB::attach($pdo);

        $webtrees_schema = new WebtreesSchema();

        DB::migrate(
            target: $webtrees_schema->version42(),
            mode: new MigrationMode(strict_not_null: true),
            execute: true,
        );
        DB::query(table: 'gedcom')->insert(values: ['gedcom_name' => 'example']);
        DB::query(table: 'module')->insert(values: ['module_name' => 'example']);
        DB::query(table: 'module_privacy')->insertBatch(rows: [
            [
                'module_name'  => 'example',
                'gedcom_id'    => 1,
                'component'    => 'sidebar',
                'access_level' => 2,
            ],
            [
                'module_name'  => 'example',
                'gedcom_id'    => 1,
                'component'    => 'tab',
                'access_level' => 1,
            ],
        ]);
        DB::query(table: 'site_setting')->insert(values: [
            'setting_name'  => 'WT_SCHEMA_VERSION',
            'setting_value' => '42',
        ]);

        DB::migrate(
            target: $webtrees_schema->version43a(),
            mode: new MigrationMode(strict_not_null: false, drop_orphan_columns: false),
            execute: true,
        );
        self::assertSame(
            ['component' => 'sidebar', 'id' => null, 'interface' => null],
            $pdo->query("SELECT component, id, interface FROM module_privacy WHERE component = 'sidebar'")->fetch(PDO::FETCH_ASSOC),
        );

        $webtrees_schema->updateSchema();

        self::assertSame(
            [
                ['id' => 1, 'interface' => ModuleSidebarInterface::class, 'access_level' => 2],
                ['id' => 2, 'interface' => ModuleTabInterface::class, 'access_level' => 1],
            ],
            $pdo->query('SELECT id, interface, access_level FROM module_privacy ORDER BY id')->fetchAll(PDO::FETCH_ASSOC),
        );
        self::assertFalse(DB::introspect()->getTable(name: 'module_privacy')->hasColumn(name: 'component'));
        self::assertSame(1, (int) $pdo->query('PRAGMA foreign_keys')->fetchColumn());

        DB::query(table: 'site_setting')
            ->whereEqual(column: 'setting_name', value: 'WT_SCHEMA_VERSION')
            ->update(values: ['setting_value' => '42']);
        $webtrees_schema->updateSchema();

        self::assertSame(2, (int) $pdo->query('SELECT COUNT(*) FROM module_privacy')->fetchColumn());
    }

    public function testUpdateResumesAfterIntermediatePrimaryKeyChange(): void
    {
        $pdo = new Sqlite('sqlite::memory:');
        $pdo->exec('PRAGMA foreign_keys = ON');
        DB::attach($pdo);

        $webtrees_schema = new WebtreesSchema();

        DB::migrate(
            target: $webtrees_schema->version42(),
            mode: new MigrationMode(strict_not_null: true),
            execute: true,
        );
        DB::query(table: 'gedcom')->insert(values: ['gedcom_name' => 'example']);
        DB::query(table: 'module')->insert(values: ['module_name' => 'example']);
        DB::query(table: 'module_privacy')->insert(values: [
            'module_name'  => 'example',
            'gedcom_id'    => 1,
            'component'    => 'sidebar',
            'access_level' => 2,
        ]);
        DB::query(table: 'site_setting')->insert(values: [
            'setting_name'  => 'WT_SCHEMA_VERSION',
            'setting_value' => '42',
        ]);
        DB::migrate(
            target: $webtrees_schema->version43a(),
            mode: new MigrationMode(strict_not_null: false, drop_orphan_columns: false),
            execute: true,
        );
        DB::query(table: 'module_privacy')->update(values: [
            'id'        => 7,
            'interface' => ModuleSidebarInterface::class,
        ]);
        DB::migrate(
            target: $webtrees_schema->version43b(),
            mode: new MigrationMode(strict_not_null: true, drop_orphan_columns: false),
            execute: true,
        );

        $webtrees_schema->updateSchema();

        self::assertSame(
            ['id' => 7, 'interface' => ModuleSidebarInterface::class],
            $pdo->query('SELECT id, interface FROM module_privacy')->fetch(PDO::FETCH_ASSOC),
        );
        self::assertFalse(DB::introspect()->getTable(name: 'module_privacy')->hasColumn(name: 'component'));
    }

    public function testUpdatePreservesInvalidUserSettingsForRetry(): void
    {
        $pdo = new Sqlite('sqlite::memory:');
        $pdo->exec('PRAGMA foreign_keys = ON');
        DB::attach($pdo);

        $webtrees_schema = new WebtreesSchema();

        DB::migrate(
            target: $webtrees_schema->version45(),
            mode: new MigrationMode(strict_not_null: true),
            execute: true,
        );
        DB::query(table: 'gedcom')->insert(values: ['gedcom_name' => 'example']);
        DB::query(table: 'gedcom_setting')->insert(values: [
            'gedcom_id'     => 1,
            'setting_name'  => 'CONTACT_USER_ID',
            'setting_value' => '999',
        ]);
        DB::query(table: 'site_setting')->insert(values: [
            'setting_name'  => 'WT_SCHEMA_VERSION',
            'setting_value' => '45',
        ]);

        try {
            $webtrees_schema->updateSchema();
            self::fail('An invalid contact user must not be discarded');
        } catch (RuntimeException $exception) {
            self::assertSame(
                'Tree 1 references missing user 999 in CONTACT_USER_ID',
                $exception->getMessage(),
            );
        }

        self::assertSame(
            '999',
            $pdo->query("SELECT setting_value FROM gedcom_setting WHERE setting_name = 'CONTACT_USER_ID'")->fetchColumn(),
        );
        self::assertSame(
            '45',
            $pdo->query("SELECT setting_value FROM site_setting WHERE setting_name = 'WT_SCHEMA_VERSION'")->fetchColumn(),
        );

        DB::query(table: 'user')->insert(values: [
            'user_name' => 'example',
            'real_name' => 'Example User',
            'email'     => 'example@example.com',
            'password'  => 'test',
        ]);
        DB::query(table: 'gedcom_setting')
            ->whereEqual(column: 'setting_name', value: 'CONTACT_USER_ID')
            ->update(values: ['setting_value' => '1']);
        $webtrees_schema->updateSchema();

        self::assertSame(1, (int) $pdo->query('SELECT contact_user_id FROM gedcom')->fetchColumn());
        self::assertSame(0, (int) $pdo->query('SELECT COUNT(*) FROM gedcom_setting')->fetchColumn());
    }

    public function testUpdateCopiesMediaAndTreeSettings(): void
    {
        $pdo = new Sqlite('sqlite::memory:');
        $pdo->exec('PRAGMA foreign_keys = ON');
        DB::attach($pdo);

        $webtrees_schema = new WebtreesSchema();

        DB::migrate(
            target: $webtrees_schema->version37(),
            mode: new MigrationMode(strict_not_null: true),
            execute: true,
        );
        DB::query(table: 'gedcom')->insert(values: ['gedcom_name' => 'example']);
        DB::query(table: 'media')->insert(values: [
            'm_id'       => 'M1',
            'm_file'     => 1,
            'm_filename' => 'photo.jpg',
            'm_ext'      => 'jpg',
            'm_type'     => 'photo',
            'm_titl'     => 'Portrait',
            'm_gedcom'   => '',
        ]);
        DB::query(table: 'gedcom_setting')->insertBatch(rows: [
            ['gedcom_id' => 1, 'setting_name' => 'LANGUAGE', 'setting_value' => 'fr'],
            ['gedcom_id' => 1, 'setting_name' => 'title', 'setting_value' => 'My tree'],
            ['gedcom_id' => 1, 'setting_name' => 'MEDIA_DIRECTORY', 'setting_value' => 'assets/'],
        ]);
        DB::query(table: 'site_setting')->insert(values: [
            'setting_name'  => 'WT_SCHEMA_VERSION',
            'setting_value' => '37',
        ]);

        $webtrees_schema->updateSchema();

        self::assertSame(
            [
                'multimedia_file_refn' => 'photo.jpg',
                'multimedia_format'    => 'JPG',
                'source_media_type'    => 'PHOTO',
                'descriptive_title'    => 'Portrait',
            ],
            $pdo->query(
                'SELECT multimedia_file_refn, multimedia_format, source_media_type, descriptive_title FROM media_file'
            )->fetch(PDO::FETCH_ASSOC),
        );
        self::assertSame(
            ['title' => 'My tree', 'media_folder' => 'assets/'],
            $pdo->query('SELECT title, media_folder FROM gedcom WHERE gedcom_id = 1')->fetch(PDO::FETCH_ASSOC),
        );
        self::assertSame(
            'fr',
            $pdo->query("SELECT setting_value FROM site_setting WHERE setting_name = 'LANGUAGE'")->fetchColumn(),
        );
        self::assertSame(0, (int) $pdo->query('SELECT COUNT(*) FROM gedcom_setting')->fetchColumn());
        self::assertFalse(DB::introspect()->getTable(name: 'media')->hasColumn(name: 'm_filename'));
        self::assertFalse(DB::introspect()->hasTable(name: 'next_id'));
    }

    public function testConstraintNamesFollowTableAndType(): void
    {
        $pdo = new Sqlite('sqlite::memory:');
        $pdo->exec('PRAGMA foreign_keys = ON');
        DB::attach($pdo);

        $webtrees_schema = new WebtreesSchema();

        $versions = [
            $webtrees_schema->version1(),
            $webtrees_schema->version38(),
            $webtrees_schema->version40(),
            $webtrees_schema->version41(),
            $webtrees_schema->version43a(),
            $webtrees_schema->version43b(),
            $webtrees_schema->version43c(),
            $webtrees_schema->version45(),
            $webtrees_schema->version46(),
        ];

        foreach ($versions as $schema) {
            foreach ($schema->getTables() as $table) {
                $index_numbers = ['ix' => 0, 'ux' => 0];

                foreach ($table->getIndexes() as $index) {
                    $suffix = $index->isUnique() ? 'ux' : 'ix';
                    $index_numbers[$suffix]++;

                    self::assertSame($table->getName() . '_' . $suffix . $index_numbers[$suffix], $index->getName());
                    self::assertLessThanOrEqual(31, strlen($index->getName()));
                }

                foreach ($table->getForeignKeys() as $number => $foreign_key) {
                    self::assertSame($table->getName() . '_fk' . ($number + 1), $foreign_key->getName());
                    self::assertLessThanOrEqual(31, strlen($foreign_key->getName()));
                }
            }
        }
    }

    public function testVersions45And46DefinePlaceLocationsAndTreeSettings(): void
    {
        $pdo = new Sqlite('sqlite::memory:');
        $pdo->exec('PRAGMA foreign_keys = ON');
        DB::attach($pdo);

        $webtrees_schema = new WebtreesSchema();

        $place_location = $webtrees_schema->version45()->getTable(name: 'place_location');

        self::assertSame(['id'], $place_location->getPrimaryKey()?->getColumns());
        self::assertCount(5, $place_location->getColumns());
        self::assertCount(4, $place_location->getIndexes());
        self::assertSame([], $place_location->getForeignKeys());
        self::assertFalse($webtrees_schema->version45()->getTable(name: 'gedcom')->hasColumn(name: 'media_folder'));

        $gedcom = $webtrees_schema->version46()->getTable(name: 'gedcom');

        self::assertCount(10, $gedcom->getColumns());
        self::assertCount(9, $gedcom->getIndexes());
        self::assertSame('media/', $gedcom->getColumn(name: 'media_folder')->getDefault());
        self::assertSame('tree', $gedcom->getColumn(name: 'title')->getDefault());
        self::assertSame('tree.ged', $gedcom->getColumn(name: 'gedcom_filename')->getDefault());
        self::assertSame(1, $gedcom->getColumn(name: 'imported')->getDefault());
        self::assertSame(0, $gedcom->getColumn(name: 'private')->getDefault());
        self::assertTrue($gedcom->getColumn(name: 'contact_user_id')->isNull());
        self::assertTrue($gedcom->getColumn(name: 'support_user_id')->isNull());
        self::assertSame(Action::NoAction, $gedcom->getForeignKey(name: 'gedcom_fk1')->getOnDelete());
        self::assertSame(Action::NoAction, $gedcom->getForeignKey(name: 'gedcom_fk1')->getOnUpdate());
        self::assertSame(Action::NoAction, $gedcom->getForeignKey(name: 'gedcom_fk2')->getOnDelete());
        self::assertSame(Action::NoAction, $gedcom->getForeignKey(name: 'gedcom_fk2')->getOnUpdate());
    }

    public function testVersions39Through44FollowTheirMigrations(): void
    {
        $webtrees_schema = new WebtreesSchema();

        self::assertEquals($webtrees_schema->version38(), $webtrees_schema->version39());
        self::assertFalse($webtrees_schema->version39()->hasTable(name: 'favorite'));

        $favorite = $webtrees_schema->version40()->getTable(name: 'favorite');
        self::assertSame(['favorite_id'], $favorite->getPrimaryKey()?->getColumns());
        self::assertCount(8, $favorite->getColumns());
        self::assertCount(2, $favorite->getIndexes());
        self::assertSame(Action::Cascade, $favorite->getForeignKey(name: 'favorite_fk1')->getOnDelete());
        self::assertSame(Action::Cascade, $favorite->getForeignKey(name: 'favorite_fk2')->getOnDelete());

        $news = $webtrees_schema->version41()->getTable(name: 'news');
        self::assertSame(['news_id'], $news->getPrimaryKey()?->getColumns());
        self::assertCount(6, $news->getColumns());
        self::assertCount(2, $news->getIndexes());
        self::assertSame(Action::Cascade, $news->getForeignKey(name: 'news_fk1')->getOnDelete());
        self::assertSame(Action::Cascade, $news->getForeignKey(name: 'news_fk2')->getOnDelete());

        self::assertFalse($webtrees_schema->version41()->getTable(name: 'module')->hasColumn(name: 'footer_order'));
        self::assertFalse($webtrees_schema->version42()->getTable(name: 'module')->getColumn(name: 'footer_order')->isNotNull());

        $old_privacy = $webtrees_schema->version42()->getTable(name: 'module_privacy');
        $new_privacy = $webtrees_schema->version43c()->getTable(name: 'module_privacy');

        self::assertSame(['module_name', 'gedcom_id', 'component'], $old_privacy->getPrimaryKey()?->getColumns());
        $intermediate_privacy = $webtrees_schema->version43a()->getTable(name: 'module_privacy');
        self::assertSame(['module_name', 'gedcom_id', 'component'], $intermediate_privacy->getPrimaryKey()?->getColumns());
        self::assertTrue($intermediate_privacy->hasColumn(name: 'component'));
        self::assertFalse($intermediate_privacy->getColumn(name: 'id')->isNotNull());
        self::assertFalse($intermediate_privacy->getColumn(name: 'interface')->isNotNull());
        $keyed_privacy = $webtrees_schema->version43b()->getTable(name: 'module_privacy');
        self::assertSame(['id'], $keyed_privacy->getPrimaryKey()?->getColumns());
        self::assertTrue($keyed_privacy->hasColumn(name: 'component'));
        self::assertSame(['id'], $new_privacy->getPrimaryKey()?->getColumns());
        self::assertFalse($new_privacy->hasColumn(name: 'component'));
        $interface_column = $new_privacy->getColumn(name: 'interface');
        self::assertInstanceOf(VarcharType::class, $interface_column);
        self::assertSame(255, $interface_column->getLength());
        self::assertCount(2, $new_privacy->getIndexes());
        self::assertSame(
            ['gedcom_id', 'module_name', 'interface'],
            $new_privacy->getIndex(name: 'module_privacy_ux1')->getColumns(),
        );
        self::assertSame(
            ['module_name', 'gedcom_id', 'interface'],
            $new_privacy->getIndex(name: 'module_privacy_ux2')->getColumns(),
        );
        self::assertSame(Action::Cascade, $new_privacy->getForeignKey(name: 'module_privacy_fk1')->getOnDelete());
        self::assertSame(Action::Cascade, $new_privacy->getForeignKey(name: 'module_privacy_fk2')->getOnDelete());
        self::assertEquals($webtrees_schema->version43c(), $webtrees_schema->version44());
    }

    public function testVersion38SplitsMediaAndDropsNextId(): void
    {
        $webtrees_schema = new WebtreesSchema();
        $previous        = $webtrees_schema->version37();
        $schema          = $webtrees_schema->version38();

        self::assertTrue($previous->hasTable(name: 'next_id'));
        self::assertFalse($previous->hasTable(name: 'media_file'));
        self::assertTrue($previous->getTable(name: 'media')->hasColumn(name: 'm_filename'));

        self::assertFalse($schema->hasTable(name: 'next_id'));
        self::assertFalse($schema->hasTable(name: 'site_access_rule'));
        self::assertSame(
            ['m_id', 'm_file', 'm_gedcom'],
            array_map(static fn (ColumnInterface $column): string => $column->getName(), $schema->getTable(name: 'media')->getColumns()),
        );

        $media_file = $schema->getTable(name: 'media_file');

        self::assertSame(
            ['id', 'm_id', 'm_file', 'multimedia_file_refn', 'multimedia_format', 'source_media_type', 'descriptive_title'],
            array_map(static fn (ColumnInterface $column): string => $column->getName(), $media_file->getColumns()),
        );
        self::assertSame(['id'], $media_file->getPrimaryKey()?->getColumns());
        self::assertInstanceOf(IntegerType::class, $media_file->getColumn(name: 'id'));
        self::assertSame(
            [
                'media_file_ix1',
                'media_file_ix2',
                'media_file_ix3',
                'media_file_ix4',
                'media_file_ix5',
                'media_file_ix6',
            ],
            array_map(static fn (Index $index): string => $index->getName(), $media_file->getIndexes()),
        );
        self::assertSame([], $media_file->getForeignKeys());
    }

    public function testHistoricalSchemaTransitionsAreDistinct(): void
    {
        $webtrees_schema = new WebtreesSchema();

        self::assertFalse($webtrees_schema->version1()->hasTable(name: 'session'));
        self::assertTrue($webtrees_schema->version2()->hasTable(name: 'session'));
        self::assertFalse($webtrees_schema->version2()->hasTable(name: 'gedcom_chunk'));
        self::assertTrue($webtrees_schema->version3()->hasTable(name: 'gedcom_chunk'));
        self::assertTrue($webtrees_schema->version1()->hasTable(name: 'remotelinks'));
        self::assertFalse($webtrees_schema->version7()->hasTable(name: 'remotelinks'));
        self::assertTrue($webtrees_schema->version18()->hasTable(name: 'site_access_rule'));
        self::assertFalse($webtrees_schema->version38()->hasTable(name: 'site_access_rule'));
        self::assertTrue($webtrees_schema->version20()->hasTable(name: 'media_mapping'));
        self::assertFalse($webtrees_schema->version21()->hasTable(name: 'media_mapping'));
        self::assertTrue($webtrees_schema->version29()->hasTable(name: 'ip_address'));
        self::assertFalse($webtrees_schema->version30()->hasTable(name: 'ip_address'));
    }

    public function testVersion1PreservesExistingDatesWithoutPrimaryKey(): void
    {
        $pdo = new Sqlite('sqlite::memory:');
        $pdo->exec('PRAGMA foreign_keys = ON');
        $pdo->exec(
            'CREATE TABLE dates (
                d_day INTEGER NOT NULL,
                d_month VARCHAR(5),
                d_mon INTEGER NOT NULL,
                d_year INTEGER NOT NULL,
                d_julianday1 INTEGER NOT NULL,
                d_julianday2 INTEGER NOT NULL,
                d_fact VARCHAR(15) NOT NULL,
                d_gid VARCHAR(20) NOT NULL,
                d_file INTEGER NOT NULL,
                d_type VARCHAR(13) NOT NULL
            )',
        );
        $pdo->exec(
            "INSERT INTO dates (d_day, d_month, d_mon, d_year, d_julianday1, d_julianday2, d_fact, d_gid, d_file, d_type)
            VALUES (1, 'JAN', 1, 2000, 2451545, 2451545, 'BIRT', 'I1', 1, 'DATE'),
                   (2, 'FEB', 2, 2001, 2451943, 2451943, 'DEAT', 'I2', 1, 'DATE')",
        );
        DB::attach($pdo);

        $webtrees_schema = new WebtreesSchema();

        $dates  = $webtrees_schema->version1()->getTable(name: 'dates');
        $target = DB::schema(tables: [$dates]);

        self::assertNull($dates->getPrimaryKey());

        DB::migrate(
            target: $target,
            mode: new MigrationMode(strict_not_null: true, drop_orphan_columns: true),
            execute: true,
        );
        self::assertSame(
            [
                ['d_day' => 1, 'd_gid' => 'I1'],
                ['d_day' => 2, 'd_gid' => 'I2'],
            ],
            $pdo->query('SELECT d_day, d_gid FROM dates ORDER BY d_day')->fetchAll(PDO::FETCH_ASSOC),
        );

        $columns = $pdo->query('PRAGMA table_info(dates)')->fetchAll(PDO::FETCH_ASSOC);
        self::assertCount(10, $columns);
        foreach ($columns as $column) {
            self::assertSame(0, $column['pk']);
        }
        self::assertSame(
            '',
            (string) DB::migrate(
                target: $target,
                mode: new MigrationMode(strict_not_null: true, drop_orphan_columns: true),
                execute: true,
            ),
        );
        $pdo->exec("INSERT INTO dates SELECT * FROM dates WHERE d_gid = 'I1'");
        self::assertSame(2, (int) $pdo->query("SELECT COUNT(*) FROM dates WHERE d_gid = 'I1'")->fetchColumn());
        self::assertSame(1, (int) $pdo->query('PRAGMA foreign_keys')->fetchColumn());
    }

    public function testVersion1ContainsHistoricalTablesAndConstraints(): void
    {
        $webtrees_schema = new WebtreesSchema();

        $schema = $webtrees_schema->version1();

        $column_counts = [
            'block'               => 7,
            'block_setting'       => 3,
            'change'              => 8,
            'dates'               => 10,
            'default_resn'        => 5,
            'families'            => 6,
            'gedcom'              => 4,
            'gedcom_setting'      => 3,
            'hit_counter'         => 4,
            'individuals'         => 6,
            'ip_address'          => 3,
            'link'                => 4,
            'log'                 => 7,
            'media'               => 7,
            'media_mapping'       => 6,
            'message'             => 7,
            'module'              => 5,
            'module_privacy'      => 4,
            'module_setting'      => 3,
            'name'                => 14,
            'next_id'             => 3,
            'other'               => 4,
            'placelinks'          => 3,
            'places'              => 7,
            'remotelinks'         => 3,
            'site_setting'        => 2,
            'sources'             => 5,
            'user'                => 5,
            'user_gedcom_setting' => 4,
            'user_setting'        => 3,
        ];

        $table_names = [];

        foreach ($schema->getTables() as $table) {
            $table_names[] = $table->getName();
            self::assertCount($column_counts[$table->getName()], $table->getColumns());

            foreach ($table->getColumns() as $column) {
                self::assertNotEmpty($column->getName());
            }

            foreach ($table->getPrimaryKey()?->getColumns() ?? [] as $column) {
                self::assertTrue($table->hasColumn(name: $column));
            }

            foreach ($table->getIndexes() as $index) {
                foreach ($index->getColumns() as $column) {
                    self::assertTrue($table->hasColumn(name: $column));
                }
            }

            foreach ($table->getForeignKeys() as $foreign_key) {
                foreach ($foreign_key->getColumns() as $column) {
                    self::assertTrue($table->hasColumn(name: $column));
                }

                $foreign_table = $schema->getTable(name: $foreign_key->getForeignTable());

                foreach ($foreign_key->getForeignColumns() as $column) {
                    self::assertTrue($foreign_table->hasColumn(name: $column));
                }
            }
        }

        self::assertSame(array_keys($column_counts), $table_names);
        self::assertSame(['i_id', 'i_file'], $schema->getTable(name: 'individuals')->getPrimaryKey()?->getColumns());
        self::assertSame(['m_id'], $schema->getTable(name: 'media')->getPrimaryKey()?->getColumns());
        self::assertNull($schema->getTable(name: 'dates')->getPrimaryKey());
        self::assertTrue($schema->getTable(name: 'module_privacy')->getIndex(name: 'module_privacy_ux1')->isUnique());
        self::assertInstanceOf(VarcharType::class, $schema->getTable(name: 'individuals')->getColumn(name: 'i_sex'));
        self::assertInstanceOf(VarcharType::class, $schema->getTable(name: 'next_id')->getColumn(name: 'next_id'));
        self::assertNull($schema->getTable(name: 'log')->getColumn(name: 'log_time')->getDefault());
        self::assertInstanceOf(DatetimeType::class, $schema->getTable(name: 'log')->getColumn(name: 'log_time'));
    }
}
