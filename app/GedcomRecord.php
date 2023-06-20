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

use Closure;
use Exception;
use Fisharebest\Webtrees\Contracts\TimestampInterface;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Elements\RestrictionNotice;
use Fisharebest\Webtrees\Http\RequestHandlers\GedcomRecordPage;
use Fisharebest\Webtrees\Services\PendingChangesService;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;

use function array_combine;
use function array_keys;
use function array_map;
use function array_search;
use function array_shift;
use function count;
use function date;
use function e;
use function explode;
use function implode;
use function in_array;
use function md5;
use function preg_match;
use function preg_match_all;
use function preg_replace;
use function preg_replace_callback;
use function preg_split;
use function range;
use function route;
use function str_contains;
use function str_ends_with;
use function str_pad;
use function str_starts_with;
use function strtoupper;
use function strtr;
use function trim;
use function view;

use const PHP_INT_MAX;
use const PREG_SET_ORDER;
use const STR_PAD_LEFT;

/**
 * A GEDCOM object.
 */
class GedcomRecord
{
    public const RECORD_TYPE = 'UNKNOWN';

    protected const ROUTE_NAME = GedcomRecordPage::class;

    protected string $xref;

    protected Tree $tree;

    // GEDCOM data (before any pending edits)
    protected string $gedcom;

    // GEDCOM data (after any pending edits)
    protected ?string $pending;

    /** @var array<Fact> Facts extracted from $gedcom/$pending */
    protected array $facts;

    /** @var array<array<string>> All the names of this individual */
    protected array $getAllNames = [];

    /** @var int|null Cached result */
    private ?int $getPrimaryName = null;

    /** @var int|null Cached result */
    private ?int $getSecondaryName = null;

    /**
     * Create a GedcomRecord object from raw GEDCOM data.
     *
     * @param string      $xref
     * @param string      $gedcom  an empty string for new/pending records
     * @param string|null $pending null for a record with no pending edits,
     *                             empty string for records with pending deletions
     * @param Tree        $tree
     */
    public function __construct(string $xref, string $gedcom, ?string $pending, Tree $tree)
    {
        $this->xref    = $xref;
        $this->gedcom  = $gedcom;
        $this->pending = $pending;
        $this->tree    = $tree;
        $this->facts   = $this->parseFacts();
    }

    /**
     * A closure which will filter out private records.
     *
     * @return Closure(GedcomRecord):bool
     */
    public static function accessFilter(): Closure
    {
        return static function (GedcomRecord $record): bool {
            return $record->canShow();
        };
    }

    /**
     * A closure which will compare records by name.
     *
     * @return Closure(GedcomRecord,GedcomRecord):int
     */
    public static function nameComparator(): Closure
    {
        return static function (GedcomRecord $x, GedcomRecord $y): int {
            if ($x->canShowName()) {
                if ($y->canShowName()) {
                    return I18N::comparator()($x->sortName(), $y->sortName());
                }

                return -1; // only $y is private
            }

            if ($y->canShowName()) {
                return 1; // only $x is private
            }

            return 0; // both $x and $y private
        };
    }

    /**
     * A closure which will compare records by change time.
     *
     * @param int $direction +1 to sort ascending, -1 to sort descending
     *
     * @return Closure(GedcomRecord,GedcomRecord):int
     */
    public static function lastChangeComparator(int $direction = 1): Closure
    {
        return static function (GedcomRecord $x, GedcomRecord $y) use ($direction): int {
            return $direction * ($x->lastChangeTimestamp() <=> $y->lastChangeTimestamp());
        };
    }

    /**
     * Get the GEDCOM tag for this record.
     *
     * @return string
     */
    public function tag(): string
    {
        preg_match('/^0 @[^@]*@ (\w+)/', $this->gedcom(), $match);

        return $match[1] ?? static::RECORD_TYPE;
    }

    /**
     * Get the XREF for this record
     *
     * @return string
     */
    public function xref(): string
    {
        return $this->xref;
    }

    /**
     * Get the tree to which this record belongs
     *
     * @return Tree
     */
    public function tree(): Tree
    {
        return $this->tree;
    }

