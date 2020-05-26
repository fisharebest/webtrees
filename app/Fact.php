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

use Closure;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\Services\GedcomService;
use Illuminate\Support\Collection;
use InvalidArgumentException;

use function array_flip;
use function array_key_exists;
use function count;
use function e;
use function implode;
use function in_array;
use function preg_match;
use function preg_match_all;
use function preg_replace;
use function strpos;
use function usort;

use const PREG_SET_ORDER;

/**
 * A GEDCOM fact or event object.
 */
class Fact
{
    private const FACT_ORDER = [
        'BIRT',
        '_HNM',
        'ALIA',
        '_AKA',
        '_AKAN',
        'ADOP',
        '_ADPF',
        '_ADPF',
        '_BRTM',
        'CHR',
        'BAPM',
        'FCOM',
        'CONF',
        'BARM',
        'BASM',
        'EDUC',
        'GRAD',
        '_DEG',
        'EMIG',
        'IMMI',
        'NATU',
        '_MILI',
        '_MILT',
        'ENGA',
        'MARB',
        'MARC',
        'MARL',
        '_MARI',
        '_MBON',
        'MARR',
        '_COML',
        '_STAT',
        '_SEPR',
        'DIVF',
        'MARS',
        'DIV',
        'ANUL',
        'CENS',
        'OCCU',
        'RESI',
        'PROP',
        'CHRA',
        'RETI',
        'FACT',
        'EVEN',
        '_NMR',
        '_NMAR',
        'NMR',
        'NCHI',
        'WILL',
        '_HOL',
        '_????_',
        'DEAT',
        '_FNRL',
        'CREM',
        'BURI',
        '_INTE',
        '_YART',
        '_NLIV',
        'PROB',
        'TITL',
        'COMM',
        'NATI',
        'CITN',
        'CAST',
        'RELI',
        'SSN',
        'IDNO',
        'TEMP',
        'SLGC',
        'BAPL',
        'CONL',
        'ENDL',
        'SLGS',
        'ADDR',
        'PHON',
        'EMAIL',
        '_EMAIL',
        'EMAL',
        'FAX',
        'WWW',
        'URL',
        '_URL',
        'AFN',
        'REFN',
        '_PRMN',
        'REF',
        'RIN',
        '_UID',
        'OBJE',
        'NOTE',
        'SOUR',
        'CHAN',
        '_TODO',
    ];

    /** @var string Unique identifier for this fact (currently implemented as a hash of the raw data). */
    private $id;

    /** @var GedcomRecord The GEDCOM record from which this fact is taken */
    private $record;

    /** @var string The raw GEDCOM data for this fact */
    private $gedcom;

    /** @var string The GEDCOM tag for this record */
    private $tag;

    /** @var bool Is this a recently deleted fact, pending approval? */
    private $pending_deletion = false;

    /** @var bool Is this a recently added fact, pending approval? */
    private $pending_addition = false;

    /** @var Date The date of this fact, from the “2 DATE …” attribute */
    private $date;

    /** @var Place The place of this fact, from the “2 PLAC …” attribute */
    private $place;

    /** @var int Used by Functions::sortFacts() */
    private $sortOrder;

    /**
     * Create an event object from a gedcom fragment.
     * We need the parent object (to check privacy) and a (pseudo) fact ID to
     * identify the fact within the record.
     *
     * @param string       $gedcom
     * @param GedcomRecord $parent
     * @param string       $id
     *
     * @throws InvalidArgumentException
     */
    public function __construct($gedcom, GedcomRecord $parent, $id)
    {
        if (preg_match('/^1 (' . Gedcom::REGEX_TAG . ')/', $gedcom, $match)) {
            $this->gedcom = $gedcom;
            $this->record = $parent;
            $this->id     = $id;
            $this->tag    = $match[1];
        } else {
            throw new InvalidArgumentException('Invalid GEDCOM data passed to Fact::_construct(' . $gedcom . ')');
        }
    }

