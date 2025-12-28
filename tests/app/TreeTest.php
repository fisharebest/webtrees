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

namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Contracts\CacheFactoryInterface;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Services\GedcomExportService;
use Fisharebest\Webtrees\Services\GedcomImportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use InvalidArgumentException;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Cache\Adapter\NullAdapter;

use function fclose;
use function file_get_contents;
use function preg_replace;
use function stream_get_contents;

#[CoversClass(Tree::class)]
#[CoversClass(TreeService::class)]
#[CoversClass(GedcomExportService::class)]
class TreeTest extends TestCase
{
    protected static bool $uses_database = true;

    protected function setUp(): void
    {
        parent::setUp();

        $cache_factory = self::createStub(CacheFactoryInterface::class);
        $cache_factory->method('array')->willReturn(new Cache(new NullAdapter()));
        Registry::cache($cache_factory);
    }

    public function testConstructor(): void
    {
        $gedcom_import_service = new GedcomImportService();
        $tree_service          = new TreeService($gedcom_import_service);
        $tree                  = $tree_service->create('name', 'title');

        self::assertSame('name', $tree->name());
        self::assertSame('title', $tree->title());
    }

    public function testTreePreferences(): void
    {
        $gedcom_import_service = new GedcomImportService();
        $tree_service          = new TreeService($gedcom_import_service);
        $tree                  = $tree_service->create('name', 'title');

        $tree->setPreference('foo', 'bar');
        $pref = $tree->getPreference('foo');
        self::assertSame('bar', $pref);
    }

    public function testUserTreePreferences(): void
    {
        $user_service          = new UserService();
        $gedcom_import_service = new GedcomImportService();
        $tree_service          = new TreeService($gedcom_import_service);
        $tree                  = $tree_service->create('name', 'title');
        $user                  = $user_service->create('user', 'User', 'user@example.com', 'secret');

        $pref = $tree->getUserPreference($user, 'foo', 'default');
        self::assertSame('default', $pref);

        $tree->setUserPreference($user, 'foo', 'bar');
        $pref = $tree->getUserPreference($user, 'foo', 'default');
        self::assertSame('bar', $pref);
    }

    public function testCreateInvalidIndividual(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $user_service          = new UserService();
        $gedcom_import_service = new GedcomImportService();
        $tree_service          = new TreeService($gedcom_import_service);
        $tree                  = $tree_service->create('name', 'title');
        $user                  = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $tree->createIndividual("0 @@ FOO\n1 SEX U");
    }

    public function testCreateIndividual(): void
    {
        $user_service          = new UserService();
        $gedcom_import_service = new GedcomImportService();
        $tree_service          = new TreeService($gedcom_import_service);
        $tree                  = $tree_service->create('name', 'title');
        $user                  = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $record = $tree->createIndividual("0 @@ INDI\n1 SEX F\n1 NAME Foo /Bar/");
        self::assertTrue($record->isPendingAddition());

        $user->setPreference(UserInterface::PREF_AUTO_ACCEPT_EDITS, '1');
        $record = $tree->createIndividual("0 @@ INDI\n1 SEX F\n1 NAME Foo /Bar/");
        self::assertFalse($record->isPendingAddition());
    }

    public function testCreateInvalidFamily(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $user_service          = new UserService();
        $gedcom_import_service = new GedcomImportService();
        $tree_service          = new TreeService($gedcom_import_service);
        $tree                  = $tree_service->create('name', 'title');
        $user                  = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $tree->createFamily("0 @@ FOO\n1 MARR Y");
    }

    public function testCreateFamily(): void
    {
        $user_service          = new UserService();
        $gedcom_import_service = new GedcomImportService();
        $tree_service          = new TreeService($gedcom_import_service);
        $tree                  = $tree_service->create('name', 'title');
        $user                  = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $record = $tree->createFamily("0 @@ FAM\n1 MARR Y");
        self::assertTrue($record->isPendingAddition());

        $user->setPreference(UserInterface::PREF_AUTO_ACCEPT_EDITS, '1');
        $record = $tree->createFamily("0 @@ FAM\n1 MARR Y");
        self::assertFalse($record->isPendingAddition());
    }

    public function testCreateInvalidMediaObject(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $user_service          = new UserService();
        $gedcom_import_service = new GedcomImportService();
        $tree_service          = new TreeService($gedcom_import_service);
        $tree                  = $tree_service->create('name', 'title');
        $user                  = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $tree->createMediaObject("0 @@ FOO\n1 MARR Y");
    }

    public function testCreateMediaObject(): void
    {
        $user_service          = new UserService();
        $gedcom_import_service = new GedcomImportService();
        $tree_service          = new TreeService($gedcom_import_service);
        $tree                  = $tree_service->create('name', 'title');
        $user                  = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $record = $tree->createMediaObject("0 @@ OBJE\n1 FILE foo.jpeg");
        self::assertTrue($record->isPendingAddition());

        $user->setPreference(UserInterface::PREF_AUTO_ACCEPT_EDITS, '1');
        $record = $tree->createMediaObject("0 @@ OBJE\n1 FILE foo.jpeg");
        self::assertFalse($record->isPendingAddition());
    }

