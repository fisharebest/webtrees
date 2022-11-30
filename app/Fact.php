<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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

use Closure;
use Fisharebest\Webtrees\Elements\RestrictionNotice;
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
use function preg_replace;
use function str_contains;
use function str_ends_with;
use function usort;

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
        'NO',
        'ADDR',
        'PHON',
        'EMAIL',
        '_EMAIL',
        'EMAL',
        'FAX',
        'WWW',
        'URL',
        '_URL',
        '_FSFTID',
        'AFN',
        'REFN',
        '_PRMN',
        'REF',
        'RIN',
        '_UID',
        'OBJE',
        'NOTE',
        'SOUR',
        'CREA',
        'CHAN',
        '_TODO',
    ];

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

    // Used to sort facts
    public int $sortOrder;

    // Used by anniversary calculations
    public int $jd;
    public int $anniv;

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
     *
     * @return string
     */
    public function value(): string
    {
        if (preg_match('/^1 ' . $this->tag . ' ?(.*(?:\n2 CONT ?.*)*)/', $this->gedcom, $match)) {
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
     * @param string $tag
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
     *
     * @return float|null
     */
    public function latitude(): ?float
    {
        if (preg_match('/\n4 LATI (.+)/', $this->gedcom, $match)) {
            $gedcom_service = new GedcomService();

            return $gedcom_service->readLatitude($match[1]);
        }

        return null;
    }

    /**
     * Get the PLAC:MAP:LONG for the fact.
     *
     * @return float|null
     */
    public function longitude(): ?float
    {
        if (preg_match('/\n4 LONG (.+)/', $this->gedcom, $match)) {
            $gedcom_service = new GedcomService();

            return $gedcom_service->readLongitude($match[1]);
        }

        return null;
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

        // Does this record have an explicit restriction notice?
        $restriction = $this->attribute('RESN');

        if (str_ends_with($restriction, RestrictionNotice::VALUE_CONFIDENTIAL)) {
            return Auth::PRIV_NONE >= $access_level;
        }

        if (str_ends_with($restriction, RestrictionNotice::VALUE_PRIVACY)) {
            return Auth::PRIV_USER >= $access_level;
        }
        if (str_ends_with($restriction, RestrictionNotice::VALUE_NONE)) {
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
        return Auth::isEditor($this->record->tree()) && !str_ends_with($this->attribute('RESN'), RestrictionNotice::VALUE_LOCKED) && $this->tag !== 'RESN' && $this->tag !== 'CHAN';
    }

    /**
     * The place where the event occured.
     *
     * @return Place
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
     *
     * @return Date
     */
    public function date(): Date
    {
        $this->date ??= new Date($this->attribute('DATE'));

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
        return $this->record->tag() . ':' . $this->tag;
    }

    /**
     * The GEDCOM record where this Fact came from
     *
     * @return GedcomRecord
     */
    public function record(): GedcomRecord
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
                $attributes[] = '<bdi>' . e($value) . '</bdi>';
            }
            // Fact date
            $date = $this->date();
            if ($date->isOK()) {
                if ($this->record instanceof Individual && in_array($this->tag, Gedcom::BIRTH_EVENTS, true) && $this->record->tree()->getPreference('SHOW_PARENTS_AGE')) {
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
     *
     * @return string
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
                return $a->sortOrder <=> $b->sortOrder;
            }

            $atag = $a->tag;
            $btag = $b->tag;

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

            // If facts are the same then put dated facts before non-dated facts
            if ($atag === $btag) {
                if ($a->attribute('DATE') !== '' && $b->attribute('DATE') === '') {
                    return -1;
                }

                if ($b->attribute('DATE') !== '' && $a->attribute('DATE') === '') {
                    return 1;
                }

                // If no sorting preference, then keep original ordering
                return $a->sortOrder <=> $b->sortOrder;
            }

            return $factsort[$atag] <=> $factsort[$btag];
        };
    }

    /**
     * A multi-key sort
     * 1. First divide the facts into two arrays one set with dates and one set without dates
     * 2. Sort each of the two new arrays, the date using the compare date function, the non-dated
     * using the compare type function
     * 3. Then merge the arrays back into the original array using the compare type function
     *
     * @param Collection<int,Fact> $unsorted
     *
     * @return Collection<int,Fact>
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
     * @param Collection<int,string> $unsorted
     *
     * @return Collection<int,string>
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
