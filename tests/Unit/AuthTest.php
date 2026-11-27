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

namespace Fisharebest\Webtrees\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Header;
use Fisharebest\Webtrees\Http\Exceptions\HttpForbiddenException;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Location;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\SharedNote;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Submission;
use Fisharebest\Webtrees\Submitter;
use Fisharebest\Webtrees\Tests\TestCase;

#[CoversClass(Auth::class)]
class AuthTest extends TestCase
{
    /**
     * @return iterable<string,array{string,class-string<GedcomRecord>,string}>
     */
    public static function recordAccessChecks(): iterable
    {
        yield 'family' => ['checkFamilyAccess', Family::class, 'family'];
        yield 'header' => ['checkHeaderAccess', Header::class, 'record'];
        yield 'individual' => ['checkIndividualAccess', Individual::class, 'individual'];
        yield 'location' => ['checkLocationAccess', Location::class, 'record'];
        yield 'media' => ['checkMediaAccess', Media::class, 'media object'];
        yield 'note' => ['checkNoteAccess', Note::class, 'note'];
        yield 'shared note' => ['checkSharedNoteAccess', SharedNote::class, 'note'];
        yield 'record' => ['checkRecordAccess', GedcomRecord::class, 'record'];
        yield 'repository' => ['checkRepositoryAccess', Repository::class, 'repository'];
        yield 'source' => ['checkSourceAccess', Source::class, 'source'];
        yield 'submitter' => ['checkSubmitterAccess', Submitter::class, 'record'];
        yield 'submission' => ['checkSubmissionAccess', Submission::class, 'record'];
    }

    public function testClass(): void
    {
        self::assertTrue(class_exists(Auth::class));
    }

    #[DataProvider('recordAccessChecks')]
    public function testMissingRecordMessage(string $method, string $record_class, string $noun): void
    {
        try {
            Auth::$method(null);
            self::fail('Expected a not-found exception.');
        } catch (HttpNotFoundException $exception) {
            self::assertSame('This ' . $noun . ' does not exist.', $exception->getMessage());
        }
    }

    #[DataProvider('recordAccessChecks')]
    public function testPrivateRecordMessage(string $method, string $record_class, string $noun): void
    {
        $record = $this->createStub($record_class);
        $record->method('canShow')->willReturn(false);

        try {
            Auth::$method($record);
            self::fail('Expected a forbidden exception.');
        } catch (HttpForbiddenException $exception) {
            self::assertSame(
                'This ' . $noun . ' is private. You may need to sign in to see it.',
                $exception->getMessage()
            );
        }
    }

    #[DataProvider('recordAccessChecks')]
    public function testPrivateRecordMessageForSignedInUser(string $method, string $record_class, string $noun): void
    {
        Session::put('wt_user', 1);
        $record = $this->createStub($record_class);
        $record->method('canShow')->willReturn(false);

        try {
            Auth::$method($record);
            self::fail('Expected a forbidden exception.');
        } catch (HttpForbiddenException $exception) {
            self::assertSame('This ' . $noun . ' is private.', $exception->getMessage());
        }
    }
}
