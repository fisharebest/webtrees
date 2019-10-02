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

use Fisharebest\Webtrees\Contracts\UserInterface;
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
use Fisharebest\Webtrees\Module\ModuleInterface;
use Fisharebest\Webtrees\Services\UserService;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

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
     * @param UserInterface|null $user
     *
     * @return bool
     */
    public static function isAdmin(UserInterface $user = null): bool
    {
        $user = $user ?? self::user();

        return $user->getPreference('canadmin') === '1';
    }

    /**
     * Is the specified/current user a manager of a tree?
     *
     * @param Tree               $tree
     * @param UserInterface|null $user
     *
     * @return bool
     */
    public static function isManager(Tree $tree, UserInterface $user = null): bool
    {
        $user = $user ?? self::user();

        return self::isAdmin($user) || $tree->getUserPreference($user, 'canedit') === 'admin';
    }

    /**
     * Is the specified/current user a moderator of a tree?
     *
     * @param Tree               $tree
     * @param UserInterface|null $user
     *
     * @return bool
     */
    public static function isModerator(Tree $tree, UserInterface $user = null): bool
    {
        $user = $user ?? self::user();

        return self::isManager($tree, $user) || $tree->getUserPreference($user, 'canedit') === 'accept';
    }

    /**
     * Is the specified/current user an editor of a tree?
     *
     * @param Tree               $tree
     * @param UserInterface|null $user
     *
     * @return bool
     */
    public static function isEditor(Tree $tree, UserInterface $user = null): bool
    {
        $user = $user ?? self::user();

        return self::isModerator($tree, $user) || $tree->getUserPreference($user, 'canedit') === 'edit';
    }

    /**
     * Is the specified/current user a member of a tree?
     *
     * @param Tree               $tree
     * @param UserInterface|null $user
     *
     * @return bool
     */
    public static function isMember(Tree $tree, UserInterface $user = null): bool
    {
        $user = $user ?? self::user();

        return self::isEditor($tree, $user) || $tree->getUserPreference($user, 'canedit') === 'access';
    }

    /**
     * What is the specified/current user's access level within a tree?
     *
     * @param Tree               $tree
     * @param UserInterface|null $user
     *
     * @return int
     */
    public static function accessLevel(Tree $tree, UserInterface $user = null): int
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
    public static function id(): ?int
    {
        $id = Session::get('wt_user');

        if ($id !== null) {
            // In webtrees 1.x, the ID may have been a string.
            $id = (int) $id;
        }

        return $id;
    }

    /**
     * The authenticated user, from the current session.
     *
     * @return UserInterface
     */
    public static function user(): UserInterface
    {
        return app(UserService::class)->find(self::id()) ?? new GuestUser();
    }

    /**
     * Login directly as an explicit user - for masquerading.
     *
     * @param UserInterface $user
     *
     * @return void
     */
    public static function login(UserInterface $user): void
    {
        Session::regenerate(false);
        Session::put('wt_user', $user->id());
    }

    /**
     * End the session for the current user.
     *
     * @return void
     */
    public static function logout(): void
    {
        Session::regenerate(true);
    }

    /**
     * @param ModuleInterface $module
     * @param string          $component
     * @param Tree            $tree
     * @param UserInterface   $user
     *
     * @return void
     */
    public static function checkComponentAccess(ModuleInterface $module, string $component, Tree $tree, UserInterface $user): void
    {
        if ($module->accessLevel($tree, $component) < self::accessLevel($tree, $user)) {
            throw new AccessDeniedHttpException('Access denied');
        }
    }

    /**
     * @param Family|null $family
     * @param bool|null   $edit
     *
     * @return void
     * @throws FamilyNotFoundException
     * @throws FamilyAccessDeniedException
     */
    public static function checkFamilyAccess(Family $family = null, $edit = false): void
    {
        if ($family === null) {
            throw new FamilyNotFoundException();
        }

        if (!$family->canShow()) {
            throw new FamilyAccessDeniedException();
        }

        if ($edit && !$family->canEdit()) {
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
    public static function checkIndividualAccess(Individual $individual = null, $edit = false): void
    {
        if ($individual === null) {
            throw new IndividualNotFoundException();
        }

        if (!$individual->canShow()) {
            throw new IndividualAccessDeniedException();
        }

        if ($edit && !$individual->canEdit()) {
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
    public static function checkMediaAccess(Media $media = null, $edit = false): void
    {
        if ($media === null) {
            throw new MediaNotFoundException();
        }

        if (!$media->canShow()) {
            throw new MediaAccessDeniedException();
        }

        if ($edit && !$media->canEdit()) {
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
    public static function checkNoteAccess(Note $note = null, $edit = false): void
    {
        if ($note === null) {
            throw new NoteNotFoundException();
        }

        if (!$note->canShow()) {
            throw new NoteAccessDeniedException();
        }

        if ($edit && !$note->canEdit()) {
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
    public static function checkRecordAccess(GedcomRecord $record = null, $edit = false): void
    {
        if ($record === null) {
            throw new RecordNotFoundException();
        }

        if (!$record->canShow()) {
            throw new RecordAccessDeniedException();
        }

        if ($edit && !$record->canEdit()) {
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
    public static function checkRepositoryAccess(Repository $repository = null, $edit = false): void
    {
        if ($repository === null) {
            throw new RepositoryNotFoundException();
        }

        if (!$repository->canShow()) {
            throw new RepositoryAccessDeniedException();
        }

        if ($edit && !$repository->canEdit()) {
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
    public static function checkSourceAccess(Source $source = null, $edit = false): void
    {
        if ($source === null) {
            throw new SourceNotFoundException();
        }

        if (!$source->canShow()) {
            throw new SourceAccessDeniedException();
        }

        if ($edit && !$source->canEdit()) {
            throw new SourceAccessDeniedException();
        }
    }
}
