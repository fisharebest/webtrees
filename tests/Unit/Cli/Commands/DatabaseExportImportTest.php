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

namespace Fisharebest\Webtrees\Tests\Unit\Cli\Commands;

use Fisharebest\Webtrees\Cli\Commands\DatabaseExport;
use Fisharebest\Webtrees\Cli\Commands\DatabaseImport;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tests\TestCase;
use Fisharebest\Webtrees\Webtrees;
use Pdo\Sqlite;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Console\Tester\CommandTester;

use function is_file;
use function preg_match;
use function sys_get_temp_dir;
use function uniqid;
use function unlink;

#[CoversClass(DatabaseExport::class)]
#[CoversClass(DatabaseImport::class)]
class DatabaseExportImportTest extends TestCase
{
    /** @var array<string> */
    private array $export_paths = [];

    private function exportDatabase(): string
    {
        $export = new CommandTester(Registry::container()->get(DatabaseExport::class));
        self::assertSame(0, $export->execute([]), $export->getDisplay());
        self::assertMatchesRegularExpression('/data\/(export-[0-9]{8}-[0-9]{6}\.sqlite)/', $export->getDisplay());
        self::assertStringContainsString(' remaining', $export->getDisplay());
        self::assertStringNotContainsString(' elapsed', $export->getDisplay());
        self::assertStringNotContainsString(' MiB', $export->getDisplay());
        preg_match('/data\/(export-[0-9]{8}-[0-9]{6}\.sqlite)/', $export->getDisplay(), $matches);

        $this->export_paths[] = Webtrees::DATA_DIR . $matches[1];

        return $matches[1];
    }

    protected function tearDown(): void
    {
        foreach ($this->export_paths as $path) {
            unlink($path);
        }

        parent::tearDown();
    }

    public function testExportAndImportPreserveRowsAndForeignKeys(): void
    {
        self::createDatabase();

        DB::query(table: 'user')->insert([
            'user_id'   => 42,
            'user_name' => 'backup-test',
            'real_name' => 'Backup Test',
            'email'     => 'backup@test.invalid',
            'password'  => 'password',
        ]);
        DB::query(table: 'gedcom')->insert(['gedcom_id' => 42, 'gedcom_name' => 'backup-test', 'contact_user_id' => 42]);
        DB::query(table: 'user_gedcom_setting')->insert([
            'user_id'       => 42,
            'gedcom_id'     => 42,
            'setting_name'  => 'backup-test',
            'setting_value' => 'original',
        ]);

        for ($number = 0; $number < 151; $number++) {
            DB::query(table: 'name')->insert([
                'n_file' => 42,
                'n_id'   => 'I' . $number,
                'n_num'  => 0,
                'n_type' => 'NAME',
                'n_sort' => 'Backup Test',
                'n_full' => 'Backup Test',
            ]);
        }

        $filename = $this->exportDatabase();

        $sqlite = new Sqlite('sqlite:' . $this->export_paths[0]);
        self::assertNotSame(0, $sqlite->query('PRAGMA application_id')->fetchColumn());
        self::assertSame(42, $sqlite->query("SELECT contact_user_id FROM gedcom WHERE gedcom_id = 42")->fetchColumn());
        self::assertSame('original', $sqlite->query("SELECT setting_value FROM user_gedcom_setting WHERE user_id = 42")->fetchColumn());
        self::assertSame(151, $sqlite->query('SELECT COUNT(*) FROM name WHERE n_file = 42')->fetchColumn());
        self::assertSame('ok', $sqlite->query('PRAGMA integrity_check')->fetchColumn());
        $sqlite = null;

        DB::query(table: 'user_gedcom_setting')->whereEqual('user_id', 42)->delete();
        DB::query(table: 'name')->whereEqual('n_file', 42)->delete();
        DB::query(table: 'gedcom')->whereEqual('gedcom_id', 42)->delete();
        DB::query(table: 'user')->whereEqual('user_id', 42)->delete();

        $import = new CommandTester(Registry::container()->get(DatabaseImport::class));
        self::assertSame(0, $import->execute(['filename' => $filename]), $import->getDisplay());
        self::assertSame(42, DB::query(table: 'gedcom')->whereEqual('gedcom_id', 42)->value('contact_user_id'));
        self::assertSame('original', DB::query(table: 'user_gedcom_setting')->whereEqual('user_id', 42)->value('setting_value'));
        self::assertSame(151, DB::query(table: 'name')->whereEqual('n_file', 42)->count());
        self::assertSame(1, DB::query(table: 'user')->whereEqual('user_id', 42)->count());
        self::assertFalse(DB::connection()->pdo()->query('PRAGMA foreign_key_check')->fetch());
    }

