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

namespace Fisharebest\Webtrees\Http\Routing;

use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Header;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Location;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\SharedNote;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Submission;
use Fisharebest\Webtrees\Submitter;
use Fisharebest\Webtrees\Tree;

/**
 * Resolves GEDCOM record parameters: serializes to XREF, deserializes from XREF.
 * Requires a Tree in the context to look up records.
 */
final readonly class GedcomRecordParameterResolver implements ParameterResolverInterface
{
    private const SUPPORTED_TYPES = [
        Individual::class,
        Family::class,
        Source::class,
        Repository::class,
        Media::class,
        Note::class,
        SharedNote::class,
        Location::class,
        Header::class,
        Submission::class,
        Submitter::class,
        GedcomRecord::class,
    ];

    public function supports(string $type_name): bool
    {
        return in_array($type_name, self::SUPPORTED_TYPES, true);
    }

    public function serialize(mixed $value): string|null
    {
        if ($value instanceof GedcomRecord) {
            return $value->xref();
        }

        return null;
    }

    /**
     * @param array<string, mixed> $context Expects 'tree' key containing a Tree instance.
     */
    public function deserialize(string $value, string $type_name, array $context = []): GedcomRecord|null
    {
        $tree = $context['tree'] ?? null;

        if (!$tree instanceof Tree) {
            return null;
        }

        return match ($type_name) {
            Individual::class   => Registry::individualFactory()->make($value, $tree),
            Family::class       => Registry::familyFactory()->make($value, $tree),
            Source::class       => Registry::sourceFactory()->make($value, $tree),
            Repository::class   => Registry::repositoryFactory()->make($value, $tree),
            Media::class        => Registry::mediaFactory()->make($value, $tree),
            Note::class         => Registry::noteFactory()->make($value, $tree),
            SharedNote::class   => Registry::sharedNoteFactory()->make($value, $tree),
            Location::class     => Registry::locationFactory()->make($value, $tree),
            Header::class       => Registry::headerFactory()->make($value, $tree),
            Submission::class   => Registry::submissionFactory()->make($value, $tree),
            Submitter::class    => Registry::submitterFactory()->make($value, $tree),
            GedcomRecord::class => Registry::gedcomRecordFactory()->make($value, $tree),
            default             => null,
        };
    }
}
