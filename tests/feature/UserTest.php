<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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
 * Test the user functions
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class UserTest extends \Fisharebest\Webtrees\TestCase
{
    protected static $uses_database = true;

    protected static $uses_transactions = true;

    /**
     * Test administrators.
     *
     * @covers \Fisharebest\Webtrees\Auth
     * @covers \Fisharebest\Webtrees\Tree
     * @covers \Fisharebest\Webtrees\User
     *
     * @return void
     */
    public function testAdministrator(): void
    {
        // By default, new users are not admins.
        $user = User::create('admin', 'Administrator', 'admin@example.com', 'secret');
        $this->assertFalse(Auth::isAdmin($user));

        // Make the user a manager.
        $user->setPreference('canadmin', '1');
        $this->assertTrue(Auth::isAdmin($user));

        // Test that the current user is an admin.
        $this->assertFalse(Auth::isAdmin());
        Auth::login($user);
        $this->assertTrue(Auth::isAdmin());
    }

    /**
     * Test managers.
     *
     * @covers \Fisharebest\Webtrees\Auth
     * @covers \Fisharebest\Webtrees\Tree
     * @covers \Fisharebest\Webtrees\User
     *
     * @return void
     */
    public function testManager(): void
    {
        // By default, new users are not managers.
        $user = User::create('manager', 'Manager', 'manager@example.com', 'secret');
        $tree = Tree::create('test', 'Test');
        $tree->setPreference('imported', '1');
        $this->assertFalse(Auth::isManager($tree, $user));

        // Make the user a manager.
        $tree->setUserPreference($user, 'canedit', 'admin');
        $this->assertTrue(Auth::isManager($tree, $user));

        // Test that a specific user is a manager.
        $this->assertFalse(Auth::isManager($tree));
        Auth::login($user);
        $this->assertTrue(Auth::isManager($tree));
    }

    /**
     * Test moderators.
     *
     * @covers \Fisharebest\Webtrees\Auth
     * @covers \Fisharebest\Webtrees\Tree
     * @covers \Fisharebest\Webtrees\User
     *
     * @return void
     */
    public function testModerator(): void
    {
        // By default, new users are not moderators.
        $user = User::create('moderator', 'Moderator', 'moderator@example.com', 'secret');
        $tree = Tree::create('test', 'Test');
        $this->assertFalse(Auth::isModerator($tree, $user));

        // Make the user a moderator.
        $tree->setUserPreference($user, 'canedit', 'accept');
        $this->assertTrue(Auth::isModerator($tree, $user));

        // Test that a specific user is a moderator.
        $this->assertFalse(Auth::isModerator($tree));
        Auth::login($user);
        $this->assertTrue(Auth::isModerator($tree));
    }

    /**
     * Test editors.
     *
     * @covers \Fisharebest\Webtrees\Auth
     * @covers \Fisharebest\Webtrees\Tree
     * @covers \Fisharebest\Webtrees\User
     *
     * @return void
     */
    public function testEditor(): void
    {
        // By default, new users are not editors.
        $user = User::create('editor', 'Editor', 'editor@example.com', 'secret');
        $tree = Tree::create('test', 'Test');
        $this->assertFalse(Auth::isEditor($tree, $user));

        // Make the user an editor.
        $tree->setUserPreference($user, 'canedit', 'edit');
        $this->assertTrue(Auth::isEditor($tree, $user));

        // Test that a specific user is an editor.
        $this->assertFalse(Auth::isEditor($tree));
        Auth::login($user);
        $this->assertTrue(Auth::isEditor($tree));
    }

    /**
     * Test members.
     *
     * @covers \Fisharebest\Webtrees\Auth
     * @covers \Fisharebest\Webtrees\Tree
     * @covers \Fisharebest\Webtrees\User
     *
     * @return void
     */
    public function testMember(): void
    {
        // By default, new users are not members.
        $user = User::create('member', 'Member', 'member@example.com', 'secret');
        $tree = Tree::create('test', 'Test');
        $this->assertFalse(Auth::isMember($tree, $user));

        // Make the user a members.
        $tree->setUserPreference($user, 'canedit', 'edit');
        $this->assertTrue(Auth::isMember($tree, $user));

        // Test that a specific user is a member.
        $this->assertFalse(Auth::isMember($tree));
        Auth::login($user);
        $this->assertTrue(Auth::isMember($tree));
    }
}
