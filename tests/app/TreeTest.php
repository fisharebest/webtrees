<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Functions\FunctionsImport;
use Fisharebest\Webtrees\Services\GedcomExportService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use InvalidArgumentException;

use function stream_get_contents;

/**
 * Test harness for the class Tree
 */
class TreeTest extends TestCase
{
    protected static $uses_database = true;

    /**
     * @covers \Fisharebest\Webtrees\Tree::__construct
     * @covers \Fisharebest\Webtrees\Tree::id
     * @covers \Fisharebest\Webtrees\Tree::name
     * @covers \Fisharebest\Webtrees\Tree::title
     * @return void
     */
    public function testConstructor(): void
    {
        $tree_service = new TreeService();
        $tree         = $tree_service->create('name', 'title');

        $this->assertSame('name', $tree->name());
        $this->assertSame('title', $tree->title());
    }

    /**
     * @covers \Fisharebest\Webtrees\Tree::getPreference
     * @covers \Fisharebest\Webtrees\Tree::setPreference
     * @return void
     */
    public function testTreePreferences(): void
    {
        $tree_service = new TreeService();
        $tree         = $tree_service->create('name', 'title');

        $pref = $tree->getPreference('foo', 'default');
        $this->assertSame('default', $pref);

        $tree->setPreference('foo', 'bar');
        $pref = $tree->getPreference('foo', 'default');
        $this->assertSame('bar', $pref);
    }

    /**
     * @covers \Fisharebest\Webtrees\Tree::getUserPreference
     * @covers \Fisharebest\Webtrees\Tree::setUserPreference
     * @return void
     */
    public function testUserTreePreferences(): void
    {
        $user_service = new UserService();
        $tree_service = new TreeService();
        $tree         = $tree_service->create('name', 'title');
        $user         = $user_service->create('user', 'User', 'user@example.com', 'secret');

        $pref = $tree->getUserPreference($user, 'foo', 'default');
        $this->assertSame('default', $pref);

        $tree->setUserPreference($user, 'foo', 'bar');
        $pref = $tree->getUserPreference($user, 'foo', 'default');
        $this->assertSame('bar', $pref);
    }

    /**
     * @covers \Fisharebest\Webtrees\Tree::getNewXref
     * @return void
     */
    public function testGetNewXref(): void
    {
        $tree_service = new TreeService();
        $tree         = $tree_service->create('tree-name', 'Tree title');

        // New trees have an individual X1.
        $this->assertSame('X2', $tree->getNewXref());
        $this->assertSame('X3', $tree->getNewXref());
        $this->assertSame('X4', $tree->getNewXref());
        $this->assertSame('X5', $tree->getNewXref());
    }

