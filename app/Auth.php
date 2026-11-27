<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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
use Fisharebest\Webtrees\Enums\AccessLevel;
use Fisharebest\Webtrees\Enums\Role;
use Fisharebest\Webtrees\Http\Exceptions\HttpForbiddenException;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Module\ModuleInterface;
use Fisharebest\Webtrees\Services\UserService;

use function is_int;

/**
 * Authentication.
 */
class Auth
{
    /**
     * Are we currently logged in?
     */
    public static function check(): bool
    {
        return self::id() !== null;
    }

    /**
     * Is the specified/current user an administrator?
     */
    public static function isAdmin(UserInterface|null $user = null): bool
    {
        $user ??= self::user();

        return $user->getPreference(UserInterface::PREF_IS_ADMINISTRATOR) === '1';
    }

    /**
     * Is the specified/current user a manager of a tree?
     */
    public static function isManager(Tree $tree, UserInterface|null $user = null): bool
    {
        $user ??= self::user();

        return self::isAdmin($user) || $tree->getUserPreference($user, UserInterface::PREF_TREE_ROLE) === Role::Manager->value;
    }

    /**
     * Is the specified/current user a moderator of a tree?
     */
    public static function isModerator(Tree $tree, UserInterface|null $user = null): bool
    {
        $user ??= self::user();

        return
            self::isManager($tree, $user) ||
            $tree->getUserPreference($user, UserInterface::PREF_TREE_ROLE) === Role::Moderator->value;
    }

    /**
     * Is the specified/current user an editor of a tree?
     */
    public static function isEditor(Tree $tree, UserInterface|null $user = null): bool
    {
        $user ??= self::user();

        return
            self::isModerator($tree, $user) ||
            $tree->getUserPreference($user, UserInterface::PREF_TREE_ROLE) === Role::Editor->value;
    }

    /**
     * Is the specified/current user a member of a tree?
     */
    public static function isMember(Tree $tree, UserInterface|null $user = null): bool
    {
        $user ??= self::user();

        return
            self::isEditor($tree, $user) ||
            $tree->getUserPreference($user, UserInterface::PREF_TREE_ROLE) === Role::Member->value;
    }

    /**
     * What is the specified/current user's access level within a tree?
     */
    public static function accessLevel(Tree $tree, UserInterface|null $user = null): AccessLevel
    {
        $user ??= self::user();

        if (self::isManager($tree, $user)) {
            return AccessLevel::Manager;
        }

        if (self::isMember($tree, $user)) {
            return AccessLevel::Member;
        }

        return AccessLevel::Public;
    }

    /**
     * Should media be watermarked for the specified/current user in a tree?
     * The tree preference stores the most public access level that can view without a watermark.
     */
    public static function needsWatermark(Tree $tree, UserInterface|null $user = null): bool
    {
        $watermark_level = AccessLevel::fromTreePreference($tree, 'SHOW_NO_WATERMARK');

        return $watermark_level->disallows(self::accessLevel($tree, $user));
    }

    /**
     * The ID of the authenticated user, from the current session.
     */
    public static function id(): int|null
    {
        $wt_user = Session::get('wt_user');

        return is_int($wt_user) ? $wt_user : null;
    }

    /**
     * The authenticated user, from the current session.
     */
    public static function user(): UserInterface
    {
        $user_service = Registry::container()->get(UserService::class);

        return $user_service->find(self::id()) ?? new GuestUser();
    }

    /**
     * Login directly as an explicit user - for masquerading.
     */
    public static function login(UserInterface $user): void
    {
        Session::regenerate();
        Session::put('wt_user', $user->id());
    }

    /**
     * End the session for the current user.
     */
    public static function logout(): void
    {
        Session::regenerate(true);
    }

    /**
     * @template T of ModuleInterface
     *
     * @param class-string<T> $interface
     */
    public static function checkComponentAccess(ModuleInterface $module, string $interface, Tree $tree, UserInterface $user): void
    {
        if ($module->accessLevel($tree, $interface)->disallows(self::accessLevel($tree, $user))) {
            throw new HttpForbiddenException();
        }
    }

    public static function checkFamilyAccess(Family|null $family, bool $edit = false): Family
    {
        if ($family === null) {
            throw new HttpNotFoundException(I18N::translate('This family does not exist.'));
        }

        if ($edit && $family->canEdit()) {
            $family->lock();

            return $family;
        }

        if ($family->canShow()) {
            return $family;
        }

        throw new HttpForbiddenException(self::privateRecordMessage(I18N::translate('This family is private.')));
    }

    public static function checkHeaderAccess(Header|null $header, bool $edit = false): Header
    {
        if ($header === null) {
            throw new HttpNotFoundException(I18N::translate('This record does not exist.'));
        }

        if ($edit && $header->canEdit()) {
            $header->lock();

            return $header;
        }

        if ($header->canShow()) {
            return $header;
        }

        throw new HttpForbiddenException(self::privateRecordMessage(I18N::translate('This record is private.')));
    }