    /**
     * Application code should access data via Fact objects.
     * This function exists to support old code.
     *
     * @return string
     */
    public function gedcom(): string
    {
        return $this->pending ?? $this->gedcom;
    }

    /**
     * Does this record have a pending change?
     *
     * @return bool
     */
    public function isPendingAddition(): bool
    {
        return $this->pending !== null;
    }

    /**
     * Does this record have a pending deletion?
     *
     * @return bool
     */
    public function isPendingDeletion(): bool
    {
        return $this->pending === '';
    }

    /**
     * Generate a URL to this record.
     *
     * @return string
     */
    public function url(): string
    {
        return route(static::ROUTE_NAME, [
            'xref' => $this->xref(),
            'tree' => $this->tree->name(),
            'slug' => Registry::slugFactory()->make($this),
        ]);
    }

    /**
     * Can the details of this record be shown?
     *
     * @param int|null $access_level
     *
     * @return bool
     */
    public function canShow(int $access_level = null): bool
    {
        $access_level ??= Auth::accessLevel($this->tree);

        // We use this value to bypass privacy checks. For example,
        // when downloading data or when calculating privacy itself.
        if ($access_level === Auth::PRIV_HIDE) {
            return true;
        }

        $cache_key = 'show-' . $this->xref . '-' . $this->tree->id() . '-' . $access_level;

        return Registry::cache()->array()->remember($cache_key, function () use ($access_level) {
            return $this->canShowRecord($access_level);
        });
    }

    /**
     * Can the name of this record be shown?
     *
     * @param int|null $access_level
     *
     * @return bool
     */
    public function canShowName(int $access_level = null): bool
    {
        return $this->canShow($access_level);
    }

    /**
     * Can we edit this record?
     *
     * @return bool
     */
    public function canEdit(): bool
    {
        if ($this->isPendingDeletion()) {
            return false;
        }

        if (Auth::isManager($this->tree)) {
            return true;
        }

        $fact   = $this->facts(['RESN'])->first();
        $locked = $fact instanceof Fact && str_ends_with($fact->attribute('RESN'), RestrictionNotice::VALUE_LOCKED);

        return Auth::isEditor($this->tree) && !$locked;
    }

    /**
     * Remove private data from the raw gedcom record.
     * Return both the visible and invisible data. We need the invisible data when editing.
     *
     * @param int $access_level
     *
     * @return string
     */
    public function privatizeGedcom(int $access_level): string
    {
        if ($access_level === Auth::PRIV_HIDE) {
            // We may need the original record, for example when downloading a GEDCOM or clippings cart
            return $this->gedcom;
        }

        if ($this->canShow($access_level)) {
            // The record is not private, but the individual facts may be.

            // Include the entire first line (for NOTE records)
            [$gedrec] = explode("\n", $this->gedcom . $this->pending, 2);

            // Check each of the facts for access
            foreach ($this->facts([], false, $access_level) as $fact) {
                $gedrec .= "\n" . $fact->gedcom();
            }

            return $gedrec;
        }

        // We cannot display the details, but we may be able to display
        // limited data, such as links to other records.
        return $this->createPrivateGedcomRecord($access_level);
    }

    /**
     * Default for "other" object types
     *
     * @return void
     */
    public function extractNames(): void
    {
        $this->addName(static::RECORD_TYPE, $this->getFallBackName(), '');
    }

    /**
     * Derived classes should redefine this function, otherwise the object will have no name
     *
     * @return array<int,array<string,string>>
     */
    public function getAllNames(): array
    {
        if ($this->getAllNames === []) {
            if ($this->canShowName()) {
                // Ask the record to extract its names
                $this->extractNames();
                // No name found? Use a fallback.
                if ($this->getAllNames === []) {
                    $this->addName(static::RECORD_TYPE, $this->getFallBackName(), '');
                }
            } else {
                $this->addName(static::RECORD_TYPE, I18N::translate('Private'), '');
            }
        }

        return $this->getAllNames;
    }

    /**
     * If this object has no name, what do we call it?
     *
     * @return string
     */
    public function getFallBackName(): string
    {
        return e($this->xref());
    }

