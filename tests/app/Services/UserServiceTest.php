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

/**
 * Test the UserService class
 */
class UserServiceTest extends TestCase
{
    protected static $uses_database = true;

    /**
     * @covers \Fisharebest\Webtrees\Services\UserService::create
     * @return void
     */
    public function testCreate(): void
    {
        $user_service = new UserService();

        $user = $user_service->create('user', 'User', 'user@example.com', 'secret');

        $this->assertSame(1, $user->id());
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\UserService::setUserName
     * @covers \Fisharebest\Webtrees\Services\UserService::userName
     * @covers \Fisharebest\Webtrees\Services\UserService::setRealName
     * @covers \Fisharebest\Webtrees\Services\UserService::realName
     * @covers \Fisharebest\Webtrees\Services\UserService::setEmail
     * @covers \Fisharebest\Webtrees\Services\UserService::email
     * @covers \Fisharebest\Webtrees\Services\UserService::setPassword
     * @covers \Fisharebest\Webtrees\Services\UserService::checkPassword
     * @return void
     */
    public function testGettersAndSetters(): void
    {
        $user_service = new UserService();
        $user = $user_service->create('user', 'User', 'user@example.com', 'secret');

        $this->assertSame(1, $user->id());

        $this->assertSame('user', $user->userName());
        $user->setUserName('foo');
        $this->assertSame('foo', $user->userName());

        $this->assertSame('User', $user->realName());
        $user->setRealName('Foo');
        $this->assertSame('Foo', $user->realName());

        $this->assertSame('user@example.com', $user->email());
        $user->setEmail('foo@example.com');
        $this->assertSame('foo@example.com', $user->email());

        $this->assertTrue($user->checkPassword('secret'));
        $user->setPassword('letmein');
        $this->assertTrue($user->checkPassword('letmein'));
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\UserService::checkPassword
     * @return void
     */
    public function testCheckPasswordCaseSensitive(): void
    {
        $user_service = new UserService();
        $user = $user_service->create('user', 'User', 'user@example.com', 'secret');

        $this->assertTrue($user->checkPassword('secret'));
        $this->assertFalse($user->checkPassword('SECRET'));
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\UserService::delete
     * @return void
     */
    public function testDelete(): void
    {
        $user_service = new UserService();
        $user = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user_id = $user->id();
        $user_service->delete($user);

        $this->assertNull($user_service->find($user_id));
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\UserService::setPreference
     * @covers \Fisharebest\Webtrees\Services\UserService::getPreference
     * @return void
     */
    public function testPreferences(): void
    {
        $user_service = new UserService();
        $user = $user_service->create('user', 'User', 'user@example.com', 'secret');

        $this->assertSame('', $user->getPreference('foo'));
        $user->setPreference('foo', 'bar');
        $this->assertSame('bar', $user->getPreference('foo'));
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\UserService::find
     * @return void
     */
    public function testFindNonExistingUser(): void
    {
        $user_service = new UserService();
        $user = $user_service->find(999);

        $this->assertNull($user);
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\UserService::find
     * @return void
     */
    public function testFindExistingUser(): void
    {
        $user_service = new UserService();
        $user1 = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user2 = $user_service->find($user1->id());

        $this->assertSame($user1->id(), $user2->id());
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\UserService::findByEmail
     * @return void
     */
    public function testFindUserByEmail(): void
    {
        $user_service = new UserService();
        $user1 = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user2 = $user_service->findByEmail($user1->email());

        $this->assertSame($user1->id(), $user2->id());
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\UserService::findByUserName
     * @return void
     */
    public function testFindUserByUserName(): void
    {
        $user_service = new UserService();
        $user1 = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user2 = $user_service->findByUserName($user1->userName());

        $this->assertSame($user1->id(), $user2->id());
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\UserService::findByIdentifier
     * @return void
     */
    public function testFindUserByIdentifier(): void
    {
        $user_service = new UserService();
        $user1 = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user2 = $user_service->findByIdentifier($user1->userName());
        $user3 = $user_service->findByIdentifier($user1->email());

        $this->assertSame($user1->id(), $user2->id());
        $this->assertSame($user1->id(), $user3->id());
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\UserService::findByIndividual
     * @return void
     */
    public function testFindUsersByIndividual(): void
    {
        $user_service = new UserService();
        $user = $user_service->create('user', 'User', 'user@example.com', 'secret');
        Auth::login($user);
        $tree = $this->importTree('demo.ged');
        $indi = $tree->createIndividual('0 @@ INDI');
        $tree->setUserPreference($user, 'gedcomid', $indi->xref());

        $users = $user_service->findByIndividual($indi);

        $this->assertSame(1, count($users));
        $this->assertSame($user->id(), $users[0]->id());
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\UserService::all
     * @return void
     */
    public function testFindAllUsers(): void
    {
        $user_service = new UserService();
        $user1 = $user_service->create('bbbbb', 'BBBBB', 'bbbbb@example.com', 'secret');
        $user2 = $user_service->create('aaaaa', 'AAAAA', 'aaaaa@example.com', 'secret');

        $users = $user_service->all();

        $this->assertSame(2, $users->count());
        $this->assertSame($user2->id(), $users[0]->id());
        $this->assertSame($user1->id(), $users[1]->id());
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\UserService::administrators
     * @return void
     */
    public function testFindAdministrators(): void
    {
        $user_service = new UserService();
        $user_service->create('user', 'User', 'user@example.com', 'secret');

        $admin = $user_service->create('admin', 'Admin', 'admin@example.com', 'secret');
        $admin->setPreference('canadmin', '1');

        $users = $user_service->administrators();

        $this->assertSame(1, count($users));
        $this->assertSame($admin->id(), $users[0]->id());
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\UserService::managers
     * @return void
     */
    public function testFindManagers(): void
    {
        $user_service = new UserService();
        $user1 = $user_service->create('user1', 'User1', 'user1@example.com', 'secret');
        $user2 = $user_service->create('user2', 'User2', 'user2@example.com', 'secret');
        $user3 = $user_service->create('user3', 'User3', 'user3@example.com', 'secret');
        $user4 = $user_service->create('user4', 'User4', 'user4@example.com', 'secret');

        $tree = $this->importTree('demo.ged');
        $tree->setUserPreference($user1, 'canedit', 'admin');
        $tree->setUserPreference($user2, 'canedit', 'accept');
        $tree->setUserPreference($user3, 'canedit', 'edit');
        $tree->setUserPreference($user4, 'canedit', 'access');

        $users = $user_service->managers();

        $this->assertSame(1, count($users));
        $this->assertSame($user1->id(), $users[0]->id());
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\UserService::moderators
     * @return void
     */
    public function testFindModerators(): void
    {
        $user_service = new UserService();
        $user1 = $user_service->create('user1', 'User1', 'user1@example.com', 'secret');
        $user2 = $user_service->create('user2', 'User2', 'user2@example.com', 'secret');
        $user3 = $user_service->create('user3', 'User3', 'user3@example.com', 'secret');
        $user4 = $user_service->create('user4', 'User4', 'user4@example.com', 'secret');

        $tree = $this->importTree('demo.ged');
        $tree->setUserPreference($user1, 'canedit', 'admin');
        $tree->setUserPreference($user2, 'canedit', 'accept');
        $tree->setUserPreference($user3, 'canedit', 'edit');
        $tree->setUserPreference($user4, 'canedit', 'access');

        $users = $user_service->moderators();

        $this->assertSame(1, count($users));
        $this->assertSame($user2->id(), $users[0]->id());
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\UserService::unapproved
     * @covers \Fisharebest\Webtrees\Services\UserService::unverified
     * @return void
     */
    public function testFindUnapprovedAndUnverified(): void
    {
        $user_service = new UserService();
        $user1 = $user_service->create('user1', 'User1', 'user1@example.com', 'secret');
        $user2 = $user_service->create('user2', 'User2', 'user2@example.com', 'secret');
        $user3 = $user_service->create('user3', 'User3', 'user3@example.com', 'secret');
        $user4 = $user_service->create('user4', 'User4', 'user4@example.com', 'secret');

        $user1->setPreference('verified', '0');
        $user1->setPreference('verified_by_admin', '0');
        $user2->setPreference('verified', '0');
        $user2->setPreference('verified_by_admin', '1');
        $user3->setPreference('verified', '1');
        $user3->setPreference('verified_by_admin', '0');
        $user4->setPreference('verified', '1');
        $user4->setPreference('verified_by_admin', '1');

        $users = $user_service->unapproved();

        $this->assertSame(2, $users->count());
        $this->assertSame($user1->id(), $users[0]->id());
        $this->assertSame($user3->id(), $users[1]->id());

        $users = $user_service->unverified();

        $this->assertSame(2, $users->count());
        $this->assertSame($user1->id(), $users[0]->id());
        $this->assertSame($user2->id(), $users[1]->id());
    }
}
