<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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

use Fisharebest\Webtrees\Contracts\CacheFactoryInterface;
use Fisharebest\Webtrees\Contracts\CalendarDateFactoryInterface;
use Fisharebest\Webtrees\Contracts\ElementFactoryInterface;
use Fisharebest\Webtrees\Contracts\EncodingFactoryInterface;
use Fisharebest\Webtrees\Contracts\FamilyFactoryInterface;
use Fisharebest\Webtrees\Contracts\FilesystemFactoryInterface;
use Fisharebest\Webtrees\Contracts\GedcomRecordFactoryInterface;
use Fisharebest\Webtrees\Contracts\HeaderFactoryInterface;
use Fisharebest\Webtrees\Contracts\IdFactoryInterface;
use Fisharebest\Webtrees\Contracts\ImageFactoryInterface;
use Fisharebest\Webtrees\Contracts\IndividualFactoryInterface;
use Fisharebest\Webtrees\Contracts\LocationFactoryInterface;
use Fisharebest\Webtrees\Contracts\MarkdownFactoryInterface;
use Fisharebest\Webtrees\Contracts\MediaFactoryInterface;
use Fisharebest\Webtrees\Contracts\NoteFactoryInterface;
use Fisharebest\Webtrees\Contracts\RepositoryFactoryInterface;
use Fisharebest\Webtrees\Contracts\ResponseFactoryInterface;
use Fisharebest\Webtrees\Contracts\RouteFactoryInterface;
use Fisharebest\Webtrees\Contracts\SharedNoteFactoryInterface;
use Fisharebest\Webtrees\Contracts\SlugFactoryInterface;
use Fisharebest\Webtrees\Contracts\SourceFactoryInterface;
use Fisharebest\Webtrees\Contracts\SubmissionFactoryInterface;
use Fisharebest\Webtrees\Contracts\SubmitterFactoryInterface;
use Fisharebest\Webtrees\Contracts\SurnameTraditionFactoryInterface;
use Fisharebest\Webtrees\Contracts\TimeFactoryInterface;
use Fisharebest\Webtrees\Contracts\TimestampFactoryInterface;
use Fisharebest\Webtrees\Contracts\XrefFactoryInterface;

/**
 * Provide access to factory objects and those that represent external entities (filesystems, caches)
 */
class Registry
{
    private static CacheFactoryInterface $cache_factory;

    private static CalendarDateFactoryInterface $calendar_date_factory;

    private static ElementFactoryInterface $element_factory;

    private static EncodingFactoryInterface $encoding_factory;

    private static FamilyFactoryInterface $family_factory;

    private static FilesystemFactoryInterface $filesystem_factory;

    private static GedcomRecordFactoryInterface $gedcom_record_factory;

    private static HeaderFactoryInterface $header_factory;

    private static IdFactoryInterface $id_factory;

    private static ImageFactoryInterface $image_factory;

    private static IndividualFactoryInterface $individual_factory;

    private static LocationFactoryInterface $location_factory;

    private static MarkdownFactoryInterface $markdown_factory;

    private static MediaFactoryInterface $media_factory;

    private static NoteFactoryInterface $note_factory;

    private static RepositoryFactoryInterface $repository_factory;

    private static ResponseFactoryInterface $response_factory;

    private static RouteFactoryInterface $route_factory;

    private static SharedNoteFactoryInterface $shared_note_factory;

    private static SlugFactoryInterface $slug_factory;

    private static SourceFactoryInterface $source_factory;

    private static SubmissionFactoryInterface $submission_factory;

    private static SubmitterFactoryInterface $submitter_factory;

    private static SurnameTraditionFactoryInterface $surname_tradition_factory;

    private static TimeFactoryInterface $time_factory;

    private static TimestampFactoryInterface $timestamp_factory;

    private static XrefFactoryInterface $xref_factory;

    /**
     * Store or retrieve a factory object.
     *
     * @param CacheFactoryInterface|null $factory
     *
     * @return CacheFactoryInterface
     */
    public static function cache(CacheFactoryInterface $factory = null): CacheFactoryInterface
    {
        if ($factory instanceof CacheFactoryInterface) {
            self::$cache_factory = $factory;
        }

        return self::$cache_factory;
    }

