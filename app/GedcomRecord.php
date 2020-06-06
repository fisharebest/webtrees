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
use Exception;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\Http\RequestHandlers\GedcomRecordPage;
use Fisharebest\Webtrees\Services\PendingChangesService;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Throwable;
use Transliterator;

use function addcslashes;
use function app;
use function array_shift;
use function assert;
use function count;
use function date;
use function e;
use function explode;
use function in_array;
use function md5;
use function preg_match;
use function preg_match_all;
use function preg_replace;
use function preg_replace_callback;
use function preg_split;
use function route;
use function str_pad;
use function strip_tags;
use function strpos;
use function strtoupper;
use function trim;

use const PREG_SET_ORDER;
use const STR_PAD_LEFT;

/**
 * A GEDCOM object.
 */
class GedcomRecord
{
    public const RECORD_TYPE = 'UNKNOWN';

    protected const ROUTE_NAME = GedcomRecordPage::class;

    /** @var string The record identifier */
    protected $xref;

    /** @var Tree  The family tree to which this record belongs */
    protected $tree;

    /** @var string  GEDCOM data (before any pending edits) */
    protected $gedcom;

    /** @var string|null  GEDCOM data (after any pending edits) */
    protected $pending;

    /** @var Fact[] facts extracted from $gedcom/$pending */
    protected $facts;

    /** @var string[][] All the names of this individual */
    protected $getAllNames;

    /** @var int|null Cached result */
    protected $getPrimaryName;
    /** @var int|null Cached result */
    protected $getSecondaryName;

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

