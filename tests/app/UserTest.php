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
 * Test the user functions
 */
class UserTest extends \Fisharebest\Webtrees\TestCase
{
    protected static $uses_database = true;

    /**
     * @covers \Fisharebest\Webtrees\User::__construct
     * @covers \Fisharebest\Webtrees\User::create
     * @covers \Fisharebest\Webtrees\User::getUserId
     * @return void
     */
    public function testCreate(): void
    {
        $user = User::create('user', 'User', 'user@example.com', 'secret');

        $this->assertSame(1, $user->getUserId());
    }

    /**
     * @covers \Fisharebest\Webtrees\User::setUserName
     * @covers \Fisharebest\Webtrees\User::getUserName
     * @covers \Fisharebest\Webtrees\User::setRealName
     * @covers \Fisharebest\Webtrees\User::getRealName
     * @covers \Fisharebest\Webtrees\User::setEmail
     * @covers \Fisharebest\Webtrees\User::getEmail
     * @covers \Fisharebest\Webtrees\User::setPassword
     * @covers \Fisharebest\Webtrees\User::checkPassword
     * @return void
     */
    public function testGettersAndSetters(): void
    {
        $user = User::create('user', 'User', 'user@example.com', 'secret');

        $this->assertSame(1, $user->getUserId());

        $this->assertSame('user', $user->getUserName());
        $user->setUserName('foo');
        $this->assertSame('foo', $user->getUserName());

        $this->assertSame('User', $user->getRealName());
        $user->setRealName('Foo');
        $this->assertSame('Foo', $user->getRealName());

        $this->assertSame('user@example.com', $user->getEmail());
        $user->setEmail('foo@example.com');
        $this->assertSame('foo@example.com', $user->getEmail());

        $this->assertTrue($user->checkPassword('secret'));
        $user->setPassword('letmein');
        $this->assertTrue($user->checkPassword('letmein'));
    }

    /**
     * @covers \Fisharebest\Webtrees\User::checkPassword
     * @return void
     */
    public function testCheckPasswordCaseSensitive(): void
    {
        $user = User::create('user', 'User', 'user@example.com', 'secret');

        $this->assertTrue($user->checkPassword('secret'));
        $this->assertFalse($user->checkPassword('SECRET'));
    }

    /**
     * @covers \Fisharebest\Webtrees\User::delete
     * @return void
     */
    public function testDelete(): void
    {
        $user = User::create('user', 'User', 'user@example.com', 'secret');
        $user_id = $user->getUserId();
        $user->delete();

        $this->assertNull(User::find($user_id));
    }

    /**
     * @covers \Fisharebest\Webtrees\User::setPreference
     * @covers \Fisharebest\Webtrees\User::getPreference
     * @return void
     */
    public function testPreferences(): void
    {
        $user = User::create('user', 'User', 'user@example.com', 'secret');

        $this->assertSame('', $user->getPreference('foo'));
        $user->setPreference('foo', 'bar');
        $this->assertSame('bar', $user->getPreference('foo'));
    }

    /**
     * @covers \Fisharebest\Webtrees\User::find
     * @return void
     */
    public function testFindNonExistingUser(): void
    {
        $user = User::find(999);

        $this->assertNull($user);
    }

    /**
     * @covers \Fisharebest\Webtrees\User::find
     * @return void
     */
    public function testFindExistingUser(): void
    {
        $user1 = User::create('user', 'User', 'user@example.com', 'secret');
        $user2 = User::find($user1->getUserId());

        $this->assertSame($user1->getuserId(), $user2->getuserId());
    }

    /**
     * @covers \Fisharebest\Webtrees\User::findByEmail
     * @return void
     */
    public function testFindUserByEmail(): void
    {
        $user1 = User::create('user', 'User', 'user@example.com', 'secret');
        $user2 = User::findByEmail($user1->getEmail());

        $this->assertSame($user1->getuserId(), $user2->getuserId());
    }

    /**
     * @covers \Fisharebest\Webtrees\User::findByUserName
     * @return void
     */
    public function testFindUserByUserName(): void
    {
        $user1 = User::create('user', 'User', 'user@example.com', 'secret');
        $user2 = User::findByUserName($user1->getUserName());

        $this->assertSame($user1->getuserId(), $user2->getuserId());
    }

    /**
     * @covers \Fisharebest\Webtrees\User::findByIdentifier
     * @return void
     */
    public function testFindUserByIdentifier(): void
    {
        $user1 = User::create('user', 'User', 'user@example.com', 'secret');
        $user2 = User::findByIdentifier($user1->getUsername());
        $user3 = User::findByIdentifier($user1->getEmail());

        $this->assertSame($user1->getuserId(), $user2->getuserId());
        $this->assertSame($user1->getuserId(), $user3->getuserId());
    }

