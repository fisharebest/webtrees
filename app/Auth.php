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

use Fisharebest\Webtrees\Exceptions\FamilyAccessDeniedException;
use Fisharebest\Webtrees\Exceptions\FamilyNotFoundException;
use Fisharebest\Webtrees\Exceptions\IndividualAccessDeniedException;
use Fisharebest\Webtrees\Exceptions\IndividualNotFoundException;
use Fisharebest\Webtrees\Exceptions\MediaAccessDeniedException;
use Fisharebest\Webtrees\Exceptions\MediaNotFoundException;
use Fisharebest\Webtrees\Exceptions\NoteAccessDeniedException;
use Fisharebest\Webtrees\Exceptions\NoteNotFoundException;
use Fisharebest\Webtrees\Exceptions\RecordAccessDeniedException;
use Fisharebest\Webtrees\Exceptions\RecordNotFoundException;
use Fisharebest\Webtrees\Exceptions\RepositoryAccessDeniedException;
use Fisharebest\Webtrees\Exceptions\RepositoryNotFoundException;
use Fisharebest\Webtrees\Exceptions\SourceAccessDeniedException;
use Fisharebest\Webtrees\Exceptions\SourceNotFoundException;
use stdClass;

/**
 * Authentication.
 */
class Auth
{
    // Privacy constants
    public const PRIV_PRIVATE = 2; // Allows visitors to view the item
    public const PRIV_USER    = 1; // Allows members to access the item
    public const PRIV_NONE    = 0; // Allows managers to access the item
    public const PRIV_HIDE    = -1; // Hide the item to all users

    /**
     * Are we currently logged in?
     *
     * @return bool
     */
    public static function check(): bool
    {
        return self::id() !== null;
    }

    /**
     * Is the specified/current user an administrator?
     *
     * @param User|null $user
     *
     * @return bool
     */
    public static function isAdmin(User $user = null): bool
    {
        $user = $user ?? self::user();

        return $user->getPreference('canadmin') === '1';
    }

    /**
     * Is the specified/current user a manager of a tree?
     *
     * @param Tree      $tree
     * @param User|null $user
     *
     * @return bool
     */
    public static function isManager(Tree $tree, User $user = null): bool
    {
        $user = $user ?? self::user();

        return self::isAdmin($user) || $tree->getUserPreference($user, 'canedit') === 'admin';
    }

    /**
     * Is the specified/current user a moderator of a tree?
     *
     * @param Tree      $tree
     * @param User|null $user
     *
     * @return bool
     */
    public static function isModerator(Tree $tree, User $user = null): bool
    {
        $user = $user ?? self::user();

        return self::isManager($tree, $user) || $tree->getUserPreference($user, 'canedit') === 'accept';
    }

    /**
     * Is the specified/current user an editor of a tree?
     *
     * @param Tree      $tree
     * @param User|null $user
     *
     * @return bool
     */
    public static function isEditor(Tree $tree, User $user = null): bool
    {
        $user = $user ?? self::user();

        return self::isModerator($tree, $user) || $tree->getUserPreference($user, 'canedit') === 'edit';
    }

    /**
     * Is the specified/current user a member of a tree?
     *
     * @param Tree      $tree
     * @param User|null $user
     *
     * @return bool
     */
    public static function isMember(Tree $tree, User $user = null): bool
    {
        $user = $user ?? self::user();

        return self::isEditor($tree, $user) || $tree->getUserPreference($user, 'canedit') === 'access';
    }

    /**
     * What is the specified/current user's access level within a tree?
     *
     * @param Tree      $tree
     * @param User|null $user
     *
     * @return int
     */
    public static function accessLevel(Tree $tree, User $user = null)
    {
        $user = $user ?? self::user();

        if (self::isManager($tree, $user)) {
            return self::PRIV_NONE;
        }

        if (self::isMember($tree, $user)) {
            return self::PRIV_USER;
        }

        return self::PRIV_PRIVATE;
    }

    /**
     * The ID of the authenticated user, from the current session.
     *
     * @return int|null
     */
    public static function id()
    {
        return Session::get('wt_user');
    }

