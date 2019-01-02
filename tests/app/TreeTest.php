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

/**
 * Test harness for the class Tree
 */
class TreeTest extends \Fisharebest\Webtrees\TestCase
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

        $this->assertSame(1, $tree->id());
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
        $tree = Tree::create('tree-name', 'Tree title');
        $user = User::create('user', 'User', 'user@example.com', 'secret');

        $pref = $tree->getUserPreference($user, 'foo', 'default');
        $this->assertSame('default', $pref);

        $tree->setUserPreference($user, 'foo', 'bar');
        $pref = $tree->getUserPreference($user, 'foo', 'default');
        $this->assertSame('bar', $pref);
    }

    /**
     * @covers \Fisharebest\Webtrees\Tree::createIndividual
     *
     * @return void
     */
    public function testCreateIndividual(): void
    {
        $tree = Tree::create('tree-name', 'Tree title');
        $user = User::create('user', 'User', 'user@example.com', 'secret');
        $user->setPreference('canadmin', '1');
        Auth::login($user);

        $gedcom = "0 @@ INDI\n1 SEX F\n1 NAME Foo /Bar/";

        $individual1 = $tree->createIndividual($gedcom);

        $this->assertTrue($individual1->isPendingAddition());

        $user->setPreference('auto_accept', '1');

        $individual2 = $tree->createIndividual($gedcom);

        $this->assertFalse($individual2->isPendingAddition());

    }
}