    /**
     * Which of the (possibly several) names of this record is the primary one.
     *
     * @return int
     */
    public function getPrimaryName(): int
    {
        static $language_script;

        $language_script ??= I18N::locale()->script()->code();

        if ($this->getPrimaryName === null) {
            // Generally, the first name is the primary one....
            $this->getPrimaryName = 0;
            // ...except when the language/name use different character sets
            foreach ($this->getAllNames() as $n => $name) {
                if (I18N::textScript($name['sort']) === $language_script) {
                    $this->getPrimaryName = $n;
                    break;
                }
            }
        }

        return $this->getPrimaryName;
    }

    /**
     * Which of the (possibly several) names of this record is the secondary one.
     *
     * @return int
     */
    public function getSecondaryName(): int
    {
        if ($this->getSecondaryName === null) {
            // Generally, the primary and secondary names are the same
            $this->getSecondaryName = $this->getPrimaryName();
            // ....except when there are names with different character sets
            $all_names = $this->getAllNames();
            if (count($all_names) > 1) {
                $primary_script = I18N::textScript($all_names[$this->getPrimaryName()]['sort']);
                foreach ($all_names as $n => $name) {
                    if ($n !== $this->getPrimaryName() && $name['type'] !== '_MARNM' && I18N::textScript($name['sort']) !== $primary_script) {
                        $this->getSecondaryName = $n;
                        break;
                    }
                }
            }
        }

        return $this->getSecondaryName;
    }

    /**
     * Allow the choice of primary name to be overidden, e.g. in a search result
     *
     * @param int|null $n
     *
     * @return void
     */
    public function setPrimaryName(int $n = null): void
    {
        $this->getPrimaryName   = $n;
        $this->getSecondaryName = null;
    }

    /**
     * Allow native PHP functions such as array_unique() to work with objects
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->xref . '@' . $this->tree->id();
    }

    /**
     * /**
     * Get variants of the name
     *
     * @return string
     */
    public function fullName(): string
    {
        if ($this->canShowName()) {
            $tmp = $this->getAllNames();

            return $tmp[$this->getPrimaryName()]['full'];
        }

        return I18N::translate('Private');
    }

    /**
     * Get a sortable version of the name. Do not display this!
     *
     * @return string
     */
    public function sortName(): string
    {
        // The sortable name is never displayed, no need to call canShowName()
        $tmp = $this->getAllNames();

        return $tmp[$this->getPrimaryName()]['sort'];
    }

    /**
     * Get the full name in an alternative character set
     *
     * @return string|null
     */
    public function alternateName(): ?string
    {
        if ($this->canShowName() && $this->getPrimaryName() !== $this->getSecondaryName()) {
            $all_names = $this->getAllNames();

            return $all_names[$this->getSecondaryName()]['full'];
        }

        return null;
    }

    /**
     * Format this object for display in a list
     *
     * @return string
     */
    public function formatList(): string
    {
        $html = '<a href="' . e($this->url()) . '">';
        $html .= '<b>' . $this->fullName() . '</b>';
        $html .= '</a>';
        $html .= $this->formatListDetails();

        return $html;
    }

    /**
     * This function should be redefined in derived classes to show any major
     * identifying characteristics of this record.
     *
     * @return string
     */
    public function formatListDetails(): string
    {
        return '';
    }

    /**
     * Extract/format the first fact from a list of facts.
     *
     * @param array<string> $facts
     * @param int           $style
     *
     * @return string
     */
    public function formatFirstMajorFact(array $facts, int $style): string
    {
        $fact = $this->facts($facts, true)->first();

        if ($fact === null) {
            return '';
        }

        // Only display if it has a date or place (or both)
        $attributes = [];

        if ($fact->date()->isOK()) {
            $attributes[] = view('fact-date', ['cal_link' => 'false', 'fact' => $fact, 'record' => $fact->record(), 'time' => false]);
        }

        if ($fact->place()->gedcomName() !== '' && $style === 2) {
            $attributes[] = $fact->place()->shortName();
        }

        if ($attributes === []) {
            return '';
        }

        return '<div><em>' . I18N::translate('%1$s: %2$s', $fact->label(), implode(' — ', $attributes)) . '</em></div>';
    }

