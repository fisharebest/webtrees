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

use Fisharebest\Webtrees\Comparators\TagComparator;
use Fisharebest\Webtrees\Elements\RestrictionNotice;
use Fisharebest\Webtrees\Services\FactSortService;
use Fisharebest\Webtrees\Services\GedcomService;
use Illuminate\Support\Collection;
use InvalidArgumentException;

use function e;
use function implode;
use function in_array;
use function preg_match;
use function preg_replace;
use function str_contains;
use function str_ends_with;
use function str_starts_with;
use function trigger_error;

use const E_USER_DEPRECATED;

/**
 * A GEDCOM fact or event object.
 */
class Fact
{
    // Unique identifier for this fact (currently implemented as a hash of the raw data).
    private string $id;

    // The GEDCOM record from which this fact is taken
    private GedcomRecord $record;

    // The raw GEDCOM data for this fact
    private string $gedcom;

    // The GEDCOM tag for this record
    private string $tag;

    private bool $pending_deletion = false;

    private bool $pending_addition = false;

    private Date $date;

    private Place $place;

    // Used by anniversary calculations
    public int $jd;
    public int $anniv;

    /**
     * Create an event object from a gedcom fragment.
     * We need the parent object (to check privacy) and a (pseudo) fact ID to
     * identify the fact within the record.
     */
    public function __construct(string $gedcom, GedcomRecord $parent, string $id)
    {
        if (preg_match('/^1 (' . Gedcom::REGEX_TAG . ')/', $gedcom, $match)) {
            $this->gedcom = $gedcom;
            $this->record = $parent;
            $this->id     = $id;
            $this->tag    = $match[1];
        } else {
            throw new InvalidArgumentException('Invalid GEDCOM data passed to Fact::_construct(' . $gedcom . ',' . $parent->xref() . ')');
        }
    }

    /**
     * Get the value of level 1 data in the fact
     * Allow for multi-line values
     */
    public function value(): string
    {
        if (preg_match('/^1 ' . $this->tag . ' ?(.*(?:\n2 CONT ?.*)*)/', $this->gedcom, $match)) {
            $value = preg_replace("/\n2 CONT ?/", "\n", $match[1]);

            return Registry::elementFactory()->make($this->tag())->canonical($value);
        }

        return '';
    }

    /**
     * Get the record to which this fact links
     *
     * @return Family|GedcomRecord|Individual|Location|Media|Note|Repository|Source|Submission|Submitter|null
     */
    public function target()
    {
        if (!preg_match('/^@(' . Gedcom::REGEX_XREF . ')@$/', $this->value(), $match)) {
            return null;
        }

        $xref = $match[1];

        switch ($this->tag) {
            case 'FAMC':
            case 'FAMS':
                return Registry::familyFactory()->make($xref, $this->record->tree());
            case 'HUSB':
            case 'WIFE':
            case 'ALIA':
            case 'CHIL':
            case '_ASSO':
                return Registry::individualFactory()->make($xref, $this->record->tree());
            case 'ASSO':
                return
                    Registry::individualFactory()->make($xref, $this->record->tree()) ??
                    Registry::submitterFactory()->make($xref, $this->record->tree());
            case 'SOUR':
                return Registry::sourceFactory()->make($xref, $this->record->tree());
            case 'OBJE':
                return Registry::mediaFactory()->make($xref, $this->record->tree());
            case 'REPO':
                return Registry::repositoryFactory()->make($xref, $this->record->tree());
            case 'NOTE':
                return Registry::noteFactory()->make($xref, $this->record->tree());
            case 'ANCI':
            case 'DESI':
            case 'SUBM':
                return Registry::submitterFactory()->make($xref, $this->record->tree());
            case 'SUBN':
                return Registry::submissionFactory()->make($xref, $this->record->tree());
            case '_LOC':
                return Registry::locationFactory()->make($xref, $this->record->tree());
            default:
                return Registry::gedcomRecordFactory()->make($xref, $this->record->tree());
        }
    }

    /**
     * Get the value of level 2 data in the fact
     *
     *
     * @return string
     */
    public function attribute(string $tag): string
    {
        if (preg_match('/\n2 ' . $tag . '\b ?(.*(?:(?:\n3 CONT ?.*)*)*)/', $this->gedcom, $match)) {
            $value = preg_replace("/\n3 CONT ?/", "\n", $match[1]);

            return Registry::elementFactory()->make($this->tag() . ':' . $tag)->canonical($value);
        }

        return '';
    }

    /**
     * Get the PLAC:MAP:LATI for the fact.
     */
    public function latitude(): float|null
    {
        if (preg_match('/\n4 LATI (.+)/', $this->gedcom, $match)) {
            $gedcom_service = new GedcomService();

            return $gedcom_service->readLatitude($match[1]);
        }

        return null;
    }

