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

use Fisharebest\Webtrees\Cli\Commands\BackupDatabase;
use Fisharebest\Webtrees\Cli\Commands\RestoreDatabase;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tests\TestCase;
use Fisharebest\Webtrees\Webtrees;
use Pdo\Sqlite;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Console\Tester\CommandTester;

use function preg_match;
use function unlink;

#[CoversClass(BackupDatabase::class)]
#[CoversClass(RestoreDatabase::class)]
class DatabaseCommandsTest extends TestCase
{
    private string|null $backup_path = null;

    private function backupDatabase(): string
    {
        $backup = new CommandTester(Registry::container()->get(BackupDatabase::class));
        self::assertSame(0, $backup->execute([]));
        self::assertMatchesRegularExpression('/data\/(backup-[0-9]{8}-[0-9]{6}\.sqlite)/', $backup->getDisplay());
        preg_match('/data\/(backup-[0-9]{8}-[0-9]{6}\.sqlite)/', $backup->getDisplay(), $matches);

        $this->backup_path = Webtrees::DATA_DIR . $matches[1];

        return $matches[1];
    }

    protected function tearDown(): void
    {
        if ($this->backup_path !== null) {
            unlink($this->backup_path);
        }

        parent::tearDown();
    }

    public function testBackupAndRestorePreserveRowsAndForeignKeys(): void
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

        $filename = $this->backupDatabase();

        $sqlite = new Sqlite('sqlite:' . $this->backup_path);
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

        $restore = new CommandTester(Registry::container()->get(RestoreDatabase::class));
        self::assertSame(0, $restore->execute(['filename' => $filename]), $restore->getDisplay());
        self::assertSame(42, DB::query(table: 'gedcom')->whereEqual('gedcom_id', 42)->value('contact_user_id'));
        self::assertSame('original', DB::query(table: 'user_gedcom_setting')->whereEqual('user_id', 42)->value('setting_value'));
        self::assertSame(151, DB::query(table: 'name')->whereEqual('n_file', 42)->count());
        self::assertSame(1, DB::query(table: 'user')->whereEqual('user_id', 42)->count());
        self::assertFalse(DB::connection()->pdo()->query('PRAGMA foreign_key_check')->fetch());
    }

    public function testRestoreRejectsInvalidFilenameWithoutChangingDatabase(): void
    {
        self::createDatabase();

        $restore = new CommandTester(Registry::container()->get(RestoreDatabase::class));
        self::assertSame(1, $restore->execute(['filename' => '../config.ini.php']));
        self::assertSame(1, DB::query(table: 'user')->whereEqual('user_id', -1)->count());
    }

    public function testRestoreRejectsInvalidBackupBeforeDroppingTables(): void
    {
        self::createDatabase();

        $filename = $this->backupDatabase();
        $sqlite   = new Sqlite('sqlite:' . $this->backup_path);
        $sqlite->exec('DROP TABLE name');

        $restore = new CommandTester(Registry::container()->get(RestoreDatabase::class));
        self::assertSame(1, $restore->execute(['filename' => $filename]));
        self::assertStringContainsString('Missing database table: name', $restore->getDisplay());
        self::assertSame(1, DB::query(table: 'user')->whereEqual('user_id', -1)->count());
    }

    public function testRestoreRejectsBackupWithoutCompletionMarker(): void
    {
        self::createDatabase();

        $filename = $this->backupDatabase();
        $sqlite   = new Sqlite('sqlite:' . $this->backup_path);
        $sqlite->exec('PRAGMA application_id = 0');

        $restore = new CommandTester(Registry::container()->get(RestoreDatabase::class));
        self::assertSame(1, $restore->execute(['filename' => $filename]));
        self::assertStringContainsString('Backup database is incomplete', $restore->getDisplay());
        self::assertSame(1, DB::query(table: 'user')->whereEqual('user_id', -1)->count());
    }
}