        $this->parseFacts();
    }

    /**
     * A closure which will create a record from a database row.
     *
     * @deprecated since 2.0.4.  Will be removed in 2.1.0 - Use Factory::gedcomRecord()
     *
     * @param Tree $tree
     *
     * @return Closure
     */
    public static function rowMapper(Tree $tree): Closure
    {
        return Factory::gedcomRecord()->mapper($tree);
    }

    /**
     * A closure which will filter out private records.
     *
     * @return Closure
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
     * @return Closure
     */
    public static function nameComparator(): Closure
    {
        return static function (GedcomRecord $x, GedcomRecord $y): int {
            if ($x->canShowName()) {
                if ($y->canShowName()) {
                    return I18N::strcasecmp($x->sortName(), $y->sortName());
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
     * @return Closure
     */
    public static function lastChangeComparator(int $direction = 1): Closure
    {
        return static function (GedcomRecord $x, GedcomRecord $y) use ($direction): int {
            return $direction * ($x->lastChangeTimestamp() <=> $y->lastChangeTimestamp());
        };
    }

    /**
     * Get an instance of a GedcomRecord object. For single records,
     * we just receive the XREF. For bulk records (such as lists
     * and search results) we can receive the GEDCOM data as well.
     *
     * @deprecated since 2.0.4.  Will be removed in 2.1.0 - Use Factory::gedcomRecord()
     *
     * @param string      $xref
     * @param Tree        $tree
     * @param string|null $gedcom
     *
     * @return GedcomRecord|Individual|Family|Source|Repository|Media|Note|Submitter|null
     */
    public static function getInstance(string $xref, Tree $tree, string $gedcom = null)
    {
        return Factory::gedcomRecord()->make($xref, $tree, $gedcom);
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
     * Generate a "slug" to use in pretty URLs.
     *
     * @return string
     */
    public function slug(): string
    {
        $slug = strip_tags($this->fullName());

        try {
            $transliterator = Transliterator::create('Any-Latin;Latin-ASCII');
            $slug           = $transliterator->transliterate($slug);
        } catch (Throwable $ex) {
            // ext-intl not installed?
            // Transliteration algorithms not present in lib-icu?
        }

        $slug = preg_replace('/[^A-Za-z0-9]+/', '-', $slug);

        return trim($slug, '-') ?: '-';
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
            'slug' => $this->slug(),
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
        $access_level = $access_level ?? Auth::accessLevel($this->tree);

        // We use this value to bypass privacy checks. For example,
        // when downloading data or when calculating privacy itself.
        if ($access_level === Auth::PRIV_HIDE) {
            return true;
        }

        $cache_key = 'show-' . $this->xref . '-' . $this->tree->id() . '-' . $access_level;

        return app('cache.array')->remember($cache_key, function () use ($access_level) {
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

        return Auth::isEditor($this->tree) && strpos($this->gedcom, "\n1 RESN locked") === false;
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
     * @return string[][]
     */
    public function getAllNames(): array
    {
        if ($this->getAllNames === null) {
            $this->getAllNames = [];
            if ($this->canShowName()) {
                // Ask the record to extract its names
                $this->extractNames();
                // No name found? Use a fallback.
                if (!$this->getAllNames) {
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

        if ($language_script === null) {
            $language_script = $language_script ?? I18N::locale()->script()->code();
        }

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
    public function __toString()
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
        $html = '<a href="' . e($this->url()) . '" class="list_item">';
        $html .= '<b>' . $this->fullName() . '</b>';
        $html .= $this->formatListDetails();
        $html .= '</a>';

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
     * @param string[] $facts
     * @param int      $style
     *
     * @return string
     */
    public function formatFirstMajorFact(array $facts, int $style): string
    {
        foreach ($this->facts($facts, true) as $event) {
            // Only display if it has a date or place (or both)
            if ($event->date()->isOK() && $event->place()->gedcomName() !== '') {
                $joiner = ' — ';
            } else {
                $joiner = '';
            }
            if ($event->date()->isOK() || $event->place()->gedcomName() !== '') {
                switch ($style) {
                    case 1:
                        return '<br><em>' . $event->label() . ' ' . FunctionsPrint::formatFactDate($event, $this, false, false) . $joiner . FunctionsPrint::formatFactPlace($event) . '</em>';
                    case 2:
                        return '<dl><dt class="label">' . $event->label() . '</dt><dd class="field">' . FunctionsPrint::formatFactDate($event, $this, false, false) . $joiner . FunctionsPrint::formatFactPlace($event) . '</dd></dl>';
                }
            }
        }

        return '';
    }

    /**
     * Find individuals linked to this record.
     *
     * @param string $link
     *
     * @return Collection<Individual>
     */
    public function linkedIndividuals(string $link): Collection
    {
        return DB::table('individuals')
            ->join('link', static function (JoinClause $join): void {
                $join
                    ->on('l_file', '=', 'i_file')
                    ->on('l_from', '=', 'i_id');
            })
            ->where('i_file', '=', $this->tree->id())
            ->where('l_type', '=', $link)
            ->where('l_to', '=', $this->xref)
            ->select(['individuals.*'])
            ->get()
            ->map(Factory::individual()->mapper($this->tree))
            ->filter(self::accessFilter());
    }

    /**
     * Find families linked to this record.
     *
     * @param string $link
     *
     * @return Collection<Family>
     */
    public function linkedFamilies(string $link): Collection
    {
        return DB::table('families')
            ->join('link', static function (JoinClause $join): void {
                $join
                    ->on('l_file', '=', 'f_file')
                    ->on('l_from', '=', 'f_id');
            })
            ->where('f_file', '=', $this->tree->id())
            ->where('l_type', '=', $link)
            ->where('l_to', '=', $this->xref)
            ->select(['families.*'])
            ->get()
            ->map(Factory::family()->mapper($this->tree))
            ->filter(self::accessFilter());
    }

    /**
     * Find sources linked to this record.
     *
     * @param string $link
     *
     * @return Collection<Source>
     */
    public function linkedSources(string $link): Collection
    {
        return DB::table('sources')
            ->join('link', static function (JoinClause $join): void {
                $join
                    ->on('l_file', '=', 's_file')
                    ->on('l_from', '=', 's_id');
            })
            ->where('s_file', '=', $this->tree->id())
            ->where('l_type', '=', $link)
            ->where('l_to', '=', $this->xref)
            ->select(['sources.*'])
            ->get()
            ->map(Factory::source()->mapper($this->tree))
            ->filter(self::accessFilter());
    }

    /**
     * Find media objects linked to this record.
     *
     * @param string $link
     *
     * @return Collection<Media>
     */
    public function linkedMedia(string $link): Collection
    {
        return DB::table('media')
            ->join('link', static function (JoinClause $join): void {
                $join
                    ->on('l_file', '=', 'm_file')
                    ->on('l_from', '=', 'm_id');
            })
            ->where('m_file', '=', $this->tree->id())
            ->where('l_type', '=', $link)
            ->where('l_to', '=', $this->xref)
            ->select(['media.*'])
            ->get()
            ->map(Factory::media()->mapper($this->tree))
            ->filter(self::accessFilter());
    }

    /**
     * Find notes linked to this record.
     *
     * @param string $link
     *
     * @return Collection<Note>
     */
    public function linkedNotes(string $link): Collection
    {
        return DB::table('other')
            ->join('link', static function (JoinClause $join): void {
                $join
                    ->on('l_file', '=', 'o_file')
                    ->on('l_from', '=', 'o_id');
            })
            ->where('o_file', '=', $this->tree->id())
            ->where('o_type', '=', Note::RECORD_TYPE)
            ->where('l_type', '=', $link)
            ->where('l_to', '=', $this->xref)
            ->select(['other.*'])
            ->get()
            ->map(Factory::note()->mapper($this->tree))
            ->filter(self::accessFilter());
    }

    /**
     * Find repositories linked to this record.
     *
     * @param string $link
     *
     * @return Collection<Repository>
     */
    public function linkedRepositories(string $link): Collection
    {
        return DB::table('other')
            ->join('link', static function (JoinClause $join): void {
                $join
                    ->on('l_file', '=', 'o_file')
                    ->on('l_from', '=', 'o_id');
            })
            ->where('o_file', '=', $this->tree->id())
            ->where('o_type', '=', Repository::RECORD_TYPE)
            ->where('l_type', '=', $link)
            ->where('l_to', '=', $this->xref)
            ->select(['other.*'])
            ->get()
            ->map(Factory::repository()->mapper($this->tree))
            ->filter(self::accessFilter());
    }

    /**
     * Find locations linked to this record.
     *
     * @param string $link
     *
     * @return Collection<Location>
     */
    public function linkedLocations(string $link): Collection
    {
        return DB::table('other')
            ->join('link', static function (JoinClause $join): void {
                $join
                    ->on('l_file', '=', 'o_file')
                    ->on('l_from', '=', 'o_id');
            })
            ->where('o_file', '=', $this->tree->id())
            ->where('o_type', '=', Location::RECORD_TYPE)
            ->where('l_type', '=', $link)
            ->where('l_to', '=', $this->xref)
            ->select(['other.*'])
            ->get()
            ->map(Factory::location()->mapper($this->tree))
            ->filter(self::accessFilter());
    }
    
    /**
     * Get all attributes (e.g. DATE or PLAC) from an event (e.g. BIRT or MARR).
     * This is used to display multiple events on the individual/family lists.
     * Multiple events can exist because of uncertainty in dates, dates in different
     * calendars, place-names in both latin and hebrew character sets, etc.
     * It also allows us to combine dates/places from different events in the summaries.
     *
     * @param string[] $events
     *
     * @return Date[]
     */
    public function getAllEventDates(array $events): array
    {
        $dates = [];
        foreach ($this->facts($events) as $event) {
            if ($event->date()->isOK()) {
                $dates[] = $event->date();
            }
        }

        return $dates;
    }

    /**
     * Get all the places for a particular type of event
     *
     * @param string[] $events
     *
     * @return Place[]
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
     * @param string[] $filter
     * @param bool     $sort
     * @param int|null $access_level
     * @param bool     $ignore_deleted
     *
     * @return Collection<Fact>
     */
    public function facts(
        array $filter = [],
        bool $sort = false,
        int $access_level = null,
        bool $ignore_deleted = false
    ): Collection {
        $access_level = $access_level ?? Auth::accessLevel($this->tree);

        $facts = new Collection();
        if ($this->canShow($access_level)) {
            foreach ($this->facts as $fact) {
                if (($filter === [] || in_array($fact->getTag(), $filter, true)) && $fact->canShow($access_level)) {
                    $facts->push($fact);
                }
            }
        }

        if ($sort) {
            $facts = Fact::sortFacts($facts);
        }

        if ($ignore_deleted) {
            $facts = $facts->filter(static function (Fact $fact): bool {
                return !$fact->isPendingDeletion();
            });
        }

        return new Collection($facts);
    }

    /**
     * Get the last-change timestamp for this record
     *
     * @return Carbon
     */
    public function lastChangeTimestamp(): Carbon
    {
        /** @var Fact|null $chan */
        $chan = $this->facts(['CHAN'])->first();

        if ($chan instanceof Fact) {
            // The record does have a CHAN event
            $d = $chan->date()->minimumDate();

            if (preg_match('/\n3 TIME (\d\d):(\d\d):(\d\d)/', $chan->gedcom(), $match)) {
                return Carbon::create($d->year(), $d->month(), $d->day(), (int) $match[1], (int) $match[2], (int) $match[3]);
            }

            if (preg_match('/\n3 TIME (\d\d):(\d\d)/', $chan->gedcom(), $match)) {
                return Carbon::create($d->year(), $d->month(), $d->day(), (int) $match[1], (int) $match[2]);
            }

            return Carbon::create($d->year(), $d->month(), $d->day());
        }

        // The record does not have a CHAN event
        return Carbon::createFromTimestamp(0);
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
            } elseif ($fact->getTag() !== 'CHAN' || !$update_chan) {
                $new_gedcom .= "\n" . $fact->gedcom();
            }
        }

        // Adding a new fact
        if ($fact_id === '') {
            $new_gedcom .= "\n" . $gedcom;
        }

        if ($update_chan && strpos($new_gedcom, "\n1 CHAN") === false) {
            $today = strtoupper(date('d M Y'));
            $now   = date('H:i:s');
            $new_gedcom .= "\n1 CHAN\n2 DATE " . $today . "\n3 TIME " . $now . "\n2 _WT_USER " . Auth::user()->userName();
        }

        if ($new_gedcom !== $old_gedcom) {
            // Save the changes
            DB::table('change')->insert([
                'gedcom_id'  => $this->tree->id(),
                'xref'       => $this->xref,
                'old_gedcom' => $old_gedcom,
                'new_gedcom' => $new_gedcom,
                'user_id'    => Auth::id(),
            ]);

            $this->pending = $new_gedcom;

            if (Auth::user()->getPreference(User::PREF_AUTO_ACCEPT_EDITS) === '1') {
                app(PendingChangesService::class)->acceptRecord($this);
                $this->gedcom  = $new_gedcom;
                $this->pending = null;
            }
        }
        $this->parseFacts();
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
            $gedcom = preg_replace('/\n1 CHAN(\n[2-9].*)*/', '', $gedcom);
            $today = strtoupper(date('d M Y'));
            $now   = date('H:i:s');
            $gedcom .= "\n1 CHAN\n2 DATE " . $today . "\n3 TIME " . $now . "\n2 _WT_USER " . Auth::user()->userName();
        }

        // Create a pending change
        DB::table('change')->insert([
            'gedcom_id'  => $this->tree->id(),
            'xref'       => $this->xref,
            'old_gedcom' => $this->gedcom(),
            'new_gedcom' => $gedcom,
            'user_id'    => Auth::id(),
        ]);

        // Clear the cache
        $this->pending = $gedcom;

        // Accept this pending change
        if (Auth::user()->getPreference(User::PREF_AUTO_ACCEPT_EDITS) === '1') {
            app(PendingChangesService::class)->acceptRecord($this);
            $this->gedcom  = $gedcom;
            $this->pending = null;
        }

        $this->parseFacts();

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
                'user_id'    => Auth::id(),
            ]);
        }

        // Auto-accept this pending change
        if (Auth::user()->getPreference(User::PREF_AUTO_ACCEPT_EDITS) === '1') {
            app(PendingChangesService::class)->acceptRecord($this);
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
                    $next_level  = $match[1] + 1;
                    $next_levels = '[' . $next_level . '-9]';
                    $gedcom      = preg_replace('/' . $match[0] . '(\n' . $next_levels . '.*)*/', '', $gedcom);
                }
                $this->updateFact($fact->id(), $gedcom, $update_chan);
            }
        }
    }

    /**
     * Fetch XREFs of all records linked to a record - when deleting an object, we must
     * also delete all links to it.
     *
     * @return GedcomRecord[]
     */
    public function linkingRecords(): array
    {
        $like = addcslashes($this->xref(), '\\%_');

        $union = DB::table('change')
            ->where('gedcom_id', '=', $this->tree()->id())
            ->where('new_gedcom', 'LIKE', '%@' . $like . '@%')
            ->where('new_gedcom', 'NOT LIKE', '0 @' . $like . '@%')
            ->whereIn('change_id', function (Builder $query): void {
                $query->select(new Expression('MAX(change_id)'))
                    ->from('change')
                    ->where('gedcom_id', '=', $this->tree->id())
                    ->where('status', '=', 'pending')
                    ->groupBy(['xref']);
            })
            ->select(['xref']);

        $xrefs = DB::table('link')
            ->where('l_file', '=', $this->tree()->id())
            ->where('l_to', '=', $this->xref())
            ->select(['l_from'])
            ->union($union)
            ->pluck('l_from');

        return $xrefs->map(function (string $xref): GedcomRecord {
            $record = Factory::gedcomRecord()->make($xref, $this->tree);
            assert($record instanceof GedcomRecord);

            return $record;
        })->all();
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
        return '0 @' . $this->xref . '@ ' . static::RECORD_TYPE . "\n1 NOTE " . I18N::translate('Private');
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
            'sort'   => preg_replace_callback('/([0-9]+)/', static function (array $matches): string {
                return str_pad($matches[0], 10, '0', STR_PAD_LEFT);
            }, $value),
            'full'   => '<span dir="auto">' . e($value) . '</span>',
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
     * @param Collection<Fact> $facts
     *
     * @return void
     */
    protected function extractNamesFromFacts(int $level, string $fact_type, Collection $facts): void
    {
        $sublevel    = $level + 1;
        $subsublevel = $sublevel + 1;
        foreach ($facts as $fact) {
            if (preg_match_all("/^{$level} ({$fact_type}) (.+)((\n[{$sublevel}-9].+)*)/m", $fact->gedcom(), $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    // Treat 1 NAME / 2 TYPE married the same as _MARNM
                    if ($match[1] === 'NAME' && strpos($match[3], "\n2 TYPE married") !== false) {
                        $this->addName('_MARNM', $match[2], $fact->gedcom());
                    } else {
                        $this->addName($match[1], $match[2], $fact->gedcom());
                    }
                    if ($match[3] && preg_match_all("/^{$sublevel} (ROMN|FONE|_\w+) (.+)((\n[{$subsublevel}-9].+)*)/m", $match[3], $submatches, PREG_SET_ORDER)) {
                        foreach ($submatches as $submatch) {
                            $this->addName($submatch[1], $submatch[2], $match[3]);
                        }
                    }
                }
            }
        }
    }

    /**
     * Split the record into facts
     *
     * @return void
     */
    private function parseFacts(): void
    {
        // Split the record into facts
        if ($this->gedcom) {
            $gedcom_facts = preg_split('/\n(?=1)/s', $this->gedcom);
            array_shift($gedcom_facts);
        } else {
            $gedcom_facts = [];
        }
        if ($this->pending) {
            $pending_facts = preg_split('/\n(?=1)/s', $this->pending);
            array_shift($pending_facts);
        } else {
            $pending_facts = [];
        }

        $this->facts = [];

        foreach ($gedcom_facts as $gedcom_fact) {
            $fact = new Fact($gedcom_fact, $this, md5($gedcom_fact));
            if ($this->pending !== null && !in_array($gedcom_fact, $pending_facts, true)) {
                $fact->setPendingDeletion();
            }
            $this->facts[] = $fact;
        }
        foreach ($pending_facts as $pending_fact) {
            if (!in_array($pending_fact, $gedcom_facts, true)) {
                $fact = new Fact($pending_fact, $this, md5($pending_fact));
                $fact->setPendingAddition();
                $this->facts[] = $fact;
            }
        }
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
        if ($this->xref() === $this->tree->getUserPreference(Auth::user(), User::PREF_TREE_ACCOUNT_XREF) && $access_level === Auth::accessLevel($this->tree)) {
            return true;
        }

        // Does this record have a RESN?
        if (strpos($this->gedcom, "\n1 RESN confidential") !== false) {
            return Auth::PRIV_NONE >= $access_level;
        }
        if (strpos($this->gedcom, "\n1 RESN privacy") !== false) {
            return Auth::PRIV_USER >= $access_level;
        }
        if (strpos($this->gedcom, "\n1 RESN none") !== false) {
            return true;
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
}