    /**
     * Get the PLAC:MAP:LONG for the fact.
     */
    public function longitude(): float|null
    {
        if (preg_match('/\n4 LONG (.+)/', $this->gedcom, $match)) {
            $gedcom_service = new GedcomService();

            return $gedcom_service->readLongitude($match[1]);
        }

        return null;
    }

    /**
     * Do the privacy rules allow us to display this fact to the current user
     */
    public function canShow(int|null $access_level = null): bool
    {
        $access_level ??= Auth::accessLevel($this->record->tree());

        // Does this record have an explicit restriction notice?
        $element     = new RestrictionNotice('');
        $restriction = $element->canonical($this->attribute('RESN'));

        if (str_starts_with($restriction, RestrictionNotice::VALUE_CONFIDENTIAL)) {
            return Auth::PRIV_NONE >= $access_level;
        }

        if (str_starts_with($restriction, RestrictionNotice::VALUE_PRIVACY)) {
            return Auth::PRIV_USER >= $access_level;
        }
        if (str_starts_with($restriction, RestrictionNotice::VALUE_NONE)) {
            return true;
        }

        // A link to a record of the same type: NOTE=>NOTE, OBJE=>OBJE, SOUR=>SOUR, etc.
        // Use the privacy of the target record.
        $target = $this->target();

        if ($target instanceof GedcomRecord && $target->tag() === $this->tag) {
            return $target->canShow($access_level);
        }

        // Does this record have a default RESN?
        $xref                    = $this->record->xref();
        $fact_privacy            = $this->record->tree()->getFactPrivacy();
        $individual_fact_privacy = $this->record->tree()->getIndividualFactPrivacy();
        if (isset($individual_fact_privacy[$xref][$this->tag])) {
            return $individual_fact_privacy[$xref][$this->tag] >= $access_level;
        }
        if (isset($fact_privacy[$this->tag])) {
            return $fact_privacy[$this->tag] >= $access_level;
        }

        // No restrictions - it must be public
        return true;
    }

    /**
     * Check whether this fact is protected against edit
     */
    public function canEdit(): bool
    {
        if ($this->isPendingDeletion()) {
            return false;
        }

        if (Auth::isManager($this->record->tree())) {
            return true;
        }

        // Members cannot edit RESN, CHAN and locked records
        return Auth::isEditor($this->record->tree()) && !str_ends_with($this->attribute('RESN'), RestrictionNotice::VALUE_LOCKED) && $this->tag !== 'RESN' && $this->tag !== 'CHAN';
    }

    /**
     * The place where the event occurred.
     */
    public function place(): Place
    {
        $this->place ??= new Place($this->attribute('PLAC'), $this->record->tree());

        return $this->place;
    }

    /**
     * Get the date for this fact.
     * We can call this function many times, especially when sorting,
     * so keep a copy of the date.
     */
    public function date(): Date
    {
        $this->date ??= new Date($this->attribute('DATE'));

        return $this->date;
    }

    /**
     * The raw GEDCOM data for this fact
     */
    public function gedcom(): string
    {
        return $this->gedcom;
    }

    /**
     * Get a (pseudo) primary key for this fact.
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * What is the tag (type) of this fact, such as BIRT, MARR or DEAT.
     */
    public function tag(): string
    {
        return $this->record->tag() . ':' . $this->tag;
    }

    /**
     * The GEDCOM record where this Fact came from
     */
    public function record(): GedcomRecord
    {
        return $this->record;
    }

    /**
     * Get the name of this fact type, for use as a label.
     */
    public function label(): string
    {
        if (str_ends_with($this->tag(), ':NOTE') && preg_match('/^@' . Gedcom::REGEX_XREF . '@$/', $this->value())) {
            return I18N::translate('Shared note');
        }

        // Marriages
        if ($this->tag() === 'FAM:MARR') {
            $element = Registry::elementFactory()->make('FAM:MARR:TYPE');
            $type = $this->attribute('TYPE');

            if ($type !== '') {
                return $element->value($type, $this->record->tree());
            }
        }

        // Custom FACT/EVEN - with a TYPE
        if ($this->tag === 'FACT' || $this->tag === 'EVEN') {
            $type = $this->attribute('TYPE');

            if ($type !== '') {
                if (!str_contains($type, '%')) {
                    // Allow user-translations of custom types.
                    $translated = I18N::translate($type);

                    if ($translated !== $type) {
                        return $translated;
                    }
                }

                return e($type);
            }
        }

        return Registry::elementFactory()->make($this->tag())->label();
    }

