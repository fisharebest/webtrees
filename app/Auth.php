<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

        return $user->getPreference(UserInterface::PREF_IS_ADMINISTRATOR) === '1';
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

        return self::isAdmin($user) || $tree->getUserPreference($user, UserInterface::PREF_TREE_ROLE) === UserInterface::ROLE_MANAGER;
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

        return
            self::isManager($tree, $user) ||
            $tree->getUserPreference($user, UserInterface::PREF_TREE_ROLE) === UserInterface::ROLE_MODERATOR;
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

        return
            self::isModerator($tree, $user) ||
            $tree->getUserPreference($user, UserInterface::PREF_TREE_ROLE) === UserInterface::ROLE_EDITOR;
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

        return
            self::isEditor($tree, $user) ||
            $tree->getUserPreference($user, UserInterface::PREF_TREE_ROLE) === UserInterface::ROLE_MEMBER;
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
        return Session::get('wt_user');
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
     * @param string          $interface
     * @param Tree            $tree
     * @param UserInterface   $user
     *
     * @return void
     */
    public static function checkComponentAccess(ModuleInterface $module, string $interface, Tree $tree, UserInterface $user): void
    {
        if ($module->accessLevel($tree, $interface) < self::accessLevel($tree, $user)) {
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
    public static function checkFamilyAccess(?Family $family, bool $edit = false): Family
    {
        if ($family === null) {
            throw new FamilyNotFoundException();
        }

        if ($edit && $family->canEdit()) {
            $family->lock();

            return $family;
        }

        if ($family->canShow()) {
            return $family;
        }

        throw new FamilyAccessDeniedException();
    }

    /**
     * @param Header|null $header
     * @param bool        $edit
     *
     * @return Header
     * @throws RecordNotFoundException
     * @throws RecordAccessDeniedException
     */
    public static function checkHeaderAccess(?Header $header, bool $edit = false): Header
    {
        if ($header === null) {
            throw new RecordNotFoundException();
        }

        if ($edit && $header->canEdit()) {
            $header->lock();

            return $header;
        }

        if ($header->canShow()) {
            return $header;
        }

        throw new RecordAccessDeniedException();
    }

    /**
     * @param Individual|null $individual
     * @param bool            $edit
     * @param bool            $chart      For some charts, we can show private records
     *
     * @return Individual
     * @throws IndividualNotFoundException
     * @throws IndividualAccessDeniedException
     */
    public static function checkIndividualAccess(?Individual $individual, bool $edit = false, $chart = false): Individual
    {
        if ($individual === null) {
            throw new IndividualNotFoundException();
        }

        if ($edit && $individual->canEdit()) {
            $individual->lock();

            return $individual;
        }

        if ($chart && $individual->tree()->getPreference('SHOW_PRIVATE_RELATIONSHIPS') === '1') {
            return $individual;
        }

        if ($individual->canShow()) {
            return $individual;
        }

        throw new IndividualAccessDeniedException();
    }

    /**
     * @param Location|null $location
     * @param bool       $edit
     *
     * @return Location
     * @throws RecordNotFoundException
     * @throws RecordAccessDeniedException
     */
    public static function checkLocationAccess(?Location $location, bool $edit = false): Location
    {
        if ($location === null) {
            throw new RecordNotFoundException();
        }

        if ($edit && $location->canEdit()) {
            $location->lock();

            return $location;
        }

        if ($location->canShow()) {
            return $location;
        }

        throw new RecordAccessDeniedException();
    }

    /**
     * @param Media|null $media
     * @param bool       $edit
     *
     * @return Media
     * @throws MediaNotFoundException
     * @throws MediaAccessDeniedException
     */
    public static function checkMediaAccess(?Media $media, bool $edit = false): Media
    {
        if ($media === null) {
            throw new MediaNotFoundException();
        }

        if ($edit && $media->canEdit()) {
            $media->lock();

            return $media;
        }

        if ($media->canShow()) {
            return $media;
        }

        throw new MediaAccessDeniedException();
    }

    /**
     * @param Note|null $note
     * @param bool      $edit
     *
     * @return Note
     * @throws NoteNotFoundException
     * @throws NoteAccessDeniedException
     */
    public static function checkNoteAccess(?Note $note, bool $edit = false): Note
    {
        if ($note === null) {
            throw new NoteNotFoundException();
        }

        if ($edit && $note->canEdit()) {
            $note->lock();

            return $note;
        }

        if ($note->canShow()) {
            return $note;
        }

        throw new NoteAccessDeniedException();
    }

    /**
     * @param GedcomRecord|null $record
     * @param bool              $edit
     *
     * @return GedcomRecord
     * @throws RecordNotFoundException
     * @throws RecordAccessDeniedException
     */
    public static function checkRecordAccess(?GedcomRecord $record, bool $edit = false): GedcomRecord
    {
        if ($record === null) {
            throw new RecordNotFoundException();
        }

        if ($edit && $record->canEdit()) {
            $record->lock();

            return $record;
        }

        if ($record->canShow()) {
            return $record;
        }

        throw new RecordAccessDeniedException();
    }

    /**
     * @param Repository|null $repository
     * @param bool            $edit
     *
     * @return Repository
     * @throws RepositoryNotFoundException
     * @throws RepositoryAccessDeniedException
     */
    public static function checkRepositoryAccess(?Repository $repository, bool $edit = false): Repository
    {
        if ($repository === null) {
            throw new RepositoryNotFoundException();
        }

        if ($edit && $repository->canEdit()) {
            $repository->lock();

            return $repository;
        }

        if ($repository->canShow()) {
            return $repository;
        }

        throw new RepositoryAccessDeniedException();
    }

    /**
     * @param Source|null $source
     * @param bool        $edit
     *
     * @return Source
     * @throws SourceNotFoundException
     * @throws SourceAccessDeniedException
     */
    public static function checkSourceAccess(?Source $source, bool $edit = false): Source
    {
        if ($source === null) {
            throw new SourceNotFoundException();
        }

        if ($edit && $source->canEdit()) {
            $source->lock();

            return $source;
        }

        if ($source->canShow()) {
            return $source;
        }

        throw new SourceAccessDeniedException();
    }

    /*
     * @param Submitter|null $submitter
     * @param bool           $edit
     *
     * @return Submitter
     * @throws RecordNotFoundException
     * @throws RecordAccessDeniedException
     */
    public static function checkSubmitterAccess(?Submitter $submitter, bool $edit = false): Submitter
    {
        if ($submitter === null) {
            throw new RecordNotFoundException();
        }

        if ($edit && $submitter->canEdit()) {
            $submitter->lock();

            return $submitter;
        }

        if ($submitter->canShow()) {
            return $submitter;
        }

        throw new RecordAccessDeniedException();
    }

    /*
     * @param Submission|null $submission
     * @param bool            $edit
     *
     * @return Submission
     * @throws RecordNotFoundException
     * @throws RecordAccessDeniedException
     */
    public static function checkSubmissionAccess(?Submission $submission, bool $edit = false): Submission
    {
        if ($submission === null) {
            throw new RecordNotFoundException();
        }

        if ($edit && $submission->canEdit()) {
            $submission->lock();

            return $submission;
        }

        if ($submission->canShow()) {
            return $submission;
        }

        throw new RecordAccessDeniedException();
    }

    /**
     * @return array<int,string>
     */
    public static function accessLevelNames(): array
    {
        return [
            self::PRIV_PRIVATE => I18N::translate('Show to visitors'),
            self::PRIV_USER    => I18N::translate('Show to members'),
            self::PRIV_NONE    => I18N::translate('Show to managers'),
            self::PRIV_HIDE    => I18N::translate('Hide from everyone'),
        ];
    }

    /**
     * @return array<string,string>
     */
    public static function privacyRuleNames(): array
    {
        return [
            'none'         => I18N::translate('Show to visitors'),
            'privacy'      => I18N::translate('Show to members'),
            'confidential' => I18N::translate('Show to managers'),
            'hidden'       => I18N::translate('Hide from everyone'),
        ];
    }
}