    /**
     * Get all attributes (e.g. DATE or PLAC) from an event (e.g. BIRT or MARR).
     * This is used to display multiple events on the individual/family lists.
     * Multiple events can exist because of uncertainty in dates, dates in different
     * calendars, place-names in both latin and hebrew character sets, etc.
     * It also allows us to combine dates/places from different events in the summaries.
     *
     * @param array<string> $events
     *
     * @return array<Date>
     */
    public function getAllEventDates(array $events): array
    {
        $dates = [];
        foreach ($this->facts($events, false, null, true) as $event) {
            if ($event->date()->isOK()) {
                $dates[] = $event->date();
            }
        }

        return $dates;
    }

    /**
     * Get all the places for a particular type of event
     *
     * @param array<string> $events
     *
     * @return array<Place>
     */
    public function getAllEventPlaces(array $events): array
    {
        $places = [];
        foreach ($this->facts($events) as $event) {
            if (preg_match_all('/\n(?:2 PLAC|3 (?:ROMN|FONE|_HEB)) +(.+)/', $event->gedcom(), $ged_places)) {
                foreach ($ged_places[1] as $ged_place) {
                    $places[] = new Place($ged_place, $this->tree);
                }
            }
        }

        return $places;
    }

    /**
     * The facts and events for this record.
     *
     * @param array<string> $filter
     * @param bool          $sort
     * @param int|null      $access_level
     * @param bool          $ignore_deleted
     *
     * @return Collection<int,Fact>
     */
    public function facts(
        array $filter = [],
        bool $sort = false,
        int $access_level = null,
        bool $ignore_deleted = false
    ): Collection {
        $access_level ??= Auth::accessLevel($this->tree);

        // Convert BIRT into INDI:BIRT, etc.
        $filter = array_map(fn (string $tag): string => $this->tag() . ':' . $tag, $filter);

        $facts = new Collection();
        if ($this->canShow($access_level)) {
            foreach ($this->facts as $fact) {
                if (($filter === [] || in_array($fact->tag(), $filter, true)) && $fact->canShow($access_level)) {
                    $facts->push($fact);
                }
            }
        }

        if ($sort) {
            switch ($this->tag()) {
                case Family::RECORD_TYPE:
                case Individual::RECORD_TYPE:
                    $facts = Fact::sortFacts($facts);
                    break;

                default:
                    $subtags = Registry::elementFactory()->make($this->tag())->subtags();
                    $subtags = array_map(fn (string $tag): string => $this->tag() . ':' . $tag, array_keys($subtags));

                    if ($subtags !== []) {
                        // Renumber keys from 1.
                        $subtags = array_combine(range(1, count($subtags)), $subtags);
                    }


                    $facts = $facts
                        ->sort(static function (Fact $x, Fact $y) use ($subtags): int {
                            $sort_x = array_search($x->tag(), $subtags, true) ?: PHP_INT_MAX;
                            $sort_y = array_search($y->tag(), $subtags, true) ?: PHP_INT_MAX;

                            return $sort_x <=> $sort_y;
                        });
                    break;
            }
        }

        if ($ignore_deleted) {
            $facts = $facts->filter(static function (Fact $fact): bool {
                return !$fact->isPendingDeletion();
            });
        }

        return $facts;
    }

    /**
     * @return array<string,string>
     */
    public function missingFacts(): array
    {
        $missing_facts = [];

        foreach (Registry::elementFactory()->make($this->tag())->subtags() as $subtag => $repeat) {
            [, $max] = explode(':', $repeat);
            $max = $max === 'M' ? PHP_INT_MAX : (int) $max;

            if ($this->facts([$subtag], false, null, true)->count() < $max) {
                $missing_facts[$subtag] = $subtag;
                $missing_facts[$subtag] = Registry::elementFactory()->make($this->tag() . ':' . $subtag)->label();
            }
        }

        uasort($missing_facts, I18N::comparator());

        if (!Auth::canUploadMedia($this->tree, Auth::user())) {
            unset($missing_facts['OBJE']);
        }

        // We have special code for this.
        unset($missing_facts['FILE']);

        return $missing_facts;
    }