    /**
     * Store or retrieve a factory object.
     *
     * @param CalendarDateFactoryInterface|null $factory
     *
     * @return CalendarDateFactoryInterface
     */
    public static function calendarDateFactory(CalendarDateFactoryInterface $factory = null): CalendarDateFactoryInterface
    {
        if ($factory instanceof CalendarDateFactoryInterface) {
            self::$calendar_date_factory = $factory;
        }

        return self::$calendar_date_factory;
    }

    /**
     * Store or retrieve a factory object.
     *
     * @param ElementFactoryInterface|null $factory
     *
     * @return ElementFactoryInterface
     */
    public static function elementFactory(ElementFactoryInterface $factory = null): ElementFactoryInterface
    {
        if ($factory instanceof ElementFactoryInterface) {
            self::$element_factory = $factory;
        }

        return self::$element_factory;
    }

    /**
     * Store or retrieve a factory object.
     *
     * @param EncodingFactoryInterface|null $factory
     *
     * @return EncodingFactoryInterface
     */
    public static function encodingFactory(EncodingFactoryInterface $factory = null): EncodingFactoryInterface
    {
        if ($factory instanceof EncodingFactoryInterface) {
            self::$encoding_factory = $factory;
        }

        return self::$encoding_factory;
    }

    /**
     * Store or retrieve a factory object.
     *
     * @param FamilyFactoryInterface|null $factory
     *
     * @return FamilyFactoryInterface
     */
    public static function familyFactory(FamilyFactoryInterface $factory = null): FamilyFactoryInterface
    {
        if ($factory instanceof FamilyFactoryInterface) {
            self::$family_factory = $factory;
        }

        return self::$family_factory;
    }

    /**
     * Store or retrieve a factory object.
     *
     * @param FilesystemFactoryInterface|null $factory
     *
     * @return FilesystemFactoryInterface
     */
    public static function filesystem(FilesystemFactoryInterface $factory = null): FilesystemFactoryInterface
    {
        if ($factory instanceof FilesystemFactoryInterface) {
            self::$filesystem_factory = $factory;
        }

        return self::$filesystem_factory;
    }

    /**
     * Store or retrieve a factory object.
     *
     * @param GedcomRecordFactoryInterface|null $factory
     *
     * @return GedcomRecordFactoryInterface
     */
    public static function gedcomRecordFactory(GedcomRecordFactoryInterface $factory = null): GedcomRecordFactoryInterface
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
    public static function headerFactory(HeaderFactoryInterface $factory = null): HeaderFactoryInterface
    {
        if ($factory instanceof HeaderFactoryInterface) {
            self::$header_factory = $factory;
        }

        return self::$header_factory;
    }

    /**
     * Store or retrieve a factory object.
     *
     * @param IdFactoryInterface|null $factory
     *
     * @return IdFactoryInterface
     */
    public static function idFactory(IdFactoryInterface $factory = null): IdFactoryInterface
    {
        if ($factory instanceof IdFactoryInterface) {
            self::$id_factory = $factory;
        }

        return self::$id_factory;
    }

    /**
     * Store or retrieve a factory object.
     *
     * @param ImageFactoryInterface|null $factory
     *
     * @return ImageFactoryInterface
     */
    public static function imageFactory(ImageFactoryInterface $factory = null): ImageFactoryInterface
    {
        if ($factory instanceof ImageFactoryInterface) {
            self::$image_factory = $factory;
        }

        return self::$image_factory;
    }

    /**
     * Store or retrieve a factory object.
     *
     * @param IndividualFactoryInterface|null $factory
     *
     * @return IndividualFactoryInterface
     */
    public static function individualFactory(IndividualFactoryInterface $factory = null): IndividualFactoryInterface
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
    public static function locationFactory(LocationFactoryInterface $factory = null): LocationFactoryInterface
    {
        if ($factory instanceof LocationFactoryInterface) {
            self::$location_factory = $factory;
        }

        return self::$location_factory;
    }

    /**
     * Store or retrieve a factory object.
     *
     * @param MarkdownFactoryInterface|null $factory
     *
     * @return MarkdownFactoryInterface
     */
    public static function markdownFactory(MarkdownFactoryInterface $factory = null): MarkdownFactoryInterface
    {
        if ($factory instanceof MarkdownFactoryInterface) {
            self::$markdown_factory = $factory;
        }

        return self::$markdown_factory;
    }

