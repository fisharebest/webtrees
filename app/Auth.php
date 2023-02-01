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

use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Http\Exceptions\HttpAccessDeniedException;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Module\ModuleInterface;
use Fisharebest\Webtrees\Services\UserService;

use function assert;
use function is_int;

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
        $user ??= self::user();

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
        $user ??= self::user();

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
        $user ??= self::user();

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
        $user ??= self::user();

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
        $user ??= self::user();

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
        $user ??= self::user();

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
        $wt_user = Session::get('wt_user');

        return is_int($wt_user) ? $wt_user : null;
    }

    /**
     * The authenticated user, from the current session.
     *
     * @return UserInterface
     */
    public static function user(): UserInterface
    {
        $user_service = app(UserService::class);
        assert($user_service instanceof UserService);

        return $user_service->find(self::id()) ?? new GuestUser();
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
        Session::regenerate();
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
     * @param class-string    $interface
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
     * @throws HttpNotFoundException
     * @throws HttpAccessDeniedException
     */
    public static function checkFamilyAccess(?Family $family, bool $edit = false): Family
    {
        $message = I18N::translate('This family does not exist or you do not have permission to view it.');

        if ($family === null) {
            throw new HttpNotFoundException($message);
        }

        if ($edit && $family->canEdit()) {
            $family->lock();

            return $family;
        }

        if ($family->canShow()) {
            return $family;
        }

        throw new HttpAccessDeniedException($message);
    }

    /**
     * @param Header|null $header
     * @param bool        $edit
     *
     * @return Header
     * @throws HttpNotFoundException
     * @throws HttpAccessDeniedException
     */
    public static function checkHeaderAccess(?Header $header, bool $edit = false): Header
    {
        $message = I18N::translate('This record does not exist or you do not have permission to view it.');

        if ($header === null) {
            throw new HttpNotFoundException($message);
        }

        if ($edit && $header->canEdit()) {
            $header->lock();

            return $header;
        }

        if ($header->canShow()) {
            return $header;
        }

        throw new HttpAccessDeniedException($message);
    }

    /**
     * @param Individual|null $individual
     * @param bool            $edit
     * @param bool            $chart For some charts, we can show private records
     *
     * @return Individual
     * @throws HttpNotFoundException
     * @throws HttpAccessDeniedException
     */
    public static function checkIndividualAccess(?Individual $individual, bool $edit = false, bool $chart = false): Individual
    {
        $message = I18N::translate('This individual does not exist or you do not have permission to view it.');

        if ($individual === null) {
            throw new HttpNotFoundException($message);
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

        throw new HttpAccessDeniedException($message);
    }

    /**
     * @param Location|null $location
     * @param bool          $edit
     *
     * @return Location
     * @throws HttpNotFoundException
     * @throws HttpAccessDeniedException
     */
    public static function checkLocationAccess(?Location $location, bool $edit = false): Location
    {
        $message = I18N::translate('This record does not exist or you do not have permission to view it.');

        if ($location === null) {
            throw new HttpNotFoundException($message);
        }

        if ($edit && $location->canEdit()) {
            $location->lock();

            return $location;
        }

        if ($location->canShow()) {
            return $location;
        }

        throw new HttpAccessDeniedException($message);
    }

    /**
     * @param Media|null $media
     * @param bool       $edit
     *
     * @return Media
     * @throws HttpNotFoundException
     * @throws HttpAccessDeniedException
     */
    public static function checkMediaAccess(?Media $media, bool $edit = false): Media
    {
        $message = I18N::translate('This media object does not exist or you do not have permission to view it.');

        if ($media === null) {
            throw new HttpNotFoundException($message);
        }

        if ($edit && $media->canEdit()) {
            $media->lock();

            return $media;
        }

        if ($media->canShow()) {
            return $media;
        }

        throw new HttpAccessDeniedException($message);
    }

    /**
     * @param Note|null $note
     * @param bool      $edit
     *
     * @return Note
     * @throws HttpNotFoundException
     * @throws HttpAccessDeniedException
     */
    public static function checkNoteAccess(?Note $note, bool $edit = false): Note
    {
        $message = I18N::translate('This note does not exist or you do not have permission to view it.');

        if ($note === null) {
            throw new HttpNotFoundException($message);
        }

        if ($edit && $note->canEdit()) {
            $note->lock();

            return $note;
        }

        if ($note->canShow()) {
            return $note;
        }

        throw new HttpAccessDeniedException($message);
    }

    /**
     * @param SharedNote|null $shared_note
     * @param bool            $edit
     *
     * @return SharedNote
     * @throws HttpNotFoundException
     * @throws HttpAccessDeniedException
     */
    public static function checkSharedNoteAccess(?SharedNote $shared_note, bool $edit = false): SharedNote
    {
        $message = I18N::translate('This note does not exist or you do not have permission to view it.');

        if ($shared_note === null) {
            throw new HttpNotFoundException($message);
        }

        if ($edit && $shared_note->canEdit()) {
            $shared_note->lock();

            return $shared_note;
        }

        if ($shared_note->canShow()) {
            return $shared_note;
        }

        throw new HttpAccessDeniedException($message);
    }

    /**
     * @param GedcomRecord|null $record
     * @param bool              $edit
     *
     * @return GedcomRecord
     * @throws HttpNotFoundException
     * @throws HttpAccessDeniedException
     */
    public static function checkRecordAccess(?GedcomRecord $record, bool $edit = false): GedcomRecord
    {
        $message = I18N::translate('This record does not exist or you do not have permission to view it.');

        if ($record === null) {
            throw new HttpNotFoundException($message);
        }

        if ($edit && $record->canEdit()) {
            $record->lock();

            return $record;
        }

        if ($record->canShow()) {
            return $record;
        }

        throw new HttpAccessDeniedException($message);
    }

    /**
     * @param Repository|null $repository
     * @param bool            $edit
     *
     * @return Repository
     * @throws HttpNotFoundException
     * @throws HttpAccessDeniedException
     */
    public static function checkRepositoryAccess(?Repository $repository, bool $edit = false): Repository
    {
        $message = I18N::translate('This repository does not exist or you do not have permission to view it.');

        if ($repository === null) {
            throw new HttpNotFoundException($message);
        }

        if ($edit && $repository->canEdit()) {
            $repository->lock();

            return $repository;
        }

        if ($repository->canShow()) {
            return $repository;
        }

        throw new HttpAccessDeniedException($message);
    }

    /**
     * @param Source|null $source
     * @param bool        $edit
     *
     * @return Source
     * @throws HttpNotFoundException
     * @throws HttpAccessDeniedException
     */
    public static function checkSourceAccess(?Source $source, bool $edit = false): Source
    {
        $message = I18N::translate('This source does not exist or you do not have permission to view it.');

        if ($source === null) {
            throw new HttpNotFoundException($message);
        }

        if ($edit && $source->canEdit()) {
            $source->lock();

            return $source;
        }

        if ($source->canShow()) {
            return $source;
        }

        throw new HttpAccessDeniedException($message);
    }

    /**
     * @param Submitter|null $submitter
     * @param bool           $edit
     *
     * @return Submitter
     * @throws HttpNotFoundException
     * @throws HttpAccessDeniedException
     */
    public static function checkSubmitterAccess(?Submitter $submitter, bool $edit = false): Submitter
    {
        $message = I18N::translate('This record does not exist or you do not have permission to view it.');

        if ($submitter === null) {
            throw new HttpNotFoundException($message);
        }

        if ($edit && $submitter->canEdit()) {
            $submitter->lock();

            return $submitter;
        }

        if ($submitter->canShow()) {
            return $submitter;
        }

        throw new HttpAccessDeniedException($message);
    }

    /**
     * @param Submission|null $submission
     * @param bool            $edit
     *
     * @return Submission
     * @throws HttpNotFoundException
     * @throws HttpAccessDeniedException
     */
    public static function checkSubmissionAccess(?Submission $submission, bool $edit = false): Submission
    {
        $message = I18N::translate('This record does not exist or you do not have permission to view it.');

        if ($submission === null) {
            throw new HttpNotFoundException($message);
        }

        if ($edit && $submission->canEdit()) {
            $submission->lock();

            return $submission;
        }

        if ($submission->canShow()) {
            return $submission;
        }

        throw new HttpAccessDeniedException($message);
    }

    /**
     * @param Tree          $tree
     * @param UserInterface $user
     *
     * @return bool
     */
    public static function canUploadMedia(Tree $tree, UserInterface $user): bool
    {
        return
            self::isEditor($tree, $user) &&
            self::accessLevel($tree, $user) <= (int) $tree->getPreference('MEDIA_UPLOAD');
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