    /**
     * Get the last-change timestamp for this record
     *
     * @return TimestampInterface
     */
    public function lastChangeTimestamp(): TimestampInterface
    {
        $chan = $this->facts(['CHAN'])->first();

        if ($chan instanceof Fact) {
            // The record has a CHAN event.
            $date = $chan->date()->minimumDate();
            $ymd = sprintf('%04d-%02d-%02d', $date->year(), $date->month(), $date->day());

            if ($ymd !== '') {
                // The CHAN event has a valid DATE.
                if (preg_match('/\n3 TIME (([01]\d|2[0-3]):([0-5]\d):([0-5]\d))/', $chan->gedcom(), $match) === 1) {
                    return Registry::timestampFactory()->fromString($ymd . $match[1], 'Y-m-d H:i:s');
                }

                if (preg_match('/\n3 TIME (([01]\d|2[0-3]):([0-5]\d))/', $chan->gedcom(), $match) === 1) {
                    return Registry::timestampFactory()->fromString($ymd . $match[1], 'Y-m-d H:i');
                }

                return Registry::timestampFactory()->fromString($ymd, 'Y-m-d');
            }
        }

        // The record does not have a CHAN event
        return Registry::timestampFactory()->make(0);
    }

    /**
     * Get the last-change user for this record
     *
     * @return string
     */
    public function lastChangeUser(): string
    {
        $chan = $this->facts(['CHAN'])->first();

        if ($chan === null) {
            return I18N::translate('Unknown');
        }

        $chan_user = $chan->attribute('_WT_USER');
        if ($chan_user === '') {
            return I18N::translate('Unknown');
        }

        return $chan_user;
    }

    /**
     * Add a new fact to this record
     *
     * @param string $gedcom
     * @param bool   $update_chan
     *
     * @return void
     */
    public function createFact(string $gedcom, bool $update_chan): void
    {
        $this->updateFact('', $gedcom, $update_chan);
    }

    /**
     * Delete a fact from this record
     *
     * @param string $fact_id
     * @param bool   $update_chan
     *
     * @return void
     */
    public function deleteFact(string $fact_id, bool $update_chan): void
    {
        $this->updateFact($fact_id, '', $update_chan);
    }

    /**
     * Replace a fact with a new gedcom data.
     *
     * @param string $fact_id
     * @param string $gedcom
     * @param bool   $update_chan
     *
     * @return void
     * @throws Exception
     */
    public function updateFact(string $fact_id, string $gedcom, bool $update_chan): void
    {
        // Not all record types allow a CHAN event.
        $update_chan = $update_chan && in_array(static::RECORD_TYPE, Gedcom::RECORDS_WITH_CHAN, true);

        // MSDOS line endings will break things in horrible ways
        $gedcom = preg_replace('/[\r\n]+/', "\n", $gedcom);
        $gedcom = trim($gedcom);

        if ($this->pending === '') {
            throw new Exception('Cannot edit a deleted record');
        }
        if ($gedcom !== '' && !preg_match('/^1 ' . Gedcom::REGEX_TAG . '/', $gedcom)) {
            throw new Exception('Invalid GEDCOM data passed to GedcomRecord::updateFact(' . $gedcom . ')');
        }

        if ($this->pending) {
            $old_gedcom = $this->pending;
        } else {
            $old_gedcom = $this->gedcom;
        }

        // First line of record may contain data - e.g. NOTE records.
        [$new_gedcom] = explode("\n", $old_gedcom, 2);

        // Replacing (or deleting) an existing fact
        foreach ($this->facts([], false, Auth::PRIV_HIDE, true) as $fact) {
            if ($fact->id() === $fact_id) {
                if ($gedcom !== '') {
                    $new_gedcom .= "\n" . $gedcom;
                }
                $fact_id = 'NOT A VALID FACT ID'; // Only replace/delete one copy of a duplicate fact
            } elseif ($update_chan && str_ends_with($fact->tag(), ':CHAN')) {
                $new_gedcom .= "\n" . $this->updateChange($fact->gedcom());
            } else {
                $new_gedcom .= "\n" . $fact->gedcom();
            }
        }

        // Adding a new fact
        if ($fact_id === '') {
            $new_gedcom .= "\n" . $gedcom;
        }

        if ($update_chan && !str_contains($new_gedcom, "\n1 CHAN")) {
            $new_gedcom .= $this->updateChange("\n1 CHAN");
        }

        if ($new_gedcom !== $old_gedcom) {
            // Save the changes
            DB::table('change')->insert([
                'gedcom_id'  => $this->tree->id(),
                'xref'       => $this->xref,
                'old_gedcom' => $old_gedcom,
                'new_gedcom' => $new_gedcom,
                'status'     => 'pending',
                'user_id'    => Auth::id(),
            ]);

            $this->pending = $new_gedcom;

            if (Auth::user()->getPreference(UserInterface::PREF_AUTO_ACCEPT_EDITS) === '1') {
                $pending_changes_service = Registry::container()->get(PendingChangesService::class);

                $pending_changes_service->acceptRecord($this);
                $this->gedcom  = $new_gedcom;
                $this->pending = null;
            }
        }

        $this->facts = $this->parseFacts();
    }

