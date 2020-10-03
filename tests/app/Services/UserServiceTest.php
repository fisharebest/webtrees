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

use Fisharebest\Webtrees\Contracts\CacheFactoryInterface;
use Fisharebest\Webtrees\Services\UserService;
use Symfony\Component\Cache\Adapter\NullAdapter;

/**
 * Test the UserService class
 */
class UserServiceTest extends TestCase
{
    protected static $uses_database = true;

    public function setUp(): void
    {
        parent::setUp();

        $cache_factory = $this->createMock(CacheFactoryInterface::class);
        $cache_factory->method('array')->willReturn(new Cache(new NullAdapter()));
        Registry::cache($cache_factory);
    }

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
     * @covers \Fisharebest\Webtrees\Services\UserService::delete
     * @return void
     */
    public function testDelete(): void
    {
        $user_service = new UserService();
        $user         = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user_id      = $user->id();
        $user_service->delete($user);

        $this->assertNull($user_service->find($user_id));
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\UserService::find
     * @return void
     */
    public function testFindNonExistingUser(): void
    {
        $user_service = new UserService();
        $user         = $user_service->find(999);

        $this->assertNull($user);
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\UserService::find
     * @return void
     */
    public function testFindExistingUser(): void
    {
        $user_service = new UserService();
        $user1        = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user2        = $user_service->find($user1->id());

        $this->assertSame($user1->id(), $user2->id());
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\UserService::findByEmail
     * @return void
     */
    public function testFindUserByEmail(): void
    {
        $user_service = new UserService();
        $user1        = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user2        = $user_service->findByEmail($user1->email());

        $this->assertSame($user1->id(), $user2->id());
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\UserService::findByUserName
     * @return void
     */
    public function testFindUserByUserName(): void
    {
        $user_service = new UserService();
        $user1        = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user2        = $user_service->findByUserName($user1->userName());

        $this->assertSame($user1->id(), $user2->id());
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\UserService::findByIdentifier
     * @return void
     */
    public function testFindUserByIdentifier(): void
    {
        $user_service = new UserService();
        $user1        = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user2        = $user_service->findByIdentifier($user1->userName());
        $user3        = $user_service->findByIdentifier($user1->email());

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
        $user         = $user_service->create('user', 'User', 'user@example.com', 'secret');
        Auth::login($user);
        $tree = $this->importTree('demo.ged');
        $indi = $tree->createIndividual('0 @@ INDI');
        $tree->setUserPreference($user, User::PREF_TREE_ACCOUNT_XREF, $indi->xref());

        $users = $user_service->findByIndividual($indi);

        $this->assertCount(1, $users);
        $this->assertSame($user->id(), $users[0]->id());
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\UserService::all
     * @return void
     */
    public function testFindAllUsers(): void
    {
        $user_service = new UserService();
        $user1        = $user_service->create('bbbbb', 'BBBBB', 'bbbbb@example.com', 'secret');
        $user2        = $user_service->create('aaaaa', 'AAAAA', 'aaaaa@example.com', 'secret');

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
        $admin->setPreference(User::PREF_IS_ADMINISTRATOR, '1');

        $users = $user_service->administrators();

        $this->assertCount(1, $users);
        $this->assertSame($admin->id(), $users[0]->id());
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\UserService::managers
     * @return void
     */
    public function testFindManagers(): void
    {
        $user_service = new UserService();
        $user1        = $user_service->create('user1', 'User1', 'user1@example.com', 'secret');
        $user2        = $user_service->create('user2', 'User2', 'user2@example.com', 'secret');
        $user3        = $user_service->create('user3', 'User3', 'user3@example.com', 'secret');
        $user4        = $user_service->create('user4', 'User4', 'user4@example.com', 'secret');

        $tree = $this->importTree('demo.ged');
        $tree->setUserPreference($user1, User::PREF_TREE_ROLE, User::ROLE_MANAGER);
        $tree->setUserPreference($user2, User::PREF_TREE_ROLE, User::ROLE_MODERATOR);
        $tree->setUserPreference($user3, User::PREF_TREE_ROLE, User::ROLE_EDITOR);
        $tree->setUserPreference($user4, User::PREF_TREE_ROLE, User::ROLE_MEMBER);

        $users = $user_service->managers();

        $this->assertCount(1, $users);
        $this->assertSame($user1->id(), $users[0]->id());
    }

    /**
     * @covers \Fisharebest\Webtrees\Services\UserService::moderators
     * @return void
     */
    public function testFindModerators(): void
    {
        $user_service = new UserService();
        $user1        = $user_service->create('user1', 'User1', 'user1@example.com', 'secret');
        $user2        = $user_service->create('user2', 'User2', 'user2@example.com', 'secret');
        $user3        = $user_service->create('user3', 'User3', 'user3@example.com', 'secret');
        $user4        = $user_service->create('user4', 'User4', 'user4@example.com', 'secret');

        $tree = $this->importTree('demo.ged');
        $tree->setUserPreference($user1, User::PREF_TREE_ROLE, User::ROLE_MANAGER);
        $tree->setUserPreference($user2, User::PREF_TREE_ROLE, User::ROLE_MODERATOR);
        $tree->setUserPreference($user3, User::PREF_TREE_ROLE, User::ROLE_EDITOR);
        $tree->setUserPreference($user4, User::PREF_TREE_ROLE, User::ROLE_MEMBER);

        $users = $user_service->moderators();

        $this->assertCount(1, $users);
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
        $user1        = $user_service->create('user1', 'User1', 'user1@example.com', 'secret');
        $user2        = $user_service->create('user2', 'User2', 'user2@example.com', 'secret');
        $user3        = $user_service->create('user3', 'User3', 'user3@example.com', 'secret');
        $user4        = $user_service->create('user4', 'User4', 'user4@example.com', 'secret');

        $user1->setPreference(User::PREF_IS_EMAIL_VERIFIED, '');
        $user1->setPreference(User::PREF_IS_ACCOUNT_APPROVED, '');
        $user2->setPreference(User::PREF_IS_EMAIL_VERIFIED, '');
        $user2->setPreference(User::PREF_IS_ACCOUNT_APPROVED, '1');
        $user3->setPreference(User::PREF_IS_EMAIL_VERIFIED, '1');
        $user3->setPreference(User::PREF_IS_ACCOUNT_APPROVED, '');
        $user4->setPreference(User::PREF_IS_EMAIL_VERIFIED, '1');
        $user4->setPreference(User::PREF_IS_ACCOUNT_APPROVED, '1');

        $users = $user_service->unapproved();

        $this->assertSame(2, $users->count());
        $this->assertSame('user1', $users[0]->userName());
        $this->assertSame('user3', $users[1]->userName());

        $users = $user_service->unverified();

        $this->assertSame(2, $users->count());
        $this->assertSame('user1', $users[0]->userName());
        $this->assertSame('user2', $users[1]->userName());
    }
}
