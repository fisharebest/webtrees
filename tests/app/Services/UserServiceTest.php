<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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
use Fisharebest\Webtrees\Services\UserService;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Cache\Adapter\NullAdapter;


#[CoversClass(UserService::class)]
class UserServiceTest extends TestCase
{
    protected static bool $uses_database = true;

    /**
     * Things to run before every test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $cache_factory = $this->createMock(CacheFactoryInterface::class);
        $cache_factory->method('array')->willReturn(new Cache(new NullAdapter()));
        Registry::cache($cache_factory);
    }

    public function testCreate(): void
    {
        $user_service = new UserService();

        $user = $user_service->create('user', 'User', 'user@example.com', 'secret');

        self::assertSame(1, $user->id());
    }

    public function testDelete(): void
    {
        $user_service = new UserService();
        $user         = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user_id      = $user->id();
        $user_service->delete($user);

        self::assertNull($user_service->find($user_id));
    }

    public function testFindNonExistingUser(): void
    {
        $user_service = new UserService();
        $user         = $user_service->find(999);

        self::assertNull($user);
    }

    public function testFindExistingUser(): void
    {
        $user_service = new UserService();
        $user1        = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user2        = $user_service->find($user1->id());

        self::assertSame($user1->id(), $user2->id());
    }

    public function testFindUserByEmail(): void
    {
        $user_service = new UserService();
        $user1        = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user2        = $user_service->findByEmail($user1->email());

        self::assertSame($user1->id(), $user2->id());
    }

    public function testFindUserByUserName(): void
    {
        $user_service = new UserService();
        $user1        = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user2        = $user_service->findByUserName($user1->userName());

        self::assertSame($user1->id(), $user2->id());
    }

    public function testFindUserByIdentifier(): void
    {
        $user_service = new UserService();
        $user1        = $user_service->create('user', 'User', 'user@example.com', 'secret');
        $user2        = $user_service->findByIdentifier($user1->userName());
        $user3        = $user_service->findByIdentifier($user1->email());

        self::assertSame($user1->id(), $user2->id());
        self::assertSame($user1->id(), $user3->id());
    }

    public function testFindUsersByIndividual(): void
    {
        $user_service = new UserService();
        $user         = $user_service->create('user', 'User', 'user@example.com', 'secret');
        Auth::login($user);
        $tree = $this->importTree('demo.ged');
        $indi = $tree->createIndividual('0 @@ INDI');
        $tree->setUserPreference($user, UserInterface::PREF_TREE_ACCOUNT_XREF, $indi->xref());

        $users = $user_service->findByIndividual($indi);

        self::assertCount(1, $users);
        self::assertSame($user->id(), $users[0]->id());
    }

    public function testFindAllUsers(): void
    {
        $user_service = new UserService();
        $user1        = $user_service->create('bbbbb', 'BBBBB', 'bbbbb@example.com', 'secret');
        $user2        = $user_service->create('aaaaa', 'AAAAA', 'aaaaa@example.com', 'secret');

        $users = $user_service->all();

        self::assertSame(2, $users->count());
        self::assertSame($user2->id(), $users[0]->id());
        self::assertSame($user1->id(), $users[1]->id());
    }

    public function testFindAdministrators(): void
    {
        $user_service = new UserService();
        $user_service->create('user', 'User', 'user@example.com', 'secret');

        $admin = $user_service->create('admin', 'Admin', 'admin@example.com', 'secret');
        $admin->setPreference(UserInterface::PREF_IS_ADMINISTRATOR, '1');

        $users = $user_service->administrators();

        self::assertCount(1, $users);
        self::assertSame($admin->id(), $users[0]->id());
    }

    public function testFindManagers(): void
    {
        $user_service = new UserService();
        $user1        = $user_service->create('user1', 'User1', 'user1@example.com', 'secret');
        $user2        = $user_service->create('user2', 'User2', 'user2@example.com', 'secret');
        $user3        = $user_service->create('user3', 'User3', 'user3@example.com', 'secret');
        $user4        = $user_service->create('user4', 'User4', 'user4@example.com', 'secret');

        $tree = $this->importTree('demo.ged');
        $tree->setUserPreference($user1, UserInterface::PREF_TREE_ROLE, UserInterface::ROLE_MANAGER);
        $tree->setUserPreference($user2, UserInterface::PREF_TREE_ROLE, UserInterface::ROLE_MODERATOR);
        $tree->setUserPreference($user3, UserInterface::PREF_TREE_ROLE, UserInterface::ROLE_EDITOR);
        $tree->setUserPreference($user4, UserInterface::PREF_TREE_ROLE, UserInterface::ROLE_MEMBER);

        $users = $user_service->managers();

        self::assertCount(1, $users);
        self::assertSame($user1->id(), $users[0]->id());
    }

    public function testFindModerators(): void
    {
        $user_service = new UserService();
        $user1        = $user_service->create('user1', 'User1', 'user1@example.com', 'secret');
        $user2        = $user_service->create('user2', 'User2', 'user2@example.com', 'secret');
        $user3        = $user_service->create('user3', 'User3', 'user3@example.com', 'secret');
        $user4        = $user_service->create('user4', 'User4', 'user4@example.com', 'secret');

        $tree = $this->importTree('demo.ged');
        $tree->setUserPreference($user1, UserInterface::PREF_TREE_ROLE, UserInterface::ROLE_MANAGER);
        $tree->setUserPreference($user2, UserInterface::PREF_TREE_ROLE, UserInterface::ROLE_MODERATOR);
        $tree->setUserPreference($user3, UserInterface::PREF_TREE_ROLE, UserInterface::ROLE_EDITOR);
        $tree->setUserPreference($user4, UserInterface::PREF_TREE_ROLE, UserInterface::ROLE_MEMBER);

        $users = $user_service->moderators();

        self::assertCount(1, $users);
        self::assertSame($user2->id(), $users[0]->id());
    }

    public function testFindUnapprovedAndUnverified(): void
    {
        $user_service = new UserService();
        $user1        = $user_service->create('user1', 'User1', 'user1@example.com', 'secret');
        $user2        = $user_service->create('user2', 'User2', 'user2@example.com', 'secret');
        $user3        = $user_service->create('user3', 'User3', 'user3@example.com', 'secret');
        $user4        = $user_service->create('user4', 'User4', 'user4@example.com', 'secret');

        $user1->setPreference(UserInterface::PREF_IS_EMAIL_VERIFIED, '');
        $user1->setPreference(UserInterface::PREF_IS_ACCOUNT_APPROVED, '');
        $user2->setPreference(UserInterface::PREF_IS_EMAIL_VERIFIED, '');
        $user2->setPreference(UserInterface::PREF_IS_ACCOUNT_APPROVED, '1');
        $user3->setPreference(UserInterface::PREF_IS_EMAIL_VERIFIED, '1');
        $user3->setPreference(UserInterface::PREF_IS_ACCOUNT_APPROVED, '');
        $user4->setPreference(UserInterface::PREF_IS_EMAIL_VERIFIED, '1');
        $user4->setPreference(UserInterface::PREF_IS_ACCOUNT_APPROVED, '1');

        $users = $user_service->unapproved();

        self::assertSame(2, $users->count());
        self::assertSame('user1', $users[0]->userName());
        self::assertSame('user3', $users[1]->userName());

        $users = $user_service->unverified();

        self::assertSame(2, $users->count());
        self::assertSame('user1', $users[0]->userName());
        self::assertSame('user2', $users[1]->userName());
    }
}