    /**
     * Update this record
     *
     * @param string $gedcom
     * @param bool   $update_chan
     *
     * @return void
     */
    public function updateRecord(string $gedcom, bool $update_chan): void
    {
        // Not all record types allow a CHAN event.
        $update_chan = $update_chan && in_array(static::RECORD_TYPE, Gedcom::RECORDS_WITH_CHAN, true);

        // MSDOS line endings will break things in horrible ways
        $gedcom = preg_replace('/[\r\n]+/', "\n", $gedcom);
        $gedcom = trim($gedcom);

        // Update the CHAN record
        if ($update_chan) {
            if (preg_match('/\n1 CHAN(\n[2-9].*)*/', $gedcom, $match)) {
                $gedcom = strtr($gedcom, [$match[0] => $this->updateChange($match[0])]);
            } else {
                $gedcom .= $this->updateChange("\n1 CHAN");
            }
        }

        // Create a pending change
        DB::table('change')->insert([
            'gedcom_id'  => $this->tree->id(),
            'xref'       => $this->xref,
            'old_gedcom' => $this->gedcom(),
            'new_gedcom' => $gedcom,
            'status'     => 'pending',
            'user_id'    => Auth::id(),
        ]);

        // Clear the cache
        $this->pending = $gedcom;

        // Accept this pending change
        if (Auth::user()->getPreference(UserInterface::PREF_AUTO_ACCEPT_EDITS) === '1') {
            $pending_changes_service = Registry::container()->get(PendingChangesService::class);

            $pending_changes_service->acceptRecord($this);
            $this->gedcom  = $gedcom;
            $this->pending = null;
        }

        $this->facts = $this->parseFacts();

        Log::addEditLog('Update: ' . static::RECORD_TYPE . ' ' . $this->xref, $this->tree);
    }

    /**
     * Delete this record
     *
     * @return void
     */
    public function deleteRecord(): void
    {
        // Create a pending change
        if (!$this->isPendingDeletion()) {
            DB::table('change')->insert([
                'gedcom_id'  => $this->tree->id(),
                'xref'       => $this->xref,
                'old_gedcom' => $this->gedcom(),
                'new_gedcom' => '',
                'status'     => 'pending',
                'user_id'    => Auth::id(),
            ]);
        }

        // Auto-accept this pending change
        if (Auth::user()->getPreference(UserInterface::PREF_AUTO_ACCEPT_EDITS) === '1') {
            $pending_changes_service = Registry::container()->get(PendingChangesService::class);
            $pending_changes_service->acceptRecord($this);
        }

        Log::addEditLog('Delete: ' . static::RECORD_TYPE . ' ' . $this->xref, $this->tree);
    }

