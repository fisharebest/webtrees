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
use Fisharebest\Webtrees\Exceptions\HttpAccessDeniedException;
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

        return $user->getPreference(User::PREF_IS_ADMINISTRATOR) === '1';
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

        return self::isAdmin($user) || $tree->getUserPreference($user, User::PREF_TREE_ROLE) === User::ROLE_MANAGER;
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

        return self::isManager($tree, $user) || $tree->getUserPreference($user, User::PREF_TREE_ROLE) === User::ROLE_MODERATOR;
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

        return self::isModerator($tree, $user) || $tree->getUserPreference($user, User::PREF_TREE_ROLE) === 'edit';
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

        return self::isEditor($tree, $user) || $tree->getUserPreference($user, User::PREF_TREE_ROLE) === 'access';
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
            throw new HttpAccessDeniedException();
        }
    }

    /**
     * @param Family|null $family
     * @param bool        $edit
     *
     * @return Family
     * @throws FamilyNotFoundException
     * @throws FamilyAccessDeniedException
     */
    public static function checkFamilyAccess(Family $family = null, bool $edit = false): Family
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

        return $family;
    }

    /**
     * @param Individual|null $individual
     * @param bool            $edit
     *
     * @return Individual
     * @throws IndividualNotFoundException
     * @throws IndividualAccessDeniedException
     */
    public static function checkIndividualAccess(Individual $individual = null, bool $edit = false): Individual
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

        return $individual;
    }

    /**
     * @param Media|null $media
     * @param bool       $edit
     *
     * @return Media
     * @throws MediaNotFoundException
     * @throws MediaAccessDeniedException
     */
    public static function checkMediaAccess(Media $media = null, bool $edit = false): Media
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

        return $media;
    }

    /**
     * @param Note|null $note
     * @param bool      $edit
     *
     * @return Note
     * @throws NoteNotFoundException
     * @throws NoteAccessDeniedException
     */
    public static function checkNoteAccess(Note $note = null, bool $edit = false): Note
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

        return $note;
    }

    /**
     * @param GedcomRecord|null $record
     * @param bool              $edit
     *
     * @return GedcomRecord
     * @throws RecordNotFoundException
     * @throws RecordAccessDeniedException
     */
    public static function checkRecordAccess(GedcomRecord $record = null, bool $edit = false): GedcomRecord
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

        return $record;
    }

    /**
     * @param Repository|null $repository
     * @param bool            $edit
     *
     * @return Repository
     * @throws RepositoryNotFoundException
     * @throws RepositoryAccessDeniedException
     */
    public static function checkRepositoryAccess(Repository $repository = null, bool $edit = false): Repository
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

        return $repository;
    }

    /**
     * @param Source|null $source
     * @param bool        $edit
     *
     * @return Source
     * @throws SourceNotFoundException
     * @throws SourceAccessDeniedException
     */
    public static function checkSourceAccess(Source $source = null, bool $edit = false): Source
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

        return $source;
    }
    
    /*
     * @param Submitter|null $submitter
     * @param bool           $edit
     *
     * @return Submitter
     * @throws RecordNotFoundException
     * @throws RecordAccessDeniedException
     */
    public static function checkSubmitterAccess(Submitter $submitter = null, bool $edit = false): Submitter
    {
        if ($submitter === null) {
            throw new RecordNotFoundException();
        }

        if (!$submitter->canShow()) {
            throw new RecordAccessDeniedException();
        }

        if ($edit && !$submitter->canEdit()) {
            throw new RecordAccessDeniedException();
        }

        return $submitter;
    }
}
