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

use Fisharebest\Webtrees\Services\UserService;

use function stream_get_contents;

/**
 * Test harness for the class Tree
 */
class TreeTest extends TestCase
{
    protected static $uses_database = true;

    /**
     * @covers \Fisharebest\Webtrees\Tree::__construct
     * @covers \Fisharebest\Webtrees\Tree::create
     * @covers \Fisharebest\Webtrees\Tree::id
     * @covers \Fisharebest\Webtrees\Tree::name
     * @covers \Fisharebest\Webtrees\Tree::title
     *
     * @return void
     */
    public function testConstructor(): void
    {
        $tree = Tree::create('tree-name', 'Tree title');

        $this->assertSame('tree-name', $tree->name());
        $this->assertSame('Tree title', $tree->title());
    }

    /**
     * @covers \Fisharebest\Webtrees\Tree::getPreference
     * @covers \Fisharebest\Webtrees\Tree::setPreference
     *
     * @return void
     */
    public function testTreePreferences(): void
    {
        $tree = Tree::create('tree-name', 'Tree title');

        $pref = $tree->getPreference('foo', 'default');
        $this->assertSame('default', $pref);

        $tree->setPreference('foo', 'bar');
        $pref = $tree->getPreference('foo', 'default');
        $this->assertSame('bar', $pref);
    }

    /**
     * @covers \Fisharebest\Webtrees\Tree::getUserPreference
     * @covers \Fisharebest\Webtrees\Tree::setUserPreference
     *
     * @return void
     */
    public function testUserTreePreferences(): void
    {
        $user_service = new UserService();
        $tree = Tree::create('tree-name', 'Tree title');
        $user = $user_service->create('user', 'User', 'user@example.com', 'secret');

        $pref = $tree->getUserPreference($user, 'foo', 'default');
        $this->assertSame('default', $pref);

        $tree->setUserPreference($user, 'foo', 'bar');
        $pref = $tree->getUserPreference($user, 'foo', 'default');
        $this->assertSame('bar', $pref);
    }

    /**
     * @covers \Fisharebest\Webtrees\Tree::getNewXref
     *
     * @return void
     */
    public function testGetNewXref(): void
    {
        $tree = Tree::create('tree-name', 'Tree title');

        $this->assertSame('X1', $tree->getNewXref());
        $this->assertSame('X2', $tree->getNewXref());
        $this->assertSame('X3', $tree->getNewXref());
        $this->assertSame('X4', $tree->getNewXref());
        $this->assertSame('X5', $tree->getNewXref());
    }

    /**
     * @covers \Fisharebest\Webtrees\Tree::createIndividual
     * @expectedException \InvalidArgumentException
     *
     * @return void
     */
    public function testCreateInvalidIndividual(): void
    {
        $user_service = new UserService();
        $tree = Tree::create('tree-name', 'Tree title');
        $user = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference('canadmin', '1');
        Auth::login($user);

        $tree->createIndividual("0 @@ FOO\n1 SEX U");
    }

    /**
     * @covers \Fisharebest\Webtrees\Tree::createIndividual
     *
     * @return void
     */
    public function testCreateIndividual(): void
    {
        $user_service = new UserService();
        $tree = Tree::create('tree-name', 'Tree title');
        $user = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference('canadmin', '1');
        Auth::login($user);

        $record = $tree->createIndividual("0 @@ INDI\n1 SEX F\n1 NAME Foo /Bar/");
        $this->assertTrue($record->isPendingAddition());

        $user->setPreference('auto_accept', '1');
        $record = $tree->createIndividual("0 @@ INDI\n1 SEX F\n1 NAME Foo /Bar/");
        $this->assertFalse($record->isPendingAddition());
    }

    /**
     * @covers \Fisharebest\Webtrees\Tree::createFamily
     * @expectedException \InvalidArgumentException
     *
     * @return void
     */
    public function testCreateInvalidFamily(): void
    {
        $user_service = new UserService();
        $tree = Tree::create('tree-name', 'Tree title');
        $user = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference('canadmin', '1');
        Auth::login($user);

        $tree->createFamily("0 @@ FOO\n1 MARR Y");
    }

    /**
     * @covers \Fisharebest\Webtrees\Tree::createFamily
     *
     * @return void
     */
    public function testCreateFamily(): void
    {
        $user_service = new UserService();
        $tree = Tree::create('tree-name', 'Tree title');
        $user = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference('canadmin', '1');
        Auth::login($user);

        $record = $tree->createFamily("0 @@ FAM\n1 MARR Y");
        $this->assertTrue($record->isPendingAddition());

        $user->setPreference('auto_accept', '1');
        $record = $tree->createFamily("0 @@ FAM\n1 MARR Y");
        $this->assertFalse($record->isPendingAddition());
    }

    /**
     * @covers \Fisharebest\Webtrees\Tree::createMediaObject
     * @expectedException \InvalidArgumentException
     *
     * @return void
     */
    public function testCreateInvalidMediaObject(): void
    {
        $user_service = new UserService();
        $tree = Tree::create('tree-name', 'Tree title');
        $user = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference('canadmin', '1');
        Auth::login($user);

        $tree->createMediaObject("0 @@ FOO\n1 MARR Y");
    }

