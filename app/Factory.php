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
use Fisharebest\Webtrees\Contracts\XrefFactoryInterface;

/**
 * A service locator for our various factory objects.
 *
 * @deprecated - will be removed in 2.1.0 - use Registry instead.
 */
class Factory
{
    /**
     * Store or retrieve a factory object.
     *
     * @param FamilyFactoryInterface|null $factory
     *
     * @return FamilyFactoryInterface
     */
    public static function family(FamilyFactoryInterface $factory = null): FamilyFactoryInterface
    {
        return Registry::familyFactory($factory);
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
        return Registry::gedcomRecordFactory($factory);
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
        return Registry::headerFactory($factory);
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
        return Registry::individualFactory($factory);
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
        return Registry::locationFactory($factory);
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
        return Registry::mediaFactory($factory);
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
        return Registry::noteFactory($factory);
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
        return Registry::repositoryFactory($factory);
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
        return Registry::sourceFactory($factory);
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
        return Registry::submissionFactory($factory);
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
        return Registry::submitterFactory($factory);
    }

    /**
     * Store or retrieve a factory object.
     *
     * @param XrefFactoryInterface|null $factory
     *
     * @return XrefFactoryInterface
     */
    public static function xref(XrefFactoryInterface $factory = null): XrefFactoryInterface
    {
        return Registry::xrefFactory($factory);
    }
}
