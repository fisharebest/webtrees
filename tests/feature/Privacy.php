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
        $this->assertInstanceOf(Individual::class, $queen_elizabeth);
        $this->assertSame('Queen Elizabeth II', strip_tags($queen_elizabeth->fullName()));

        $prince_charles = Registry::individualFactory()->make('X1052', $tree);
        $this->assertInstanceOf(Individual::class, $prince_charles);
        $this->assertSame('Charles, Prince of Wales', strip_tags($prince_charles->fullName()));

        $savannah = Registry::individualFactory()->make('X1044', $tree);
        $this->assertInstanceOf(Individual::class, $savannah);
        $this->assertSame('Savannah Anne Kathleen Phillips', strip_tags($savannah->fullName()));

        $beatrice = Registry::individualFactory()->make('X1047', $tree);
        $this->assertInstanceOf(Individual::class, $beatrice);
        $this->assertSame('Princess Beatrice of York', strip_tags($beatrice->fullName()));

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
        $this->assertTrue(Auth::isAdmin(), 'admin isAdmin()');
        $this->assertTrue(Auth::isManager($tree), 'admin isManager()');
        $this->assertTrue(Auth::isModerator($tree), 'admin isModerator()');
        $this->assertTrue(Auth::isEditor($tree), 'admin isEditor()');
        $this->assertTrue(Auth::isMember($tree), 'admin isMember()');

        Auth::login($manager);
        $this->assertFalse(Auth::isAdmin(), 'manager NOT isAdmin()');
        $this->assertTrue(Auth::isManager($tree, $manager), 'manager isManager()');
        $this->assertTrue(Auth::isModerator($tree, $manager), 'manager isModerator()');
        $this->assertTrue(Auth::isEditor($tree, $manager), 'manager isEditor()');
        $this->assertTrue(Auth::isMember($tree, $manager), 'manager isMember()');

        Auth::login($moderator);
        $this->assertFalse(Auth::isAdmin(), 'moderator NOT isAdmin()');
        $this->assertFalse(Auth::isManager($tree, $moderator), 'moderator NOT isManager()');
        $this->assertTrue(Auth::isModerator($tree, $moderator), 'moderator isModerator()');
        $this->assertTrue(Auth::isEditor($tree, $moderator), 'moderator isEditor()');
        $this->assertTrue(Auth::isMember($tree, $moderator), 'moderator isMember()');

        Auth::login($editor);
        $this->assertFalse(Auth::isAdmin(), 'editor NOT isAdmin()');
        $this->assertFalse(Auth::isManager($tree, $editor), 'editor NOT isManager()');
        $this->assertFalse(Auth::isModerator($tree, $editor), 'editor isModerator()');
        $this->assertTrue(Auth::isEditor($tree, $editor), 'editor isEditor()');
        $this->assertTrue(Auth::isMember($tree, $editor), 'editor isMember()');

        Auth::login($member);
        $this->assertFalse(Auth::isAdmin(), 'member NOT isAdmin()');
        $this->assertFalse(Auth::isManager($tree, $member), 'member NOT isManager()');
        $this->assertFalse(Auth::isModerator($tree, $member), 'member isModerator()');
        $this->assertFalse(Auth::isEditor($tree, $member), 'member isEditor()');
        $this->assertTrue(Auth::isMember($tree, $member), 'member isMember()');

        Auth::login($visitor);
        $this->assertFalse(Auth::isAdmin(), 'visitor NOT isAdmin()');
        $this->assertFalse(Auth::isManager($tree, $visitor), 'visitor NOT isManager()');
        $this->assertFalse(Auth::isModerator($tree, $visitor), 'visitor isModerator()');
        $this->assertFalse(Auth::isEditor($tree, $visitor), 'visitor isEditor()');
        $this->assertFalse(Auth::isMember($tree, $visitor), 'visitor isMember()');

        Auth::logout();



        Auth::login($admin);
        $this->assertTrue($queen_elizabeth->canShow(), 'admin can see living individual with RESN=none');
        $this->assertTrue($prince_charles->canShow(), 'admin can see living individual');

        Auth::login($manager);
        $this->assertTrue($queen_elizabeth->canShow(), 'manager can see living individual with RESN=none');
        $this->assertTrue($prince_charles->canShow(), 'manager can see living individual');

        Auth::login($moderator);
        $this->assertTrue($queen_elizabeth->canShow(), 'moderator can see living individual with RESN=none');
        $this->assertTrue($prince_charles->canShow(), 'moderator can see living individual');

        Auth::login($editor);
        $this->assertTrue($queen_elizabeth->canShow(), 'editor can see living individual with RESN=none');
        $this->assertTrue($prince_charles->canShow(), 'editor can see living individual');

        Auth::login($member);
        $this->assertTrue($queen_elizabeth->canShow(), 'member can see living individual with RESN=none');
        $this->assertTrue($prince_charles->canShow(), 'member can see living individual');

        Auth::login($visitor);
        $this->assertTrue($queen_elizabeth->canShow(), 'visitor can see living individual with RESN=none');
        $this->assertFalse($prince_charles->canShow(), 'visitor can not see living individual');

        Auth::logout();
        $this->assertTrue($queen_elizabeth->canShow(), 'guest can see living individual with RESN=none');
        $this->assertFalse($prince_charles->canShow(), 'guest can not see living individual');

        // Relationship privacy
        Auth::login($member);
        $this->assertTrue($beatrice->canShow());
        $tree->setUserPreference($member, User::PREF_TREE_ACCOUNT_XREF, $savannah->xref());
        $tree->setUserPreference($member, User::PREF_TREE_PATH_LENGTH, '3');
        $this->assertFalse($beatrice->canShow());
        $tree->setUserPreference($member, User::PREF_TREE_PATH_LENGTH, '4');
        $this->assertTrue($beatrice->canShow());
    }
}