    /**
     * This is a newly deleted fact, pending approval.
     */
    public function setPendingDeletion(): void
    {
        $this->pending_deletion = true;
        $this->pending_addition = false;
    }

    /**
     * Is this a newly deleted fact, pending approval.
     */
    public function isPendingDeletion(): bool
    {
        return $this->pending_deletion;
    }

    /**
     * This is a newly added fact, pending approval.
     */
    public function setPendingAddition(): void
    {
        $this->pending_addition = true;
        $this->pending_deletion = false;
    }

    /**
     * Is this a newly added fact, pending approval.
     */
    public function isPendingAddition(): bool
    {
        return $this->pending_addition;
    }

    /**
     * A one-line summary of the fact - for charts, etc.
     */
    public function summary(): string
    {
        $attributes = [];
        $target     = $this->target();
        if ($target instanceof GedcomRecord) {
            $attributes[] = $target->fullName();
        } else {
            // Fact value
            $value = $this->value();
            if ($value !== '' && $value !== 'Y') {
                $attributes[] = '<bdi>' . e($value) . '</bdi>';
            }
            // Fact date
            $date = $this->date();
            if ($date->isOK()) {
                if (
                    $this->record instanceof Individual && in_array($this->tag, Gedcom::BIRTH_EVENTS, true) &&
                    $this->record->tree()->getPreference('SHOW_PARENTS_AGE') === '1'
                ) {
                    $attributes[] = $date->display() . view('fact-parents-age', ['individual' => $this->record, 'birth_date' => $date]);
                } else {
                    $attributes[] = $date->display();
                }
            }
            // Fact place
            if ($this->place()->gedcomName() !== '') {
                $attributes[] = $this->place()->shortName();
            }
        }

        $class = 'fact_' . $this->tag;
        if ($this->isPendingAddition()) {
            $class .= ' wt-new';
        } elseif ($this->isPendingDeletion()) {
            $class .= ' wt-old';
        }

        $label = '<span class="label">' . $this->label() . '</span>';
        $value = '<span class="field" dir="auto">' . implode(' — ', $attributes) . '</span>';

        /* I18N: a label/value pair, such as “Occupation: Farmer”. Some languages may need to change the punctuation. */
        return '<div class="' . $class . '">' . I18N::translate('%1$s: %2$s', $label, $value) . '</div>';
    }

    /**
     * A one-line summary of the fact - for the clipboard, etc.
     */
    public function name(): string
    {
        $items  = [$this->label()];
        $target = $this->target();

        if ($target instanceof GedcomRecord) {
            $items[] = '<bdi>' . $target->fullName() . '</bdi>';
        } else {
            // Fact value
            $value = $this->value();
            if ($value !== '' && $value !== 'Y') {
                $items[] = '<bdi>' . e($value) . '</bdi>';
            }

            // Fact date
            if ($this->date()->isOK()) {
                $items[] = $this->date()->minimumDate()->format('%Y');
            }

            // Fact place
            if ($this->place()->gedcomName() !== '') {
                $items[] = $this->place()->shortName();
            }
        }

        return implode(' — ', $items);
    }

    /**
     * Sort a collection of facts.
     *
     * 1. Split facts into dated (have a parseable date) and nondated.
     * 2. Sort dated facts chronologically, using type order as tiebreaker.
     * 3. Group nondated facts: individual facts stay separate; family facts
     *    are grouped by family identity so they are inserted as a unit.
     * 4. Insert each family group near its family's dated facts, or before
     *    any later-input families' facts (preserving original family order).
     * 5. Insert individual nondated facts at their type-order position in the result.
     *
     * @param Collection<int,Fact> $unsorted
     *
     * @return Collection<int,Fact>
     */
    public static function sortFacts(Collection $unsorted): Collection
    {
        trigger_error(
            'Fact::sortFacts() is deprecated and will be removed in version 2.3. Use FactSortService::sort() instead.',
            E_USER_DEPRECATED
        );

        return (new FactSortService())->sort($unsorted);
    }

    /**
     * Sort fact/event tags using the same order that we use for facts.
     *
     * @param Collection<int,string> $unsorted
     *
     * @return Collection<int,string>
     */
    public static function sortFactTags(Collection $unsorted): Collection
    {
        trigger_error(
            'Fact::sortFactTags() is deprecated and will be removed in version 2.3. Use TagComparator::byOrder(...) instead.',
            E_USER_DEPRECATED
        );

        return $unsorted->sort(TagComparator::byOrder(...));
    }

    /**
     * Allow native PHP functions such as array_unique() to work with objects
     */
    public function __toString(): string
    {
        return $this->id . '@' . $this->record->xref();
    }
}