    /**
     * @covers \Fisharebest\Webtrees\Tree::createIndividual
     * @return void
     */
    public function testCreateInvalidIndividual(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $user_service = new UserService();
        $tree_service = new TreeService();
        $tree         = $tree_service->create('name', 'title');
        $user         = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(User::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $tree->createIndividual("0 @@ FOO\n1 SEX U");
    }

    /**
     * @covers \Fisharebest\Webtrees\Tree::createIndividual
     * @return void
     */
    public function testCreateIndividual(): void
    {
        $user_service = new UserService();
        $tree_service = new TreeService();
        $tree         = $tree_service->create('name', 'title');
        $user         = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(User::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $record = $tree->createIndividual("0 @@ INDI\n1 SEX F\n1 NAME Foo /Bar/");
        $this->assertTrue($record->isPendingAddition());

        $user->setPreference(User::PREF_AUTO_ACCEPT_EDITS, '1');
        $record = $tree->createIndividual("0 @@ INDI\n1 SEX F\n1 NAME Foo /Bar/");
        $this->assertFalse($record->isPendingAddition());
    }

    /**
     * @covers \Fisharebest\Webtrees\Tree::createFamily
     * @return void
     */
    public function testCreateInvalidFamily(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $user_service = new UserService();
        $tree_service = new TreeService();
        $tree         = $tree_service->create('name', 'title');
        $user         = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(User::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $tree->createFamily("0 @@ FOO\n1 MARR Y");
    }

    /**
     * @covers \Fisharebest\Webtrees\Tree::createFamily
     * @return void
     */
    public function testCreateFamily(): void
    {
        $user_service = new UserService();
        $tree_service = new TreeService();
        $tree         = $tree_service->create('name', 'title');
        $user         = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(User::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $record = $tree->createFamily("0 @@ FAM\n1 MARR Y");
        $this->assertTrue($record->isPendingAddition());

        $user->setPreference(User::PREF_AUTO_ACCEPT_EDITS, '1');
        $record = $tree->createFamily("0 @@ FAM\n1 MARR Y");
        $this->assertFalse($record->isPendingAddition());
    }

    /**
     * @covers \Fisharebest\Webtrees\Tree::createMediaObject
     * @return void
     */
    public function testCreateInvalidMediaObject(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $user_service = new UserService();
        $tree_service = new TreeService();
        $tree         = $tree_service->create('name', 'title');
        $user         = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(User::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $tree->createMediaObject("0 @@ FOO\n1 MARR Y");
    }

    /**
     * @covers \Fisharebest\Webtrees\Tree::createMediaObject
     * @return void
     */
    public function testCreateMediaObject(): void
    {
        $user_service = new UserService();
        $tree_service = new TreeService();
        $tree         = $tree_service->create('name', 'title');
        $user         = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(User::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $record = $tree->createMediaObject("0 @@ OBJE\n1 FILE foo.jpeg");
        $this->assertTrue($record->isPendingAddition());

        $user->setPreference(User::PREF_AUTO_ACCEPT_EDITS, '1');
        $record = $tree->createMediaObject("0 @@ OBJE\n1 FILE foo.jpeg");
        $this->assertFalse($record->isPendingAddition());
    }

    /**
     * @covers \Fisharebest\Webtrees\Tree::createRecord
     * @return void
     */
    public function testCreateInvalidRecord(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $user_service = new UserService();
        $tree_service = new TreeService();
        $tree         = $tree_service->create('name', 'title');
        $user         = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(User::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $tree->createRecord("0 @@FOO\n1 NOTE noted");
    }

    /**
     * @covers \Fisharebest\Webtrees\Tree::createRecord
     * @return void
     */
    public function testCreateRecord(): void
    {
        $user_service = new UserService();
        $tree_service = new TreeService();
        $tree         = $tree_service->create('name', 'title');
        $user         = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(User::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $record = $tree->createRecord("0 @@ FOO\n1 NOTE noted");
        $this->assertTrue($record->isPendingAddition());

        $user->setPreference(User::PREF_AUTO_ACCEPT_EDITS, '1');
        $record = $tree->createRecord("0 @@ FOO\n1 NOTE noted");
        $this->assertFalse($record->isPendingAddition());
    }

    /**
     * @covers \Fisharebest\Webtrees\Tree::significantIndividual
     * @return void
     */
    public function testSignificantIndividual(): void
    {
        $user_service = new UserService();
        $tree_service = new TreeService();
        $tree         = $tree_service->create('name', 'title');
        $user         = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference(User::PREF_AUTO_ACCEPT_EDITS, '1');
        Auth::login($user);

        // Delete the tree's default individual.
        FunctionsImport::updateRecord('0 @X1@ INDI', $tree, true);

        // No individuals in tree?  Fake individual
        $this->assertSame('I', $tree->significantIndividual($user)->xref());

        $record1 = $tree->createIndividual("0 @@ INDI\n1 SEX F\n1 NAME Foo /Bar/");
        $record2 = $tree->createIndividual("0 @@ INDI\n1 SEX F\n1 NAME Foo /Bar/");
        $record3 = $tree->createIndividual("0 @@ INDI\n1 SEX F\n1 NAME Foo /Bar/");
        $record4 = $tree->createIndividual("0 @@ INDI\n1 SEX F\n1 NAME Foo /Bar/");

        // Individuals exist?  First one (lowest XREF).
        $this->assertSame($record1->xref(), $tree->significantIndividual($user)->xref());

        // Preference for tree?
        $tree->setPreference('PEDIGREE_ROOT_ID', $record2->xref());
        $this->assertSame($record2->xref(), $tree->significantIndividual($user)->xref());

        // User preference
        $tree->setUserPreference($user, User::PREF_TREE_ACCOUNT_XREF, $record3->xref());
        $this->assertSame($record3->xref(), $tree->significantIndividual($user)->xref());

        // User record
        $tree->setUserPreference($user, User::PREF_TREE_DEFAULT_XREF, $record4->xref());
        $this->assertSame($record4->xref(), $tree->significantIndividual($user)->xref());
    }

    /**
     * @covers \Fisharebest\Webtrees\Tree::importGedcomFile
     * @covers \Fisharebest\Webtrees\Tree::deleteGenealogyData
     * @return void
     */
    public function testImportAndDeleteGedcomFile(): void
    {
        $tree_service = new TreeService();
        $tree = $this->importTree('demo.ged');
        $this->assertNotNull($tree_service->all()->get('demo.ged'));
        Site::setPreference('DEFAULT_GEDCOM', $tree->name());

        $tree_service->delete($tree);

        $this->assertNull($tree_service->all()->get('demo.ged'));
        $this->assertSame('', Site::getPreference('DEFAULT_GEDCOM'));
    }

    /**
     * @covers \Fisharebest\Webtrees\Tree::hasPendingEdit
     * @return void
     */
    public function testHasPendingEdits(): void
    {
        $user_service = new UserService();
        $tree         = $this->importTree('demo.ged');
        $user         = $user_service->create('admin', 'Administrator', 'admin@example.com', 'secret');
        $user->setPreference(User::PREF_IS_ADMINISTRATOR, '1');
        Auth::login($user);

        $user->setPreference(User::PREF_AUTO_ACCEPT_EDITS, '1');
        $tree->createIndividual("0 @@ INDI\n1 SEX F\n1 NAME Foo /Bar/");
        $this->assertFalse($tree->hasPendingEdit());

        $user->setPreference(User::PREF_AUTO_ACCEPT_EDITS, '');
        $tree->createIndividual("0 @@ INDI\n1 SEX F\n1 NAME Foo /Bar/");
        $this->assertTrue($tree->hasPendingEdit());
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\GedcomExportService::export
     * @return void
     */
    public function testExportGedcom(): void
    {
        $tree = $this->importTree('demo.ged');

        $fp = fopen('php://memory', 'wb');

        $gedcom_export_service = new GedcomExportService();
        $gedcom_export_service->export($tree, $fp, true);

        rewind($fp);

        $original = file_get_contents(__DIR__ . '/../data/demo.ged');
        $export   = stream_get_contents($fp);

        // The version, date and time in the HEAD record will be different.
        $original = preg_replace('/\n2 VERS .*/', '', $original, 1);
        $export   = preg_replace('/\n2 VERS .*/', '', $export, 1);
        $original = preg_replace('/\n1 DATE .. ... ..../', '', $original, 1);
        $export   = preg_replace('/\n1 DATE .. ... ..../', '', $export, 1);
        $original = preg_replace('/\n2 TIME ..:..:../', '', $original, 1);
        $export   = preg_replace('/\n2 TIME ..:..:../', '', $export, 1);

        $this->assertSame($original, $export);
    }
}