    /**
     * Remove all links from this record to $xref
     *
     * @param string $xref
     * @param bool   $update_chan
     *
     * @return void
     */
    public function removeLinks(string $xref, bool $update_chan): void
    {
        $value = '@' . $xref . '@';

        foreach ($this->facts() as $fact) {
            if ($fact->value() === $value) {
                $this->deleteFact($fact->id(), $update_chan);
            } elseif (preg_match_all('/\n(\d) ' . Gedcom::REGEX_TAG . ' ' . $value . '/', $fact->gedcom(), $matches, PREG_SET_ORDER)) {
                $gedcom = $fact->gedcom();
                foreach ($matches as $match) {
                    $next_level  = 1 + (int) $match[1];
                    $next_levels = '[' . $next_level . '-9]';
                    $gedcom      = preg_replace('/' . $match[0] . '(\n' . $next_levels . '.*)*/', '', $gedcom);
                }
                $this->updateFact($fact->id(), $gedcom, $update_chan);
            }
        }
    }

    /**
     * Each object type may have its own special rules, and re-implement this function.
     *
     * @param int $access_level
     *
     * @return bool
     */
    protected function canShowByType(int $access_level): bool
    {
        $fact_privacy = $this->tree->getFactPrivacy();

        if (isset($fact_privacy[static::RECORD_TYPE])) {
            // Restriction found
            return $fact_privacy[static::RECORD_TYPE] >= $access_level;
        }

        // No restriction found - must be public:
        return true;
    }

    /**
     * Generate a private version of this record
     *
     * @param int $access_level
     *
     * @return string
     */
    protected function createPrivateGedcomRecord(int $access_level): string
    {
        return '0 @' . $this->xref . '@ ' . static::RECORD_TYPE;
    }

    /**
     * Convert a name record into sortable and full/display versions. This default
     * should be OK for simple record types. INDI/FAM records will need to redefine it.
     *
     * @param string $type
     * @param string $value
     * @param string $gedcom
     *
     * @return void
     */
    protected function addName(string $type, string $value, string $gedcom): void
    {
        $this->getAllNames[] = [
            'type'   => $type,
            'sort'   => preg_replace_callback('/(\d+)/', static function (array $matches): string {
                return str_pad($matches[0], 10, '0', STR_PAD_LEFT);
            }, $value),
            'full'   => '<bdi>' . e($value) . '</bdi>',
            // This is used for display
            'fullNN' => $value,
            // This goes into the database
        ];
    }

