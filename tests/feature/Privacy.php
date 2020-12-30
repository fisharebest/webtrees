<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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

use function strip_tags;

/**
 * Test the privacy logic
 */
class Privacy extends TestCase
{
    protected static $uses_database = true;

    /**
     * @return void
     */
    public function testRecordAccess(): void
    {
        $tree = $this->importTree('demo.ged');

        // Identify some individuals in the tree
        $queen_elizabeth = Registry::individualFactory()->make('X1030', $tree);
        self::assertInstanceOf(Individual::class, $queen_elizabeth);
        self::assertSame('Queen Elizabeth II', strip_tags($queen_elizabeth->fullName()));

        $prince_charles = Registry::individualFactory()->make('X1052', $tree);
        self::assertInstanceOf(Individual::class, $prince_charles);
        self::assertSame('Charles, Prince of Wales', strip_tags($prince_charles->fullName()));

        $savannah = Registry::individualFactory()->make('X1044', $tree);
        self::assertInstanceOf(Individual::class, $savannah);
        self::assertSame('Savannah Anne Kathleen Phillips', strip_tags($savannah->fullName()));

        $beatrice = Registry::individualFactory()->make('X1047', $tree);
        self::assertInstanceOf(Individual::class, $beatrice);
        self::assertSame('Princess Beatrice of York', strip_tags($beatrice->fullName()));

        $user_service = new UserService();

        $admin = $user_service->create('admin', 'admin', 'admin', '*');
        $admin->setPreference(User::PREF_IS_ADMINISTRATOR, '1');

        $manager = $user_service->create('manager', 'manager', 'manager', '*');
        $tree->setUserPreference($manager, User::PREF_TREE_ROLE, User::ROLE_MANAGER);

        $moderator = $user_service->create('moderator', 'moderator', 'moderator', '*');
        $tree->setUserPreference($moderator, User::PREF_TREE_ROLE, User::ROLE_MODERATOR);

        $editor = $user_service->create('editor', 'editor', 'editor', '*');
        $tree->setUserPreference($editor, User::PREF_TREE_ROLE, User::ROLE_EDITOR);

        $member = $user_service->create('member', 'member', 'member', '*');
        $tree->setUserPreference($member, User::PREF_TREE_ROLE, User::ROLE_MEMBER);

        $visitor = $user_service->create('visitor', 'visitor', 'visitor', '*');
        $tree->setUserPreference($visitor, User::PREF_TREE_ROLE, User::ROLE_VISITOR);

        // Enable privacy functions
        $tree->setPreference('HIDE_LIVE_PEOPLE', '1');

        Auth::login($admin);
        self::assertTrue(Auth::isAdmin(), 'admin isAdmin()');
        self::assertTrue(Auth::isManager($tree), 'admin isManager()');
        self::assertTrue(Auth::isModerator($tree), 'admin isModerator()');
        self::assertTrue(Auth::isEditor($tree), 'admin isEditor()');
        self::assertTrue(Auth::isMember($tree), 'admin isMember()');

        Auth::login($manager);
        self::assertFalse(Auth::isAdmin(), 'manager NOT isAdmin()');
        self::assertTrue(Auth::isManager($tree, $manager), 'manager isManager()');
        self::assertTrue(Auth::isModerator($tree, $manager), 'manager isModerator()');
        self::assertTrue(Auth::isEditor($tree, $manager), 'manager isEditor()');
        self::assertTrue(Auth::isMember($tree, $manager), 'manager isMember()');

        Auth::login($moderator);
        self::assertFalse(Auth::isAdmin(), 'moderator NOT isAdmin()');
        self::assertFalse(Auth::isManager($tree, $moderator), 'moderator NOT isManager()');
        self::assertTrue(Auth::isModerator($tree, $moderator), 'moderator isModerator()');
        self::assertTrue(Auth::isEditor($tree, $moderator), 'moderator isEditor()');
        self::assertTrue(Auth::isMember($tree, $moderator), 'moderator isMember()');

        Auth::login($editor);
        self::assertFalse(Auth::isAdmin(), 'editor NOT isAdmin()');
        self::assertFalse(Auth::isManager($tree, $editor), 'editor NOT isManager()');
        self::assertFalse(Auth::isModerator($tree, $editor), 'editor isModerator()');
        self::assertTrue(Auth::isEditor($tree, $editor), 'editor isEditor()');
        self::assertTrue(Auth::isMember($tree, $editor), 'editor isMember()');

        Auth::login($member);
        self::assertFalse(Auth::isAdmin(), 'member NOT isAdmin()');
        self::assertFalse(Auth::isManager($tree, $member), 'member NOT isManager()');
        self::assertFalse(Auth::isModerator($tree, $member), 'member isModerator()');
        self::assertFalse(Auth::isEditor($tree, $member), 'member isEditor()');
        self::assertTrue(Auth::isMember($tree, $member), 'member isMember()');

        Auth::login($visitor);
        self::assertFalse(Auth::isAdmin(), 'visitor NOT isAdmin()');
        self::assertFalse(Auth::isManager($tree, $visitor), 'visitor NOT isManager()');
        self::assertFalse(Auth::isModerator($tree, $visitor), 'visitor isModerator()');
        self::assertFalse(Auth::isEditor($tree, $visitor), 'visitor isEditor()');
        self::assertFalse(Auth::isMember($tree, $visitor), 'visitor isMember()');

        Auth::logout();



        Auth::login($admin);
        self::assertTrue($queen_elizabeth->canShow(), 'admin can see living individual with RESN=none');
        self::assertTrue($prince_charles->canShow(), 'admin can see living individual');

        Auth::login($manager);
        self::assertTrue($queen_elizabeth->canShow(), 'manager can see living individual with RESN=none');
        self::assertTrue($prince_charles->canShow(), 'manager can see living individual');

        Auth::login($moderator);
        self::assertTrue($queen_elizabeth->canShow(), 'moderator can see living individual with RESN=none');
        self::assertTrue($prince_charles->canShow(), 'moderator can see living individual');

        Auth::login($editor);
        self::assertTrue($queen_elizabeth->canShow(), 'editor can see living individual with RESN=none');
        self::assertTrue($prince_charles->canShow(), 'editor can see living individual');

        Auth::login($member);
        self::assertTrue($queen_elizabeth->canShow(), 'member can see living individual with RESN=none');
        self::assertTrue($prince_charles->canShow(), 'member can see living individual');

        Auth::login($visitor);
        self::assertTrue($queen_elizabeth->canShow(), 'visitor can see living individual with RESN=none');
        self::assertFalse($prince_charles->canShow(), 'visitor can not see living individual');

        Auth::logout();
        self::assertTrue($queen_elizabeth->canShow(), 'guest can see living individual with RESN=none');
        self::assertFalse($prince_charles->canShow(), 'guest can not see living individual');

        // Relationship privacy
        Auth::login($member);
        self::assertTrue($beatrice->canShow());
        $tree->setUserPreference($member, User::PREF_TREE_ACCOUNT_XREF, $savannah->xref());
        $tree->setUserPreference($member, User::PREF_TREE_PATH_LENGTH, '3');
        self::assertFalse($beatrice->canShow());
        $tree->setUserPreference($member, User::PREF_TREE_PATH_LENGTH, '4');
        self::assertTrue($beatrice->canShow());
    }
}