    /**
     * Store or retrieve a factory object.
     *
     * @param MediaFactoryInterface|null $factory
     *
     * @return MediaFactoryInterface
     */
    public static function mediaFactory(MediaFactoryInterface $factory = null): MediaFactoryInterface
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
    public static function noteFactory(NoteFactoryInterface $factory = null): NoteFactoryInterface
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
    public static function repositoryFactory(RepositoryFactoryInterface $factory = null): RepositoryFactoryInterface
    {
        if ($factory instanceof RepositoryFactoryInterface) {
            self::$repository_factory = $factory;
        }

        return self::$repository_factory;
    }

    /**
     * Store or retrieve a factory object.
     *
     * @param ResponseFactoryInterface|null $factory
     *
     * @return ResponseFactoryInterface
     */
    public static function responseFactory(ResponseFactoryInterface $factory = null): ResponseFactoryInterface
    {
        if ($factory instanceof ResponseFactoryInterface) {
            self::$response_factory = $factory;
        }

        return self::$response_factory;
    }

    /**
     * Store or retrieve a factory object.
     *
     * @param RouteFactoryInterface|null $factory
     *
     * @return RouteFactoryInterface
     */
    public static function routeFactory(RouteFactoryInterface $factory = null): RouteFactoryInterface
    {
        if ($factory instanceof RouteFactoryInterface) {
            self::$route_factory = $factory;
        }

        return self::$route_factory;
    }

    /**
     * Store or retrieve a factory object.
     *
     * @param SharedNoteFactoryInterface|null $factory
     *
     * @return SharedNoteFactoryInterface
     */
    public static function sharedNoteFactory(SharedNoteFactoryInterface $factory = null): SharedNoteFactoryInterface
    {
        if ($factory instanceof SharedNoteFactoryInterface) {
            self::$shared_note_factory = $factory;
        }

        return self::$shared_note_factory;
    }

    /**
     * Store or retrieve a factory object.
     *
     * @param SlugFactoryInterface|null $factory
     *
     * @return SlugFactoryInterface
     */
    public static function slugFactory(SlugFactoryInterface $factory = null): SlugFactoryInterface
    {
        if ($factory instanceof SlugFactoryInterface) {
            self::$slug_factory = $factory;
        }

        return self::$slug_factory;
    }

    /**
     * Store or retrieve a factory object.
     *
     * @param SourceFactoryInterface|null $factory
     *
     * @return SourceFactoryInterface
     */
    public static function sourceFactory(SourceFactoryInterface $factory = null): SourceFactoryInterface
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
    public static function submissionFactory(SubmissionFactoryInterface $factory = null): SubmissionFactoryInterface
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
    public static function submitterFactory(SubmitterFactoryInterface $factory = null): SubmitterFactoryInterface
    {
        if ($factory instanceof SubmitterFactoryInterface) {
            self::$submitter_factory = $factory;
        }

        return self::$submitter_factory;
    }

    /**
     * Store or retrieve a factory object.
     *
     * @param SurnameTraditionFactoryInterface|null $factory
     *
     * @return SurnameTraditionFactoryInterface
     */
    public static function surnameTraditionFactory(SurnameTraditionFactoryInterface $factory = null): SurnameTraditionFactoryInterface
    {
        if ($factory instanceof SurnameTraditionFactoryInterface) {
            self::$surname_tradition_factory = $factory;
        }

        return self::$surname_tradition_factory;
    }

    /**
     * Store or retrieve a factory object.
     *
     * @param TimeFactoryInterface|null $factory
     *
     * @return TimeFactoryInterface
     */
    public static function timeFactory(TimeFactoryInterface $factory = null): TimeFactoryInterface
    {
        if ($factory instanceof TimeFactoryInterface) {
            self::$time_factory = $factory;
        }

        return self::$time_factory;
    }

    /**
     * Store or retrieve a factory object.
     *
     * @param TimestampFactoryInterface|null $factory
     *
     * @return TimestampFactoryInterface
     */
    public static function timestampFactory(TimestampFactoryInterface $factory = null): TimestampFactoryInterface
    {
        if ($factory instanceof TimestampFactoryInterface) {
            self::$timestamp_factory = $factory;
        }

        return self::$timestamp_factory;
    }

    /**
     * Store or retrieve a factory object.
     *
     * @param XrefFactoryInterface|null $factory
     *
     * @return XrefFactoryInterface
     */
    public static function xrefFactory(XrefFactoryInterface $factory = null): XrefFactoryInterface
    {
        if ($factory instanceof XrefFactoryInterface) {
            self::$xref_factory = $factory;
        }

        return self::$xref_factory;
    }
}
