<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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

/**
 * A GEDCOM family (FAM) object.
 */
class Family extends GedcomRecord
{
    const RECORD_TYPE = 'FAM';
    const ROUTE_NAME  = 'family';

    /** @var Individual|null The husband (or first spouse for same-sex couples) */
    private $husb;

    /** @var Individual|null The wife (or second spouse for same-sex couples) */
    private $wife;

    /**
     * Create a GedcomRecord object from raw GEDCOM data.
     *
     * @param string      $xref
     * @param string      $gedcom  an empty string for new/pending records
     * @param string|null $pending null for a record with no pending edits,
     *                             empty string for records with pending deletions
     * @param Tree        $tree
     */
    public function __construct(string $xref, string $gedcom, $pending, Tree $tree)
    {
        parent::__construct($xref, $gedcom, $pending, $tree);

        // Fetch family members
        if (preg_match_all('/^1 (?:HUSB|WIFE|CHIL) @(.+)@/m', $gedcom . $pending, $match)) {
            Individual::load($tree, $match[1]);
        }

        if (preg_match('/^1 HUSB @(.+)@/m', $gedcom . $pending, $match)) {
            $this->husb = Individual::getInstance($match[1], $tree);
        }
        if (preg_match('/^1 WIFE @(.+)@/m', $gedcom . $pending, $match)) {
            $this->wife = Individual::getInstance($match[1], $tree);
        }

        // Make sure husb/wife are the right way round.
        if ($this->husb && $this->husb->getSex() === 'F' || $this->wife && $this->wife->getSex() === 'M') {
            list($this->husb, $this->wife) = [
                $this->wife,
                $this->husb,
            ];
        }
    }

    /**
     * Get an instance of a family object. For single records,
     * we just receive the XREF. For bulk records (such as lists
     * and search results) we can receive the GEDCOM data as well.
     *
     * @param string      $xref
     * @param Tree        $tree
     * @param string|null $gedcom
     *
     * @throws \Exception
     *
     * @return Family|null
     */
    public static function getInstance(string $xref, Tree $tree, string $gedcom = null)
    {
        $record = parent::getInstance($xref, $tree, $gedcom);

        if ($record instanceof Family) {
            return $record;
        }

        return null;
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
        $SHOW_PRIVATE_RELATIONSHIPS = $this->tree->getPreference('SHOW_PRIVATE_RELATIONSHIPS');

        $rec = '0 @' . $this->xref . '@ FAM';
        // Just show the 1 CHIL/HUSB/WIFE tag, not any subtags, which may contain private data
        preg_match_all('/\n1 (?:CHIL|HUSB|WIFE) @(' . WT_REGEX_XREF . ')@/', $this->gedcom, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $rela = Individual::getInstance($match[1], $this->tree);
            if ($rela && ($SHOW_PRIVATE_RELATIONSHIPS || $rela->canShow($access_level))) {
                $rec .= $match[0];
            }
        }

        return $rec;
    }

    /**
     * Fetch data from the database
     *
     * @param string $xref
     * @param int    $tree_id
     *
     * @return null|string
     */
    protected static function fetchGedcomRecord(string $xref, int $tree_id)
    {
        return Database::prepare(
            "SELECT f_gedcom FROM `##families` WHERE f_id = :xref AND f_file = :tree_id"
        )->execute([
            'xref'    => $xref,
            'tree_id' => $tree_id,
        ])->fetchOne();
    }