    /**
     * Get all the names of a record, including ROMN, FONE and _HEB alternatives.
     * Records without a name (e.g. FAM) will need to redefine this function.
     * Parameters: the level 1 fact containing the name.
     * Return value: an array of name structures, each containing
     * ['type'] = the gedcom fact, e.g. NAME, TITL, FONE, _HEB, etc.
     * ['full'] = the name as specified in the record, e.g. 'Vincent van Gogh' or 'John Unknown'
     * ['sort'] = a sortable version of the name (not for display), e.g. 'Gogh, Vincent' or '@N.N., John'
     *
     * @param int              $level
     * @param string           $fact_type
     * @param Collection<int,Fact> $facts
     *
     * @return void
     */
    protected function extractNamesFromFacts(int $level, string $fact_type, Collection $facts): void
    {
        $sublevel    = $level + 1;
        $subsublevel = $sublevel + 1;
        foreach ($facts as $fact) {
            if (preg_match_all('/^' . $level . ' (' . $fact_type . ') (.+)((\n[' . $sublevel . '-9].+)*)/m', $fact->gedcom(), $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    // Treat 1 NAME / 2 TYPE married the same as _MARNM
                    if ($match[1] === 'NAME' && str_contains(strtoupper($match[3]), "\n2 TYPE MARRIED")) {
                        $this->addName('_MARNM', $match[2], $fact->gedcom());
                    } else {
                        $this->addName($match[1], $match[2], $fact->gedcom());
                    }
                    if ($match[3] && preg_match_all('/^' . $sublevel . ' (ROMN|FONE|_\w+) (.+)((\n[' . $subsublevel . '-9].+)*)/m', $match[3], $submatches, PREG_SET_ORDER)) {
                        foreach ($submatches as $submatch) {
                            if ($submatch[1] !== '_RUFNAME') {
                                $this->addName($submatch[1], $submatch[2], $match[3]);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Split the record into facts
     *
     * @return array<Fact>
     */
    private function parseFacts(): array
    {
        // Split the record into facts
        if ($this->gedcom) {
            $gedcom_facts = preg_split('/\n(?=1)/', $this->gedcom);
            array_shift($gedcom_facts);
        } else {
            $gedcom_facts = [];
        }
        if ($this->pending) {
            $pending_facts = preg_split('/\n(?=1)/', $this->pending);
            array_shift($pending_facts);
        } else {
            $pending_facts = [];
        }

        $facts = [];

        foreach ($gedcom_facts as $gedcom_fact) {
            $fact = new Fact($gedcom_fact, $this, md5($gedcom_fact));
            if ($this->pending !== null && !in_array($gedcom_fact, $pending_facts, true)) {
                $fact->setPendingDeletion();
            }
            $facts[] = $fact;
        }
        foreach ($pending_facts as $pending_fact) {
            if (!in_array($pending_fact, $gedcom_facts, true)) {
                $fact = new Fact($pending_fact, $this, md5($pending_fact));
                $fact->setPendingAddition();
                $facts[] = $fact;
            }
        }

        return $facts;
    }

    /**
     * Work out whether this record can be shown to a user with a given access level
     *
     * @param int $access_level
     *
     * @return bool
     */
    private function canShowRecord(int $access_level): bool
    {
        // This setting would better be called "$ENABLE_PRIVACY"
        if (!$this->tree->getPreference('HIDE_LIVE_PEOPLE')) {
            return true;
        }

        // We should always be able to see our own record (unless an admin is applying download restrictions)
        if ($this->xref() === $this->tree->getUserPreference(Auth::user(), UserInterface::PREF_TREE_ACCOUNT_XREF) && $access_level === Auth::accessLevel($this->tree)) {
            return true;
        }

        // Does this record have a restriction notice?
        // Cannot use $this->>fact(), as that function calls this one.
        if (preg_match('/\n1 RESN (.+)/', $this->gedcom(), $match)) {
            $element     = new RestrictionNotice('');
            $restriction = $element->canonical($match[1]);

            if (str_starts_with($restriction, RestrictionNotice::VALUE_CONFIDENTIAL)) {
                return Auth::PRIV_NONE >= $access_level;
            }
            if (str_starts_with($restriction, RestrictionNotice::VALUE_PRIVACY)) {
                return Auth::PRIV_USER >= $access_level;
            }
            if (str_starts_with($restriction, RestrictionNotice::VALUE_NONE)) {
                return true;
            }
        }

        // Does this record have a default RESN?
        $individual_privacy = $this->tree->getIndividualPrivacy();
        if (isset($individual_privacy[$this->xref()])) {
            return $individual_privacy[$this->xref()] >= $access_level;
        }

        // Privacy rules do not apply to admins
        if (Auth::PRIV_NONE >= $access_level) {
            return true;
        }

        // Different types of record have different privacy rules
        return $this->canShowByType($access_level);
    }

    /**
     * Lock the database row, to prevent concurrent edits.
     */
    public function lock(): void
    {
        DB::table('other')
            ->where('o_file', '=', $this->tree->id())
            ->where('o_id', '=', $this->xref())
            ->lockForUpdate()
            ->get();
    }

    /**
     * Change records may contain notes and other fields.  Just update the date/time/author.
     *
     * @param string $gedcom
     *
     * @return string
     */
    private function updateChange(string $gedcom): string
    {
        $gedcom = preg_replace('/\n2 (DATE|_WT_USER).*(\n[3-9].*)*/', '', $gedcom);
        $today  = strtoupper(date('d M Y'));
        $now    = date('H:i:s');
        $author = Auth::user()->userName();

        return $gedcom . "\n2 DATE " . $today . "\n3 TIME " . $now . "\n2 _WT_USER " . $author;
    }
}
