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
use Fisharebest\Webtrees\Http\RequestHandlers\FamilyPage;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;

/**
 * A GEDCOM family (FAM) object.
 */
class Family extends GedcomRecord
{
    public const RECORD_TYPE = 'FAM';

    protected const ROUTE_NAME = FamilyPage::class;

    // The husband (or first spouse for same-sex couples)
    private ?Individual $husb = null;

    // The wife (or second spouse for same-sex couples)
    private ?Individual $wife = null;

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
        parent::__construct($xref, $gedcom, $pending, $tree);

        // Make sure we find records in pending records.
        $gedcom_pending = $gedcom . "\n" . $pending;

        if (preg_match('/\n1 HUSB @(.+)@/', $gedcom_pending, $match)) {
            $this->husb = Registry::individualFactory()->make($match[1], $tree);
        }
        if (preg_match('/\n1 WIFE @(.+)@/', $gedcom_pending, $match)) {
            $this->wife = Registry::individualFactory()->make($match[1], $tree);
        }
    }

    /**
     * A closure which will compare families by marriage date.
     *
     * @return Closure(Family,Family):int
     */
    public static function marriageDateComparator(): Closure
    {
        return static function (Family $x, Family $y): int {
            return Date::compare($x->getMarriageDate(), $y->getMarriageDate());
        };
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
        if ($this->tree->getPreference('SHOW_PRIVATE_RELATIONSHIPS') === '1') {
            $access_level = Auth::PRIV_HIDE;
        }

        $rec = '0 @' . $this->xref . '@ FAM';
        // Just show the 1 CHIL/HUSB/WIFE tag, not any subtags, which may contain private data
        preg_match_all('/\n1 (?:CHIL|HUSB|WIFE) @(' . Gedcom::REGEX_XREF . ')@/', $this->gedcom, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $rela = Registry::individualFactory()->make($match[1], $this->tree);
            if ($rela instanceof Individual && $rela->canShow($access_level)) {
                $rec .= $match[0];
            }
        }

        return $rec;
    }

    /**
     * Get the male (or first female) partner of the family
     *
     * @param int|null $access_level
     *
     * @return Individual|null
     */
    public function husband(int $access_level = null): ?Individual
    {
        if ($this->tree->getPreference('SHOW_PRIVATE_RELATIONSHIPS') === '1') {
            $access_level = Auth::PRIV_HIDE;
        }

        if ($this->husb instanceof Individual && $this->husb->canShowName($access_level)) {
            return $this->husb;
        }

        return null;
    }

    /**
     * Get the female (or second male) partner of the family
     *
     * @param int|null $access_level
     *
     * @return Individual|null
     */
    public function wife(int $access_level = null): ?Individual
    {
        if ($this->tree->getPreference('SHOW_PRIVATE_RELATIONSHIPS') === '1') {
            $access_level = Auth::PRIV_HIDE;
        }

        if ($this->wife instanceof Individual && $this->wife->canShowName($access_level)) {
            return $this->wife;
        }

        return null;
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
        // Hide a family if any member is private
        preg_match_all('/\n1 (?:CHIL|HUSB|WIFE) @(' . Gedcom::REGEX_XREF . ')@/', $this->gedcom, $matches);
        foreach ($matches[1] as $match) {
            $person = Registry::individualFactory()->make($match, $this->tree);
            if ($person && !$person->canShow($access_level)) {
                return false;
            }
        }

        return true;
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
        // We can always see the name (Husband-name + Wife-name), however,
        // the name will often be "private + private"
        return true;
    }

    /**
     * Find the spouse of a person.
     *
     * @param Individual $person
     * @param int|null   $access_level
     *
     * @return Individual|null
     */
    public function spouse(Individual $person, int $access_level = null): ?Individual
    {
        if ($person === $this->wife) {
            return $this->husband($access_level);
        }

        return $this->wife($access_level);
    }

    /**
     * Get the (zero, one or two) spouses from this family.
     *
     * @param int|null $access_level
     *
     * @return Collection<int,Individual>
     */
    public function spouses(int $access_level = null): Collection
    {
        $spouses = new Collection([
            $this->husband($access_level),
            $this->wife($access_level),
        ]);

        return $spouses->filter();
    }

    /**
     * Get a list of this family’s children.
     *
     * @param int|null $access_level
     *
     * @return Collection<int,Individual>
     */
    public function children(int $access_level = null): Collection
    {
        $access_level ??= Auth::accessLevel($this->tree);

        if ($this->tree->getPreference('SHOW_PRIVATE_RELATIONSHIPS') === '1') {
            $access_level = Auth::PRIV_HIDE;
        }

        $children = new Collection();

        foreach ($this->facts(['CHIL'], false, $access_level) as $fact) {
            $child = $fact->target();

            if ($child instanceof Individual && $child->canShowName($access_level)) {
                $children->push($child);
            }
        }

        return $children;
    }

    /**
     * Number of children - for the individual list
     *
     * @return int
     */
    public function numberOfChildren(): int
    {
        $nchi = $this->children()->count();

        foreach ($this->facts(['NCHI']) as $fact) {
            $nchi = max($nchi, (int) $fact->value());
        }

        return $nchi;
    }

    /**
     * get the marriage event
     *
     * @return Fact|null
     */
    public function getMarriage(): ?Fact
    {
        return $this->facts(['MARR'])->first();
    }

    /**
     * Get marriage date
     *
     * @return Date
     */
    public function getMarriageDate(): Date
    {
        $marriage = $this->getMarriage();
        if ($marriage instanceof Fact) {
            return $marriage->date();
        }

        return new Date('');
    }

    /**
     * Get the marriage year - displayed on lists of families
     *
     * @return int
     */
    public function getMarriageYear(): int
    {
        return $this->getMarriageDate()->minimumDate()->year;
    }

    /**
     * Get the marriage place
     *
     * @return Place
     */
    public function getMarriagePlace(): Place
    {
        $marriage = $this->getMarriage();

        if ($marriage instanceof Fact) {
            return $marriage->place();
        }

        return new Place('', $this->tree);
    }

    /**
     * Get a list of all marriage dates - for the family lists.
     *
     * @return array<Date>
     */
    public function getAllMarriageDates(): array
    {
        foreach (Gedcom::MARRIAGE_EVENTS as $event) {
            $array = $this->getAllEventDates([$event]);

            if ($array !== []) {
                return $array;
            }
        }

        return [];
    }

    /**
     * Get a list of all marriage places - for the family lists.
     *
     * @return array<Place>
     */
    public function getAllMarriagePlaces(): array
    {
        foreach (Gedcom::MARRIAGE_EVENTS as $event) {
            $places = $this->getAllEventPlaces([$event]);
            if ($places !== []) {
                return $places;
            }
        }

        return [];
    }

    /**
     * Derived classes should redefine this function, otherwise the object will have no name
     *
     * @return array<int,array<string,string>>
     */
    public function getAllNames(): array
    {
        if ($this->getAllNames === []) {
            // Check the script used by each name, so we can match cyrillic with cyrillic, greek with greek, etc.
            $husb_names = [];
            if ($this->husb instanceof Individual) {
                $husb_names = array_filter($this->husb->getAllNames(), static function (array $x): bool {
                    return $x['type'] !== '_MARNM';
                });
            }
            // If the individual only has married names, create a fake birth name.
            if ($husb_names === []) {
                $husb_names[] = [
                    'type' => 'BIRT',
                    'sort' => Individual::NOMEN_NESCIO,
                    'full' => I18N::translateContext('Unknown given name', '…') . ' ' . I18N::translateContext('Unknown surname', '…'),
                ];
            }
            foreach ($husb_names as $n => $husb_name) {
                $husb_names[$n]['script'] = I18N::textScript($husb_name['full']);
            }

            $wife_names = [];
            if ($this->wife instanceof Individual) {
                $wife_names = array_filter($this->wife->getAllNames(), static function (array $x): bool {
                    return $x['type'] !== '_MARNM';
                });
            }
            // If the individual only has married names, create a fake birth name.
            if ($wife_names === []) {
                $wife_names[] = [
                    'type' => 'BIRT',
                    'sort' => Individual::NOMEN_NESCIO,
                    'full' => I18N::translateContext('Unknown given name', '…') . ' ' . I18N::translateContext('Unknown surname', '…'),
                ];
            }
            foreach ($wife_names as $n => $wife_name) {
                $wife_names[$n]['script'] = I18N::textScript($wife_name['full']);
            }

            // Add the matched names first
            foreach ($husb_names as $husb_name) {
                foreach ($wife_names as $wife_name) {
                    if ($husb_name['script'] === $wife_name['script']) {
                        $this->getAllNames[] = [
                            'type' => $husb_name['type'],
                            'sort' => $husb_name['sort'] . ' + ' . $wife_name['sort'],
                            'full' => $husb_name['full'] . ' + ' . $wife_name['full'],
                            // No need for a fullNN entry - we do not currently store FAM names in the database
                        ];
                    }
                }
            }

            // Add the unmatched names second (there may be no matched names)
            foreach ($husb_names as $husb_name) {
                foreach ($wife_names as $wife_name) {
                    if ($husb_name['script'] !== $wife_name['script']) {
                        $this->getAllNames[] = [
                            'type' => $husb_name['type'],
                            'sort' => $husb_name['sort'] . ' + ' . $wife_name['sort'],
                            'full' => $husb_name['full'] . ' + ' . $wife_name['full'],
                            // No need for a fullNN entry - we do not currently store FAM names in the database
                        ];
                    }
                }
            }
        }

        return $this->getAllNames;
    }

    /**
     * This function should be redefined in derived classes to show any major
     * identifying characteristics of this record.
     *
     * @return string
     */
    public function formatListDetails(): string
    {
        return
            $this->formatFirstMajorFact(Gedcom::MARRIAGE_EVENTS, 1) .
            $this->formatFirstMajorFact(Gedcom::DIVORCE_EVENTS, 1);
    }

    /**
     * Lock the database row, to prevent concurrent edits.
     */
    public function lock(): void
    {
        DB::table('families')
            ->where('f_file', '=', $this->tree->id())
            ->where('f_id', '=', $this->xref())
            ->lockForUpdate()
            ->get();
    }
}