    /**
     * Get the value of level 1 data in the fact
     * Allow for multi-line values
     *
     * @return string
     */
    public function value(): string
    {
        if (preg_match('/^1 (?:' . $this->tag . ') ?(.*(?:(?:\n2 CONT ?.*)*))/', $this->gedcom, $match)) {
            return preg_replace("/\n2 CONT ?/", "\n", $match[1]);
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
                return Factory::family()->make($xref, $this->record()->tree());
            case 'HUSB':
            case 'WIFE':
            case 'ALIA':
            case 'CHIL':
            case '_ASSO':
                return Factory::individual()->make($xref, $this->record()->tree());
            case 'ASSO':
                return
                    Factory::individual()->make($xref, $this->record()->tree()) ??
                    Factory::submitter()->make($xref, $this->record()->tree());
            case 'SOUR':
                return Factory::source()->make($xref, $this->record()->tree());
            case 'OBJE':
                return Factory::media()->make($xref, $this->record()->tree());
            case 'REPO':
                return Factory::repository()->make($xref, $this->record()->tree());
            case 'NOTE':
                return Factory::note()->make($xref, $this->record()->tree());
            case 'ANCI':
            case 'DESI':
            case 'SUBM':
                return Factory::submitter()->make($xref, $this->record()->tree());
            case 'SUBN':
                return Factory::submission()->make($xref, $this->record()->tree());
            case '_LOC':
                return Factory::location()->make($xref, $this->record()->tree());
            default:
                return Factory::gedcomRecord()->make($xref, $this->record()->tree());
        }
    }

    /**
     * Get the value of level 2 data in the fact
     *
     * @param string $tag
     *
     * @return string
     */
    public function attribute($tag): string
    {
        if (preg_match('/\n2 (?:' . $tag . ') ?(.*(?:(?:\n3 CONT ?.*)*)*)/', $this->gedcom, $match)) {
            return preg_replace("/\n3 CONT ?/", "\n", $match[1]);
        }

        return '';
    }

    /**
     * Get the PLAC:MAP:LATI for the fact.
     *
     * @return float
     */
    public function latitude(): float
    {
        if (preg_match('/\n4 LATI (.+)/', $this->gedcom, $match)) {
            $gedcom_service = new GedcomService();

            return $gedcom_service->readLatitude($match[1]);
        }

        return 0.0;
    }

    /**
     * Get the PLAC:MAP:LONG for the fact.
     *
     * @return float
     */
    public function longitude(): float
    {
        if (preg_match('/\n4 LONG (.+)/', $this->gedcom, $match)) {
            $gedcom_service = new GedcomService();

            return $gedcom_service->readLongitude($match[1]);
        }

        return 0.0;
    }