    /**
     * The authenticated user, from the current session.
     *
     * @return User
     */
    public static function user()
    {
        return User::find(self::id()) ?? User::visitor();
    }

    /**
     * Login directly as an explicit user - for masquerading.
     *
     * @param User $user
     *
     * @return void
     */
    public static function login(User $user)
    {
        Session::regenerate(false);
        Session::put('wt_user', $user->id());
    }

    /**
     * End the session for the current user.
     *
     * @return void
     */
    public static function logout()
    {
        Session::regenerate(true);
    }

    /**
     * @param Family|null $family
     * @param bool|null   $edit
     *
     * @return void
     * @throws FamilyNotFoundException
     * @throws FamilyAccessDeniedException
     */
    public static function checkFamilyAccess(Family $family = null, $edit = false)
    {
        if ($family === null) {
            throw new FamilyNotFoundException();
        }

        if (!$family->canShow() || $edit && (!$family->canEdit() || $family->isPendingDeletion())) {
            throw new FamilyAccessDeniedException();
        }
    }

    /**
     * @param Individual|null $individual
     * @param bool|null       $edit
     *
     * @return void
     * @throws IndividualNotFoundException
     * @throws IndividualAccessDeniedException
     */
    public static function checkIndividualAccess(Individual $individual = null, $edit = false)
    {
        if ($individual === null) {
            throw new IndividualNotFoundException();
        }

        if (!$individual->canShow() || $edit && (!$individual->canEdit() || $individual->isPendingDeletion())) {
            throw new IndividualAccessDeniedException();
        }
    }

    /**
     * @param Media|null $media
     * @param bool|null  $edit
     *
     * @return void
     * @throws MediaNotFoundException
     * @throws MediaAccessDeniedException
     */
    public static function checkMediaAccess(Media $media = null, $edit = false)
    {
        if ($media === null) {
            throw new MediaNotFoundException();
        }

        if (!$media->canShow() || $edit && (!$media->canEdit() || $media->isPendingDeletion())) {
            throw new MediaAccessDeniedException();
        }
    }

    /**
     * @param Note|null $note
     * @param bool|null $edit
     *
     * @return void
     * @throws NoteNotFoundException
     * @throws NoteAccessDeniedException
     */
    public static function checkNoteAccess(Note $note = null, $edit = false)
    {
        if ($note === null) {
            throw new NoteNotFoundException();
        }

        if (!$note->canShow() || $edit && (!$note->canEdit() || $note->isPendingDeletion())) {
            throw new NoteAccessDeniedException();
        }
    }

    /**
     * @param GedcomRecord|null $record
     * @param bool|null         $edit
     *
     * @return void
     * @throws RecordNotFoundException
     * @throws RecordAccessDeniedException
     */
    public static function checkRecordAccess(GedcomRecord $record = null, $edit = false)
    {
        if ($record === null) {
            throw new RecordNotFoundException();
        }

        if (!$record->canShow() || $edit && (!$record->canEdit() || $record->isPendingDeletion())) {
            throw new RecordAccessDeniedException();
        }
    }

    /**
     * @param Repository|null $repository
     * @param bool|null       $edit
     *
     * @return void
     * @throws RepositoryNotFoundException
     * @throws RepositoryAccessDeniedException
     */
    public static function checkRepositoryAccess(Repository $repository = null, $edit = false)
    {
        if ($repository === null) {
            throw new RepositoryNotFoundException();
        }

        if (!$repository->canShow() || $edit && (!$repository->canEdit() || $repository->isPendingDeletion())) {
            throw new RepositoryAccessDeniedException();
        }
    }

    /**
     * @param Source|null $source
     * @param bool|null   $edit
     *
     * @return void
     * @throws SourceNotFoundException
     * @throws SourceAccessDeniedException
     */
    public static function checkSourceAccess(Source $source = null, $edit = false)
    {
        if ($source === null) {
            throw new SourceNotFoundException();
        }

        if (!$source->canShow() || $edit && (!$source->canEdit() || $source->isPendingDeletion())) {
            throw new SourceAccessDeniedException();
        }
    }

}