    public function testExportUsesDataDirectoryForFilename(): void
    {
        self::createDatabase();

        $filename = 'test-' . uniqid() . '.sqlite';
        $path = Webtrees::DATA_DIR . $filename;
        $export = new CommandTester(Registry::container()->get(DatabaseExport::class));
        self::assertSame(0, $export->execute(['filename' => $filename]), $export->getDisplay());
        $this->export_paths[] = $path;
        self::assertStringContainsString($path, $export->getDisplay());
        self::assertSame('ok', (new Sqlite('sqlite:' . $path))->query('PRAGMA integrity_check')->fetchColumn());
    }

    public function testExportAndImportUseExplicitPath(): void
    {
        self::createDatabase();

        $path = sys_get_temp_dir() . '/webtrees-export-' . uniqid() . '.sqlite';
        $export = new CommandTester(Registry::container()->get(DatabaseExport::class));
        self::assertSame(0, $export->execute(['filename' => $path]), $export->getDisplay());
        $this->export_paths[] = $path;
        self::assertTrue(is_file($path));

        $import = new CommandTester(Registry::container()->get(DatabaseImport::class));
        self::assertSame(0, $import->execute(['filename' => $path]), $import->getDisplay());
        self::assertStringContainsString('Database imported from', $import->getDisplay());
    }

    public function testExportRejectsNonSqliteFilename(): void
    {
        self::createDatabase();

        $export = new CommandTester(Registry::container()->get(DatabaseExport::class));
        self::assertSame(1, $export->execute(['filename' => 'test.db']));
        self::assertStringContainsString('must end in .sqlite', $export->getDisplay());
    }

    public function testExportRejectsExistingFile(): void
    {
        self::createDatabase();

        $filename = $this->exportDatabase();
        $export = new CommandTester(Registry::container()->get(DatabaseExport::class));
        self::assertSame(1, $export->execute(['filename' => $filename]));
        self::assertStringContainsString('already exists', $export->getDisplay());
    }

    public function testImportRejectsInvalidFilenameWithoutChangingDatabase(): void
    {
        self::createDatabase();

        $import = new CommandTester(Registry::container()->get(DatabaseImport::class));
        self::assertSame(1, $import->execute(['filename' => '../config.ini.php']));
        self::assertSame(1, DB::query(table: 'user')->whereEqual('user_id', -1)->count());
    }

    public function testImportRejectsInvalidBackupBeforeDroppingTables(): void
    {
        self::createDatabase();

        $filename = $this->exportDatabase();
        $sqlite   = new Sqlite('sqlite:' . $this->export_paths[0]);
        $sqlite->exec('DROP TABLE name');

        $import = new CommandTester(Registry::container()->get(DatabaseImport::class));
        self::assertSame(1, $import->execute(['filename' => $filename]));
        self::assertStringContainsString('Missing database table: name', $import->getDisplay());
        self::assertSame(1, DB::query(table: 'user')->whereEqual('user_id', -1)->count());
    }

    public function testImportRejectsBackupWithoutCompletionMarker(): void
    {
        self::createDatabase();

        $filename = $this->exportDatabase();
        $sqlite   = new Sqlite('sqlite:' . $this->export_paths[0]);
        $sqlite->exec('PRAGMA application_id = 0');

        $import = new CommandTester(Registry::container()->get(DatabaseImport::class));
        self::assertSame(1, $import->execute(['filename' => $filename]));
        self::assertStringContainsString('Backup database is incomplete', $import->getDisplay());
        self::assertSame(1, DB::query(table: 'user')->whereEqual('user_id', -1)->count());
    }
}