    /**
     * Do the privacy rules allow us to display this fact to the current user
     *
     * @param int|null $access_level
     *
     * @return bool
     */
    public function canShow(int $access_level = null): bool
    {
        $access_level = $access_level ?? Auth::accessLevel($this->record->tree());

        // Does this record have an explicit RESN?
        if (strpos($this->gedcom, "\n2 RESN confidential") !== false) {
            return Auth::PRIV_NONE >= $access_level;
        }
        if (strpos($this->gedcom, "\n2 RESN privacy") !== false) {
            return Auth::PRIV_USER >= $access_level;
        }
        if (strpos($this->gedcom, "\n2 RESN none") !== false) {
            return true;
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
     *
     * @return bool
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
        return Auth::isEditor($this->record->tree()) && strpos($this->gedcom, "\n2 RESN locked") === false && $this->tag() !== 'RESN' && $this->tag() !== 'CHAN';
    }

    /**
     * The place where the event occured.
     *
     * @return Place
     */
    public function place(): Place
    {
        if ($this->place === null) {
            $this->place = new Place($this->attribute('PLAC'), $this->record()->tree());
        }

        return $this->place;
    }

    /**
     * Get the date for this fact.
     * We can call this function many times, especially when sorting,
     * so keep a copy of the date.
     *
     * @return Date
     */
    public function date(): Date
    {
        if ($this->date === null) {
            $this->date = new Date($this->attribute('DATE'));
        }

        return $this->date;
    }

    /**
     * The raw GEDCOM data for this fact
     *
     * @return string
     */
    public function gedcom(): string
    {
        return $this->gedcom;
    }

    /**
     * Get a (pseudo) primary key for this fact.
     *
     * @return string
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * What is the tag (type) of this fact, such as BIRT, MARR or DEAT.
     *
     * @return string
     */
    public function tag(): string
    {
        return $this->tag;
    }

    /**
     * What is the tag (type) of this fact, such as BIRT, MARR or DEAT.
     *
     * @return string
     *
     * @deprecated since 2.0.5.  Will be removed in 2.1.0
     */
    public function getTag(): string
    {
        return $this->tag();
    }

    /**
     * Used to convert a real fact (e.g. BIRT) into a close-relative’s fact (e.g. _BIRT_CHIL)
     *
     * @param string $tag
     *
     * @return void
     *
     * @deprecated since 2.0.5.  Will be removed in 2.1.0
     */
    public function setTag($tag): void
    {
        $this->tag = $tag;
    }

    /**
     * The Person/Family record where this Fact came from
     *
     * @return Individual|Family|Source|Repository|Media|Note|Submitter|Submission|Location|Header|GedcomRecord
     */
    public function record()
    {
        return $this->record;
    }

    /**
     * Get the name of this fact type, for use as a label.
     *
     * @return string
     */
    public function label(): string
    {
        // Custom FACT/EVEN - with a TYPE
        if ($this->tag === 'FACT' || $this->tag === 'EVEN') {
            $type = $this->attribute('TYPE');

            if ($type !== '') {
                // Allow user-translations of custom types.
                $translated = I18N::translate($type);

                if ($translated !== $type) {
                    return $translated;
                }

                return e($type);
            }
        }

        return GedcomTag::getLabel($this->record::RECORD_TYPE . ':' . $this->tag);
    }

    /**
     * This is a newly deleted fact, pending approval.
     *
     * @return void
     */
    public function setPendingDeletion(): void
    {
        $this->pending_deletion = true;
        $this->pending_addition = false;
    }

    /**
     * Is this a newly deleted fact, pending approval.
     *
     * @return bool
     */
    public function isPendingDeletion(): bool
    {
        return $this->pending_deletion;
    }

    /**
     * This is a newly added fact, pending approval.
     *
     * @return void
     */
    public function setPendingAddition(): void
    {
        $this->pending_addition = true;
        $this->pending_deletion = false;
    }

    /**
     * Is this a newly added fact, pending approval.
     *
     * @return bool
     */
    public function isPendingAddition(): bool
    {
        return $this->pending_addition;
    }

    /**
     * Source citations linked to this fact
     *
     * @return string[]
     */
    public function getCitations(): array
    {
        preg_match_all('/\n(2 SOUR @(' . Gedcom::REGEX_XREF . ')@(?:\n[3-9] .*)*)/', $this->gedcom(), $matches, PREG_SET_ORDER);
        $citations = [];
        foreach ($matches as $match) {
            $source = Factory::source()->make($match[2], $this->record()->tree());
            if ($source && $source->canShow()) {
                $citations[] = $match[1];
            }
        }

        return $citations;
    }

    /**
     * Notes (inline and objects) linked to this fact
     *
     * @return string[]|Note[]
     */
    public function getNotes(): array
    {
        $notes = [];
        preg_match_all('/\n2 NOTE ?(.*(?:\n3.*)*)/', $this->gedcom(), $matches);
        foreach ($matches[1] as $match) {
            $note = preg_replace("/\n3 CONT ?/", "\n", $match);
            if (preg_match('/@(' . Gedcom::REGEX_XREF . ')@/', $note, $nmatch)) {
                $note = Factory::note()->make($nmatch[1], $this->record()->tree());
                if ($note && $note->canShow()) {
                    // A note object
                    $notes[] = $note;
                }
            } else {
                // An inline note
                $notes[] = $note;
            }
        }

        return $notes;
    }

    /**
     * Media objects linked to this fact
     *
     * @return Media[]
     */
    public function getMedia(): array
    {
        $media = [];
        preg_match_all('/\n2 OBJE @(' . Gedcom::REGEX_XREF . ')@/', $this->gedcom(), $matches);
        foreach ($matches[1] as $match) {
            $obje = Factory::media()->make($match, $this->record()->tree());
            if ($obje && $obje->canShow()) {
                $media[] = $obje;
            }
        }

        return $media;
    }

    /**
     * A one-line summary of the fact - for charts, etc.
     *
     * @return string
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
                $attributes[] = '<span dir="auto">' . e($value) . '</span>';
            }
            // Fact date
            $date = $this->date();
            if ($date->isOK()) {
                if ($this->record() instanceof Individual && in_array($this->tag(), Gedcom::BIRTH_EVENTS, true) && $this->record()->tree()->getPreference('SHOW_PARENTS_AGE')) {
                    $attributes[] = $date->display() . FunctionsPrint::formatParentsAges($this->record(), $date);
                } else {
                    $attributes[] = $date->display();
                }
            }
            // Fact place
            if ($this->place()->gedcomName() !== '') {
                $attributes[] = $this->place()->shortName();
            }
        }

        $class = 'fact_' . $this->tag();
        if ($this->isPendingAddition()) {
            $class .= ' wt-new';
        } elseif ($this->isPendingDeletion()) {
            $class .= ' wt-old';
        }

        return
            '<div class="' . $class . '">' .
            /* I18N: a label/value pair, such as “Occupation: Farmer”. Some languages may need to change the punctuation. */
            I18N::translate('<span class="label">%1$s:</span> <span class="field" dir="auto">%2$s</span>', $this->label(), implode(' — ', $attributes)) .
            '</div>';
    }