    /**
     * @param bool            $chart For some charts, we can show private records
     */
    public static function checkIndividualAccess(Individual|null $individual, bool $edit = false, bool $chart = false): Individual
    {
        if ($individual === null) {
            throw new HttpNotFoundException(I18N::translate('This individual does not exist.'));
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

        throw new HttpForbiddenException(self::privateRecordMessage(I18N::translate('This individual is private.')));
    }

    public static function checkLocationAccess(Location|null $location, bool $edit = false): Location
    {
        if ($location === null) {
            throw new HttpNotFoundException(I18N::translate('This record does not exist.'));
        }

        if ($edit && $location->canEdit()) {
            $location->lock();

            return $location;
        }

        if ($location->canShow()) {
            return $location;
        }

        throw new HttpForbiddenException(self::privateRecordMessage(I18N::translate('This record is private.')));
    }

    public static function checkMediaAccess(Media|null $media, bool $edit = false): Media
    {
        if ($media === null) {
            throw new HttpNotFoundException(I18N::translate('This media object does not exist.'));
        }

        if ($edit && $media->canEdit()) {
            $media->lock();

            return $media;
        }

        if ($media->canShow()) {
            return $media;
        }

        throw new HttpForbiddenException(self::privateRecordMessage(I18N::translate('This media object is private.')));
    }

    public static function checkNoteAccess(Note|null $note, bool $edit = false): Note
    {
        if ($note === null) {
            throw new HttpNotFoundException(I18N::translate('This note does not exist.'));
        }

        if ($edit && $note->canEdit()) {
            $note->lock();

            return $note;
        }

        if ($note->canShow()) {
            return $note;
        }

        throw new HttpForbiddenException(self::privateRecordMessage(I18N::translate('This note is private.')));
    }

    public static function checkSharedNoteAccess(SharedNote|null $shared_note, bool $edit = false): SharedNote
    {
        if ($shared_note === null) {
            throw new HttpNotFoundException(I18N::translate('This note does not exist.'));
        }

        if ($edit && $shared_note->canEdit()) {
            $shared_note->lock();

            return $shared_note;
        }

        if ($shared_note->canShow()) {
            return $shared_note;
        }

        throw new HttpForbiddenException(self::privateRecordMessage(I18N::translate('This note is private.')));
    }

    public static function checkRecordAccess(GedcomRecord|null $record, bool $edit = false): GedcomRecord
    {
        if ($record === null) {
            throw new HttpNotFoundException(I18N::translate('This record does not exist.'));
        }

        if ($edit && $record->canEdit()) {
            $record->lock();

            return $record;
        }

        if ($record->canShow()) {
            return $record;
        }

        throw new HttpForbiddenException(self::privateRecordMessage(I18N::translate('This record is private.')));
    }

    public static function checkRepositoryAccess(Repository|null $repository, bool $edit = false): Repository
    {
        if ($repository === null) {
            throw new HttpNotFoundException(I18N::translate('This repository does not exist.'));
        }

        if ($edit && $repository->canEdit()) {
            $repository->lock();

            return $repository;
        }

        if ($repository->canShow()) {
            return $repository;
        }

        throw new HttpForbiddenException(self::privateRecordMessage(I18N::translate('This repository is private.')));
    }

    public static function checkSourceAccess(Source|null $source, bool $edit = false): Source
    {
        if ($source === null) {
            throw new HttpNotFoundException(I18N::translate('This source does not exist.'));
        }

        if ($edit && $source->canEdit()) {
            $source->lock();

            return $source;
        }

        if ($source->canShow()) {
            return $source;
        }

        throw new HttpForbiddenException(self::privateRecordMessage(I18N::translate('This source is private.')));
    }

    public static function checkSubmitterAccess(Submitter|null $submitter, bool $edit = false): Submitter
    {
        if ($submitter === null) {
            throw new HttpNotFoundException(I18N::translate('This record does not exist.'));
        }

        if ($edit && $submitter->canEdit()) {
            $submitter->lock();

            return $submitter;
        }

        if ($submitter->canShow()) {
            return $submitter;
        }

        throw new HttpForbiddenException(self::privateRecordMessage(I18N::translate('This record is private.')));
    }

    public static function checkSubmissionAccess(Submission|null $submission, bool $edit = false): Submission
    {
        if ($submission === null) {
            throw new HttpNotFoundException(I18N::translate('This record does not exist.'));
        }

        if ($edit && $submission->canEdit()) {
            $submission->lock();

            return $submission;
        }

        if ($submission->canShow()) {
            return $submission;
        }

        throw new HttpForbiddenException(self::privateRecordMessage(I18N::translate('This record is private.')));
    }

    public static function canUploadMedia(Tree $tree, UserInterface $user): bool
    {
        $upload_level = AccessLevel::fromTreePreference($tree, 'MEDIA_UPLOAD');

        return
            self::isEditor($tree, $user) &&
            $upload_level->allows(self::accessLevel($tree, $user));
    }

    /**
     * @return array<int,string>
     */
    public static function accessLevelNames(): array
    {
        $names = [];

        foreach (AccessLevel::cases() as $level) {
            $names[$level->value] = $level->label();
        }

        return $names;
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

    private static function privateRecordMessage(string $message): string
    {
        if (self::check()) {
            return $message;
        }

        return $message . ' ' . I18N::translate('You may need to sign in to see it.');
    }
}
