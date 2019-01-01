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
     * @return void
     */
    public function testCreate(): void
    {
        $user = User::create('user', 'User', 'user@example.com', 'secret');

        $this->assertSame(1, $user->getUserId());
        $this->assertSame('user', $user->getUserName());
        $this->assertSame('User', $user->getRealName());
        $this->assertSame('user@example.com', $user->getEmail());
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

        $this->assertSame($user1, $user2);
    }

    /**
     * @covers \Fisharebest\Webtrees\User::findByEmail
     * @return void
     */
    public function testFindUserByEmail(): void
    {
        $user1 = User::create('user', 'User', 'user@example.com', 'secret');
        $user2 = User::findByEmail($user1->getEmail());

        $this->assertSame($user1, $user2);
    }

    /**
     * @covers \Fisharebest\Webtrees\User::findByUserName
     * @return void
     */
    public function testFindUserByUserName(): void
    {
        $user1 = User::create('user', 'User', 'user@example.com', 'secret');
        $user2 = User::findByUserName($user1->getUserName());

        $this->assertSame($user1, $user2);
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

        $this->assertSame($user1, $user2);
        $this->assertSame($user1, $user3);
    }
}