    public function testCreateInvalidRecord(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $user_service          = new UserService();
        $gedcom_import_service = new GedcomImportService();
        $tree_service          = new TreeService($gedcom_import_service);
        $tree                  = $tree_service->create('name', 'title');
        $user                  = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $tree->createRecord("0 @@FOO\n1 NOTE noted");
    }

    public function testCreateRecord(): void
    {
        $user_service          = new UserService();
        $gedcom_import_service = new GedcomImportService();
        $tree_service          = new TreeService($gedcom_import_service);
        $tree                  = $tree_service->create('name', 'title');
        $user                  = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $record = $tree->createRecord("0 @@ FOO\n1 NOTE noted");
        self::assertTrue($record->isPendingAddition());

        $user->setPreference(UserInterface::PREF_AUTO_ACCEPT_EDITS, '1');
        $record = $tree->createRecord("0 @@ FOO\n1 NOTE noted");
        self::assertFalse($record->isPendingAddition());
    }

    public function testSignificantIndividual(): void
    {
        $gedcom_import_service = new GedcomImportService();
        $user_service = new UserService();
        $tree_service = new TreeService($gedcom_import_service);
        $tree         = $tree_service->create('name', 'title');
        $user         = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_AUTO_ACCEPT_EDITS, '1');
        Auth::login($user);

        // Delete the tree's default individual.
        $gedcom_import_service->updateRecord('0 @X1@ INDI', $tree, true);

        // No individuals in tree?  Fake individual
        self::assertSame('I', $tree->significantIndividual($user)->xref());

        $record1 = $tree->createIndividual("0 @@ INDI\n1 SEX F\n1 NAME Foo /Bar/");
        $record2 = $tree->createIndividual("0 @@ INDI\n1 SEX F\n1 NAME Foo /Bar/");
        $record3 = $tree->createIndividual("0 @@ INDI\n1 SEX F\n1 NAME Foo /Bar/");
        $record4 = $tree->createIndividual("0 @@ INDI\n1 SEX F\n1 NAME Foo /Bar/");

        // Individuals exist?  First one (lowest XREF).
        self::assertSame($record1->xref(), $tree->significantIndividual($user)->xref());

        // Preference for tree?
        $tree->setPreference('PEDIGREE_ROOT_ID', $record2->xref());
        self::assertSame($record2->xref(), $tree->significantIndividual($user)->xref());

        // User preference
        $tree->setUserPreference($user, UserInterface::PREF_TREE_ACCOUNT_XREF, $record3->xref());
        self::assertSame($record3->xref(), $tree->significantIndividual($user)->xref());

        // User record
        $tree->setUserPreference($user, UserInterface::PREF_TREE_DEFAULT_XREF, $record4->xref());
        self::assertSame($record4->xref(), $tree->significantIndividual($user)->xref());
    }

    public function testImportAndDeleteGedcomFile(): void
    {
        $gedcom_import_service = new GedcomImportService();
        $tree_service          = new TreeService($gedcom_import_service);
        $tree                  = $this->importTree('demo.ged');
        self::assertNotNull($tree_service->all()->get('demo.ged'));
        Site::setPreference('DEFAULT_GEDCOM', $tree->name());

        $tree_service->delete($tree);

        self::assertNull($tree_service->all()->get('demo.ged'));
        self::assertSame('', Site::getPreference('DEFAULT_GEDCOM'));
    }

    public function testHasPendingEdits(): void
    {
        $user_service = new UserService();
        $tree         = $this->importTree('demo.ged');
        $user         = $user_service->create('admin', 'Administrator', 'admin@example.com', 'secret');
        $user->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $user->setPreference(UserInterface::PREF_AUTO_ACCEPT_EDITS, '1');
        $tree->createIndividual("0 @@ INDI\n1 SEX F\n1 NAME Foo /Bar/");
        self::assertFalse($tree->hasPendingEdit());

        $user->setPreference(UserInterface::PREF_AUTO_ACCEPT_EDITS, '');
        $tree->createIndividual("0 @@ INDI\n1 SEX F\n1 NAME Foo /Bar/");
        self::assertTrue($tree->hasPendingEdit());
    }

    public function testExportGedcom(): void
    {
        $tree = $this->importTree('demo.ged');

        $gedcom_export_service = new GedcomExportService(new Psr17Factory(), new Psr17Factory());

        $resource = $gedcom_export_service->export($tree, true);
        $original = file_get_contents(__DIR__ . '/../data/demo.ged');
        $export   = stream_get_contents($resource);
        self::assertIsString($original);
        self::assertIsString($export);
        fclose($resource);

        // The version, date and time in the HEAD record will be different.
        $original = preg_replace('/\n2 VERS .*/', '', $original, 1);
        $export   = preg_replace('/\n2 VERS .*/', '', $export, 1);
        self::assertIsString($original);
        self::assertIsString($export);
        $original = preg_replace('/\n1 DATE .. ... ..../', '', $original, 1);
        $export   = preg_replace('/\n1 DATE .. ... ..../', '', $export, 1);
        self::assertIsString($original);
        self::assertIsString($export);
        $original = preg_replace('/\n2 TIME ..:..:../', '', $original, 1);
        $export   = preg_replace('/\n2 TIME ..:..:../', '', $export, 1);
        self::assertIsString($original);
        self::assertIsString($export);

        self::assertSame($original, $export);
    }
}