    /**
     * Get the male (or first female) partner of the family
     *
     * @param int|null $access_level
     *
     * @return Individual|null
     */
    public function getHusband($access_level = null)
    {
        $SHOW_PRIVATE_RELATIONSHIPS = $this->tree->getPreference('SHOW_PRIVATE_RELATIONSHIPS');

        if ($this->husb && ($SHOW_PRIVATE_RELATIONSHIPS || $this->husb->canShowName($access_level))) {
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
    public function getWife($access_level = null)
    {
        $SHOW_PRIVATE_RELATIONSHIPS = $this->tree->getPreference('SHOW_PRIVATE_RELATIONSHIPS');

        if ($this->wife && ($SHOW_PRIVATE_RELATIONSHIPS || $this->wife->canShowName($access_level))) {
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
        preg_match_all('/\n1 (?:CHIL|HUSB|WIFE) @(' . WT_REGEX_XREF . ')@/', $this->gedcom, $matches);
        foreach ($matches[1] as $match) {
            $person = Individual::getInstance($match, $this->tree);
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
    public function getSpouse(Individual $person, $access_level = null)
    {
        if ($person === $this->wife) {
            return $this->getHusband($access_level);
        }

        return $this->getWife($access_level);
    }

    /**
     * Get the (zero, one or two) spouses from this family.
     *
     * @param int|null $access_level
     *
     * @return Individual[]
     */
    public function getSpouses($access_level = null): array
    {
        return array_filter([
            $this->getHusband($access_level),
            $this->getWife($access_level),
        ]);
    }

    /**
     * Get a list of this family’s children.
     *
     * @param int|null $access_level
     *
     * @return Individual[]
     */
    public function getChildren($access_level = null): array
    {
        if ($access_level === null) {
            $access_level = Auth::accessLevel($this->tree);
        }

        $SHOW_PRIVATE_RELATIONSHIPS = (bool) $this->tree->getPreference('SHOW_PRIVATE_RELATIONSHIPS');

        $children = [];
        foreach ($this->getFacts('CHIL', false, $access_level, $SHOW_PRIVATE_RELATIONSHIPS) as $fact) {
            $child = $fact->getTarget();
            if ($child && ($SHOW_PRIVATE_RELATIONSHIPS || $child->canShowName($access_level))) {
                $children[] = $child;
            }
        }

        return $children;
    }

    /**
     * Static helper function to sort an array of families by marriage date
     *
     * @param Family $x
     * @param Family $y
     *
     * @return int
     */
    public static function compareMarrDate(Family $x, Family $y): int
    {
        return Date::compare($x->getMarriageDate(), $y->getMarriageDate());
    }

    /**
     * Number of children - for the individual list
     *
     * @return int
     */
    public function getNumberOfChildren(): int
    {
        $nchi = count($this->getChildren());
        foreach ($this->getFacts('NCHI') as $fact) {
            $nchi = max($nchi, (int) $fact->getValue());
        }

        return $nchi;
    }

    /**
     * get the marriage event
     *
     * @return Fact|null
     */
    public function getMarriage()
    {
        return $this->getFirstFact('MARR');
    }

    /**
     * Get marriage date
     *
     * @return Date
     */
    public function getMarriageDate()
    {
        $marriage = $this->getMarriage();
        if ($marriage) {
            return $marriage->getDate();
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
        return $this->getMarriageDate()->minimumDate()->y;
    }

    /**
     * Get the marriage place
     *
     * @return Place
     */
    public function getMarriagePlace(): Place
    {
        $marriage = $this->getMarriage();

        return $marriage->getPlace();
    }

    /**
     * Get a list of all marriage dates - for the family lists.
     *
     * @return Date[]
     */
    public function getAllMarriageDates(): array
    {
        foreach (explode('|', WT_EVENTS_MARR) as $event) {
            if ($array = $this->getAllEventDates($event)) {
                return $array;
            }
        }

        return [];
    }

    /**
     * Get a list of all marriage places - for the family lists.
     *
     * @return Place[]
     */
    public function getAllMarriagePlaces(): array
    {
        foreach (explode('|', WT_EVENTS_MARR) as $event) {
            $places = $this->getAllEventPlaces($event);
            if (!empty($places)) {
                return $places;
            }
        }

        return [];
    }

    /**
     * Derived classes should redefine this function, otherwise the object will have no name
     *
     * @return string[][]
     */
    public function getAllNames(): array
    {
        if ($this->getAllNames === null) {
            // Check the script used by each name, so we can match cyrillic with cyrillic, greek with greek, etc.
            $husb_names = [];
            if ($this->husb) {
                $husb_names = array_filter($this->husb->getAllNames(), function (array $x): bool {
                    return $x['type'] !== '_MARNM';
                });
            }
            // If the individual only has married names, create a dummy birth name.
            if (empty($husb_names)) {
                $husb_names[] = [
                    'type' => 'BIRT',
                    'sort' => '@N.N.',
                    'full' => I18N::translateContext('Unknown given name', '…') . ' ' . I18N::translateContext('Unknown surname', '…'),
                ];
            }
            foreach ($husb_names as $n => $husb_name) {
                $husb_names[$n]['script'] = I18N::textScript($husb_name['full']);
            }

            $wife_names = [];
            if ($this->wife) {
                $wife_names = array_filter($this->wife->getAllNames(), function (array $x): bool {
                    return $x['type'] !== '_MARNM';
                });
            }
            // If the individual only has married names, create a dummy birth name.
            if (empty($wife_names)) {
                $wife_names[] = [
                    'type' => 'BIRT',
                    'sort' => '@N.N.',
                    'full' => I18N::translateContext('Unknown given name', '…') . ' ' . I18N::translateContext('Unknown surname', '…'),
                ];
            }
            foreach ($wife_names as $n => $wife_name) {
                $wife_names[$n]['script'] = I18N::textScript($wife_name['full']);
            }

            // Add the matched names first
            foreach ($husb_names as $husb_name) {
                foreach ($wife_names as $wife_name) {
                    if ($husb_name['script'] == $wife_name['script']) {
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
                    if ($husb_name['script'] != $wife_name['script']) {
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
            $this->formatFirstMajorFact(WT_EVENTS_MARR, 1) .
            $this->formatFirstMajorFact(WT_EVENTS_DIV, 1);
    }
}