    /**
     * Helper functions to sort facts
     *
     * @return Closure
     */
    private static function dateComparator(): Closure
    {
        return static function (Fact $a, Fact $b): int {
            if ($a->date()->isOK() && $b->date()->isOK()) {
                // If both events have dates, compare by date
                $ret = Date::compare($a->date(), $b->date());

                if ($ret === 0) {
                    // If dates overlap, compare by fact type
                    $ret = self::typeComparator()($a, $b);

                    // If the fact type is also the same, retain the initial order
                    if ($ret === 0) {
                        $ret = $a->sortOrder <=> $b->sortOrder;
                    }
                }

                return $ret;
            }

            // One or both events have no date - retain the initial order
            return $a->sortOrder <=> $b->sortOrder;
        };
    }

    /**
     * Helper functions to sort facts.
     *
     * @return Closure
     */
    public static function typeComparator(): Closure
    {
        static $factsort = [];

        if ($factsort === []) {
            $factsort = array_flip(self::FACT_ORDER);
        }

        return static function (Fact $a, Fact $b) use ($factsort): int {
            // Facts from same families stay grouped together
            // Keep MARR and DIV from the same families from mixing with events from other FAMs
            // Use the original order in which the facts were added
            if ($a->record instanceof Family && $b->record instanceof Family && $a->record !== $b->record) {
                return $a->sortOrder - $b->sortOrder;
            }

            $atag = $a->tag();
            $btag = $b->tag();

            // Events not in the above list get mapped onto one that is.
            if (!array_key_exists($atag, $factsort)) {
                $atag = '_????_';
            }

            if (!array_key_exists($btag, $factsort)) {
                $btag = '_????_';
            }

            // - Don't let dated after DEAT/BURI facts sort non-dated facts before DEAT/BURI
            // - Treat dated after BURI facts as BURI instead
            if ($a->attribute('DATE') !== '' && $factsort[$atag] > $factsort['BURI'] && $factsort[$atag] < $factsort['CHAN']) {
                $atag = 'BURI';
            }

            if ($b->attribute('DATE') !== '' && $factsort[$btag] > $factsort['BURI'] && $factsort[$btag] < $factsort['CHAN']) {
                $btag = 'BURI';
            }

            $ret = $factsort[$atag] - $factsort[$btag];

            // If facts are the same then put dated facts before non-dated facts
            if ($ret == 0) {
                if ($a->attribute('DATE') !== '' && $b->attribute('DATE') === '') {
                    return -1;
                }

                if ($b->attribute('DATE') !== '' && $a->attribute('DATE') === '') {
                    return 1;
                }

                // If no sorting preference, then keep original ordering
                $ret = $a->sortOrder - $b->sortOrder;
            }

            return $ret;
        };
    }

    /**
     * A multi-key sort
     * 1. First divide the facts into two arrays one set with dates and one set without dates
     * 2. Sort each of the two new arrays, the date using the compare date function, the non-dated
     * using the compare type function
     * 3. Then merge the arrays back into the original array using the compare type function
     *
     * @param Collection<Fact> $unsorted
     *
     * @return Collection<Fact>
     */
    public static function sortFacts(Collection $unsorted): Collection
    {
        $dated    = [];
        $nondated = [];
        $sorted   = [];

        // Split the array into dated and non-dated arrays
        $order = 0;

        foreach ($unsorted as $fact) {
            $fact->sortOrder = $order;
            $order++;

            if ($fact->date()->isOK()) {
                $dated[] = $fact;
            } else {
                $nondated[] = $fact;
            }
        }

        usort($dated, self::dateComparator());
        usort($nondated, self::typeComparator());

        // Merge the arrays
        $dc = count($dated);
        $nc = count($nondated);
        $i  = 0;
        $j  = 0;

        // while there is anything in the dated array continue merging
        while ($i < $dc) {
            // compare each fact by type to merge them in order
            if ($j < $nc && self::typeComparator()($dated[$i], $nondated[$j]) > 0) {
                $sorted[] = $nondated[$j];
                $j++;
            } else {
                $sorted[] = $dated[$i];
                $i++;
            }
        }

        // get anything that might be left in the nondated array
        while ($j < $nc) {
            $sorted[] = $nondated[$j];
            $j++;
        }

        return new Collection($sorted);
    }

    /**
     * Sort fact/event tags using the same order that we use for facts.
     *
     * @param Collection<string> $unsorted
     *
     * @return Collection<string>
     */
    public static function sortFactTags(Collection $unsorted): Collection
    {
        $tag_order = array_flip(self::FACT_ORDER);

        return $unsorted->sort(static function (string $x, string $y) use ($tag_order): int {
            $sort_x = $tag_order[$x] ?? $tag_order['_????_'];
            $sort_y = $tag_order[$y] ?? $tag_order['_????_'];

            return $sort_x - $sort_y;
        });
    }

    /**
     * Allow native PHP functions such as array_unique() to work with objects
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->id . '@' . $this->record->xref();
    }
}