    /**
     * @covers \Fisharebest\Webtrees\Tree::createMediaObject
     *
     * @return void
     */
    public function testCreateMediaObject(): void
    {
        $user_service = new UserService();
        $tree = Tree::create('tree-name', 'Tree title');
        $user = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference('canadmin', '1');
        Auth::login($user);

        $record = $tree->createMediaObject("0 @@ OBJE\n1 FILE foo.jpeg");
        $this->assertTrue($record->isPendingAddition());

        $user->setPreference('auto_accept', '1');
        $record = $tree->createMediaObject("0 @@ OBJE\n1 FILE foo.jpeg");
        $this->assertFalse($record->isPendingAddition());
    }

    /**
     * @covers \Fisharebest\Webtrees\Tree::createRecord
     * @expectedException \InvalidArgumentException
     *
     * @return void
     */
    public function testCreateInvalidRecord(): void
    {
        $user_service = new UserService();
        $tree = Tree::create('tree-name', 'Tree title');
        $user = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference('canadmin', '1');
        Auth::login($user);

        $tree->createRecord("0 @@FOO\n1 NOTE noted");
    }

    /**
     * @covers \Fisharebest\Webtrees\Tree::createRecord
     *
     * @return void
     */
    public function testCreateRecord(): void
    {
        $user_service = new UserService();
        $tree = Tree::create('tree-name', 'Tree title');
        $user = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference('canadmin', '1');
        Auth::login($user);

        $record = $tree->createRecord("0 @@ FOO\n1 NOTE noted");
        $this->assertTrue($record->isPendingAddition());

        $user->setPreference('auto_accept', '1');
        $record = $tree->createRecord("0 @@ FOO\n1 NOTE noted");
        $this->assertFalse($record->isPendingAddition());
    }

    /**
     * @covers \Fisharebest\Webtrees\Tree::significantIndividual
     *
     * @return void
     */
    public function testSignificantIndividual(): void
    {
        $user_service = new UserService();
        $tree = Tree::create('tree-name', 'Tree title');
        $user = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference('auto_accept', '1');
        Auth::login($user);

        // No individuals in tree?  Dummy individual
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
        $tree->setUserPreference($user, 'gedcomid', $record3->xref());
        $this->assertSame($record3->xref(), $tree->significantIndividual($user)->xref());

        // User record
        $tree->setUserPreference($user, 'rootid', $record4->xref());
        $this->assertSame($record4->xref(), $tree->significantIndividual($user)->xref());
    }

    /**
     * @covers \Fisharebest\Webtrees\Tree::importGedcomFile
     * @covers \Fisharebest\Webtrees\Tree::deleteGenealogyData
     * @covers \Fisharebest\Webtrees\Tree::delete
     *
     * @return void
     */
    public function testImportAndDeleteGedcomFile(): void
    {
        $tree = $this->importTree('demo.ged');
        $this->assertNotNull(Tree::findByName('demo.ged'));
        Site::setPreference('DEFAULT_GEDCOM', $tree->name());

        $tree->delete();

        $this->assertNull(Tree::findByName('demo.ged'));
        $this->assertSame('', Site::getPreference('DEFAULT_GEDCOM'));
    }

    /**
     * @covers \Fisharebest\Webtrees\Tree::hasPendingEdit
     *
     * @return void
     */
    public function testHasPendingEdits(): void
    {
        $user_service = new UserService();
        $tree = $this->importTree('demo.ged');
        $user = $user_service->create('admin', 'Administrator', 'admin@example.com', 'secret');
        $user->setPreference('canadmin', '1');
        Auth::login($user);

        $user->setPreference('auto_accept', '1');
        $tree->createIndividual("0 @@ INDI\n1 SEX F\n1 NAME Foo /Bar/");
        $this->assertFalse($tree->hasPendingEdit());

        $user->setPreference('auto_accept', '0');
        $tree->createIndividual("0 @@ INDI\n1 SEX F\n1 NAME Foo /Bar/");
        $this->assertTrue($tree->hasPendingEdit());
    }

    /**
     * @covers \Fisharebest\Webtrees\Tree::exportGedcom
     *
     * @return void
     */
    public function testExportGedcom(): void
    {
        $tree = $this->importTree('demo.ged');

        $fp = fopen('php://memory', 'wb');

        $tree->exportGedcom($fp);

        rewind($fp);

        $original = file_get_contents(__DIR__ . '/../data/demo.ged');
        $export   = stream_get_contents($fp);

        // The date and time in the HEAD record will be different.
        $original = preg_replace('/\n1 DATE .. ... ..../', '', $original, 1);
        $export   = preg_replace('/\n1 DATE .. ... ..../', '', $export, 1);
        $original = preg_replace('/\n2 TIME ..:..:../', '', $original, 1);
        $export   = preg_replace('/\n2 TIME ..:..:../', '', $export, 1);

        $this->assertSame($original, $export);
    }
}