    /**
     * @covers \Fisharebest\Webtrees\User::findByIndividual
     * @return void
     */
    public function testFindUsersByIndividual(): void
    {
        $user = User::create('user', 'User', 'user@example.com', 'secret');
        Auth::login($user);
        $tree = $this->importTree('demo.ged');
        $indi = $tree->createIndividual('0 @@ INDI');
        $tree->setUserPreference($user, 'gedcomid', $indi->xref());

        $users = User::findByIndividual($indi);

        $this->assertSame(1, count($users));
        $this->assertSame($user->getuserId(), $users[0]->getuserId());
    }

    /**
     * @covers \Fisharebest\Webtrees\User::all
     * @return void
     */
    public function testFindAllUsers(): void
    {
        $user1 = User::create('bbbbb', 'BBBBB', 'bbbbb@example.com', 'secret');
        $user2 = User::create('aaaaa', 'AAAAA', 'aaaaa@example.com', 'secret');

        $users = User::all();

        $this->assertSame(2, $users->count());
        $this->assertSame($user2->getUserId(), $users[0]->getUserId());
        $this->assertSame($user1->getUserId(), $users[1]->getUserId());
    }

    /**
     * @covers \Fisharebest\Webtrees\User::administrators
     * @return void
     */
    public function testFindAdministrators(): void
    {
        User::create('user', 'User', 'user@example.com', 'secret');

        $admin = User::create('admin', 'Admin', 'admin@example.com', 'secret');
        $admin->setPreference('canadmin', '1');

        $users = User::administrators();

        $this->assertSame(1, count($users));
        $this->assertSame($admin->getUserId(), $users[0]->getUserId());
    }

    /**
     * @covers \Fisharebest\Webtrees\User::managers
     * @return void
     */
    public function testFindManagers(): void
    {
        $user1 = User::create('user1', 'User1', 'user1@example.com', 'secret');
        $user2 = User::create('user2', 'User2', 'user2@example.com', 'secret');
        $user3 = User::create('user3', 'User3', 'user3@example.com', 'secret');
        $user4 = User::create('user4', 'User4', 'user4@example.com', 'secret');

        $tree = $this->importTree('demo.ged');
        $tree->setUserPreference($user1, 'canedit', 'admin');
        $tree->setUserPreference($user2, 'canedit', 'accept');
        $tree->setUserPreference($user3, 'canedit', 'edit');
        $tree->setUserPreference($user4, 'canedit', 'access');

        $users = User::managers();

        $this->assertSame(1, count($users));
        $this->assertSame($user1->getuserId(), $users[0]->getuserId());
    }

    /**
     * @covers \Fisharebest\Webtrees\User::moderators
     * @return void
     */
    public function testFindModerators(): void
    {
        $user1 = User::create('user1', 'User1', 'user1@example.com', 'secret');
        $user2 = User::create('user2', 'User2', 'user2@example.com', 'secret');
        $user3 = User::create('user3', 'User3', 'user3@example.com', 'secret');
        $user4 = User::create('user4', 'User4', 'user4@example.com', 'secret');

        $tree = $this->importTree('demo.ged');
        $tree->setUserPreference($user1, 'canedit', 'admin');
        $tree->setUserPreference($user2, 'canedit', 'accept');
        $tree->setUserPreference($user3, 'canedit', 'edit');
        $tree->setUserPreference($user4, 'canedit', 'access');

        $users = User::moderators();

        $this->assertSame(1, count($users));
        $this->assertSame($user2->getuserId(), $users[0]->getuserId());
    }

    /**
     * @covers \Fisharebest\Webtrees\User::unapproved
     * @covers \Fisharebest\Webtrees\User::unverified
     * @return void
     */
    public function testFindUnapprovedAndUnverified(): void
    {
        $user1 = User::create('user1', 'User1', 'user1@example.com', 'secret');
        $user2 = User::create('user2', 'User2', 'user2@example.com', 'secret');
        $user3 = User::create('user3', 'User3', 'user3@example.com', 'secret');
        $user4 = User::create('user4', 'User4', 'user4@example.com', 'secret');

        $user1->setPreference('verified', '0');
        $user1->setPreference('verified_by_admin', '0');
        $user2->setPreference('verified', '0');
        $user2->setPreference('verified_by_admin', '1');
        $user3->setPreference('verified', '1');
        $user3->setPreference('verified_by_admin', '0');
        $user4->setPreference('verified', '1');
        $user4->setPreference('verified_by_admin', '1');

        $users = User::unapproved();

        $this->assertSame(2, $users->count());
        $this->assertSame($user1->getuserId(), $users[0]->getuserId());
        $this->assertSame($user3->getuserId(), $users[1]->getuserId());

        $users = User::unverified();

        $this->assertSame(2, $users->count());
        $this->assertSame($user1->getuserId(), $users[0]->getuserId());
        $this->assertSame($user2->getuserId(), $users[1]->getuserId());
    }
}
