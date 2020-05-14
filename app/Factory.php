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

use Fisharebest\Webtrees\Contracts\FamilyFactoryInterface;
use Fisharebest\Webtrees\Contracts\GedcomRecordFactoryInterface;
use Fisharebest\Webtrees\Contracts\HeaderFactoryInterface;
use Fisharebest\Webtrees\Contracts\IndividualFactoryInterface;
use Fisharebest\Webtrees\Contracts\LocationFactoryInterface;
use Fisharebest\Webtrees\Contracts\MediaFactoryInterface;
use Fisharebest\Webtrees\Contracts\NoteFactoryInterface;
use Fisharebest\Webtrees\Contracts\RepositoryFactoryInterface;
use Fisharebest\Webtrees\Contracts\SourceFactoryInterface;
use Fisharebest\Webtrees\Contracts\SubmissionFactoryInterface;
use Fisharebest\Webtrees\Contracts\SubmitterFactoryInterface;

/**
 * A service locator for our various factory objects.
 */
class Factory
{
    /** @var FamilyFactoryInterface */
    private static $family_factory;

    /** @var GedcomRecordFactoryInterface */
    private static $gedcom_record_factory;

    /** @var HeaderFactoryInterface */
    private static $header_factory;

    /** @var IndividualFactoryInterface */
    private static $individual_factory;

    /** @var LocationFactoryInterface */
    private static $location_factory;

    /** @var MediaFactoryInterface */
    private static $media_factory;

    /** @var NoteFactoryInterface */
    private static $note_factory;

    /** @var RepositoryFactoryInterface */
    private static $repository_factory;

    /** @var SourceFactoryInterface */
    private static $source_factory;

    /** @var SubmissionFactoryInterface */
    private static $submission_factory;

    /** @var SubmitterFactoryInterface */
    private static $submitter_factory;

    /**
     * Store or retrieve a factory object.
     *
     * @param FamilyFactoryInterface|null $factory
     *
     * @return FamilyFactoryInterface
     */
    public static function family(FamilyFactoryInterface $factory = null): FamilyFactoryInterface
    {
        if ($factory instanceof FamilyFactoryInterface) {
            self::$family_factory = $factory;
        }

        return self::$family_factory;
    }

    /**
     * Store or retrieve a factory object.
     *
     * @param GedcomRecordFactoryInterface|null $factory
     *
     * @return GedcomRecordFactoryInterface
     */
    public static function gedcomRecord(GedcomRecordFactoryInterface $factory = null): GedcomRecordFactoryInterface
    {
        if ($factory instanceof GedcomRecordFactoryInterface) {
            self::$gedcom_record_factory = $factory;
        }

        return self::$gedcom_record_factory;
    }

    /**
     * Store or retrieve a factory object.
     *
     * @param HeaderFactoryInterface|null $factory
     *
     * @return HeaderFactoryInterface
     */
    public static function header(HeaderFactoryInterface $factory = null): HeaderFactoryInterface
    {
        if ($factory instanceof HeaderFactoryInterface) {
            self::$header_factory = $factory;
        }

        return self::$header_factory;
    }

    /**
     * Store or retrieve a factory object.
     *
     * @param IndividualFactoryInterface|null $factory
     *
     * @return IndividualFactoryInterface
     */
    public static function individual(IndividualFactoryInterface $factory = null): IndividualFactoryInterface
    {
        if ($factory instanceof IndividualFactoryInterface) {
            self::$individual_factory = $factory;
        }

        return self::$individual_factory;
    }

    /**
     * Store or retrieve a factory object.
     *
     * @param LocationFactoryInterface|null $factory
     *
     * @return LocationFactoryInterface
     */
    public static function location(LocationFactoryInterface $factory = null): LocationFactoryInterface
    {
        if ($factory instanceof LocationFactoryInterface) {
            self::$location_factory = $factory;
        }

        return self::$location_factory;
    }

    /**
     * Store or retrieve a factory object.
     *
     * @param MediaFactoryInterface|null $factory
     *
     * @return MediaFactoryInterface
     */
    public static function media(MediaFactoryInterface $factory = null): MediaFactoryInterface
    {
        if ($factory instanceof MediaFactoryInterface) {
            self::$media_factory = $factory;
        }

        return self::$media_factory;
    }

    /**
     * Store or retrieve a factory object.
     *
     * @param NoteFactoryInterface|null $factory
     *
     * @return NoteFactoryInterface
     */
    public static function note(NoteFactoryInterface $factory = null): NoteFactoryInterface
    {
        if ($factory instanceof NoteFactoryInterface) {
            self::$note_factory = $factory;
        }

        return self::$note_factory;
    }

    /**
     * Store or retrieve a factory object.
     *
     * @param RepositoryFactoryInterface|null $factory
     *
     * @return RepositoryFactoryInterface
     */
    public static function repository(RepositoryFactoryInterface $factory = null): RepositoryFactoryInterface
    {
        if ($factory instanceof RepositoryFactoryInterface) {
            self::$repository_factory = $factory;
        }

        return self::$repository_factory;
    }

    /**
     * Store or retrieve a factory object.
     *
     * @param SourceFactoryInterface|null $factory
     *
     * @return SourceFactoryInterface
     */
    public static function source(SourceFactoryInterface $factory = null): SourceFactoryInterface
    {
        if ($factory instanceof SourceFactoryInterface) {
            self::$source_factory = $factory;
        }

        return self::$source_factory;
    }

    /**
     * Store or retrieve a factory object.
     *
     * @param SubmissionFactoryInterface|null $factory
     *
     * @return SubmissionFactoryInterface
     */
    public static function submission(SubmissionFactoryInterface $factory = null): SubmissionFactoryInterface
    {
        if ($factory instanceof SubmissionFactoryInterface) {
            self::$submission_factory = $factory;
        }

        return self::$submission_factory;
    }

    /**
     * Store or retrieve a factory object.
     *
     * @param SubmitterFactoryInterface|null $factory
     *
     * @return SubmitterFactoryInterface
     */
    public static function submitter(SubmitterFactoryInterface $factory = null): SubmitterFactoryInterface
    {
        if ($factory instanceof SubmitterFactoryInterface) {
            self::$submitter_factory = $factory;
        }

        return self::$submitter_factory;
    }
}
