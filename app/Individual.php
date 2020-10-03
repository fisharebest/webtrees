<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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
use Fisharebest\ExtCalendar\GregorianCalendar;
use Fisharebest\Webtrees\Http\RequestHandlers\IndividualPage;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;

use function preg_match;

/**
 * A GEDCOM individual (INDI) object.
 */
class Individual extends GedcomRecord
{
    public const RECORD_TYPE = 'INDI';

    protected const ROUTE_NAME = IndividualPage::class;

    /** @var int used in some lists to keep track of this individual’s generation in that list */
    public $generation;

    /** @var Date The estimated date of birth */
    private $estimated_birth_date;

    /** @var Date The estimated date of death */
    private $estimated_death_date;

    /**
     * A closure which will create a record from a database row.
     *
     * @deprecated since 2.0.4.  Will be removed in 2.1.0 - Use Factory::individual()
     *
     * @param Tree $tree
     *
     * @return Closure
     */
    public static function rowMapper(Tree $tree): Closure
    {
        return Registry::individualFactory()->mapper($tree);
    }

    /**
     * A closure which will compare individuals by birth date.
     *
     * @return Closure
     */
    public static function birthDateComparator(): Closure
    {
        return static function (Individual $x, Individual $y): int {
            return Date::compare($x->getEstimatedBirthDate(), $y->getEstimatedBirthDate());
        };
    }

    /**
     * A closure which will compare individuals by death date.
     *
     * @return Closure
     */
    public static function deathDateComparator(): Closure
    {
        return static function (Individual $x, Individual $y): int {
            return Date::compare($x->getEstimatedBirthDate(), $y->getEstimatedBirthDate());
        };
    }

    /**
     * Get an instance of an individual object. For single records,
     * we just receive the XREF. For bulk records (such as lists
     * and search results) we can receive the GEDCOM data as well.
     *
     * @deprecated since 2.0.4.  Will be removed in 2.1.0 - Use Factory::individual()
     *
     * @param string      $xref
     * @param Tree        $tree
     * @param string|null $gedcom
     *
     * @return Individual|null
     */
    public static function getInstance(string $xref, Tree $tree, string $gedcom = null): ?Individual
    {
        return Registry::individualFactory()->make($xref, $tree, $gedcom);
    }

    /**
     * Sometimes, we'll know in advance that we need to load a set of records.
     * Typically when we load families and their members.
     *
     * @param Tree     $tree
     * @param string[] $xrefs
     *
     * @return void
     */
    public static function load(Tree $tree, array $xrefs): void
    {
        $rows = DB::table('individuals')
            ->where('i_file', '=', $tree->id())
            ->whereIn('i_id', array_unique($xrefs))
            ->select(['i_id AS xref', 'i_gedcom AS gedcom'])
            ->get();

        foreach ($rows as $row) {
            Registry::individualFactory()->make($row->xref, $tree, $row->gedcom);
        }
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
        $access_level = $access_level ?? Auth::accessLevel($this->tree);

        return $this->tree->getPreference('SHOW_LIVING_NAMES') >= $access_level || $this->canShow($access_level);
    }

    /**
     * Can this individual be shown?
     *
     * @param int $access_level
     *
     * @return bool
     */
    protected function canShowByType(int $access_level): bool
    {
        // Dead people...
        if ($this->tree->getPreference('SHOW_DEAD_PEOPLE') >= $access_level && $this->isDead()) {
            $keep_alive             = false;
            $KEEP_ALIVE_YEARS_BIRTH = (int) $this->tree->getPreference('KEEP_ALIVE_YEARS_BIRTH');
            if ($KEEP_ALIVE_YEARS_BIRTH) {
                preg_match_all('/\n1 (?:' . implode('|', Gedcom::BIRTH_EVENTS) . ').*(?:\n[2-9].*)*(?:\n2 DATE (.+))/', $this->gedcom, $matches, PREG_SET_ORDER);
                foreach ($matches as $match) {
                    $date = new Date($match[1]);
                    if ($date->isOK() && $date->gregorianYear() + $KEEP_ALIVE_YEARS_BIRTH > date('Y')) {
                        $keep_alive = true;
                        break;
                    }
                }
            }
            $KEEP_ALIVE_YEARS_DEATH = (int) $this->tree->getPreference('KEEP_ALIVE_YEARS_DEATH');
            if ($KEEP_ALIVE_YEARS_DEATH) {
                preg_match_all('/\n1 (?:' . implode('|', Gedcom::DEATH_EVENTS) . ').*(?:\n[2-9].*)*(?:\n2 DATE (.+))/', $this->gedcom, $matches, PREG_SET_ORDER);
                foreach ($matches as $match) {
                    $date = new Date($match[1]);
                    if ($date->isOK() && $date->gregorianYear() + $KEEP_ALIVE_YEARS_DEATH > date('Y')) {
                        $keep_alive = true;
                        break;
                    }
                }
            }
            if (!$keep_alive) {
                return true;
            }
        }
        // Consider relationship privacy (unless an admin is applying download restrictions)
        $user_path_length = (int) $this->tree->getUserPreference(Auth::user(), User::PREF_TREE_PATH_LENGTH);
        $gedcomid         = $this->tree->getUserPreference(Auth::user(), User::PREF_TREE_ACCOUNT_XREF);

        if ($gedcomid !== '' && $user_path_length > 0) {
            return self::isRelated($this, $user_path_length);
        }

        // No restriction found - show living people to members only:
        return Auth::PRIV_USER >= $access_level;
    }

    /**
     * For relationship privacy calculations - is this individual a close relative?
     *
     * @param Individual $target
     * @param int        $distance
     *
     * @return bool
     */
    private static function isRelated(Individual $target, $distance): bool
    {
        static $cache = null;

        $user_individual = Registry::individualFactory()->make($target->tree->getUserPreference(Auth::user(), User::PREF_TREE_ACCOUNT_XREF), $target->tree);
        if ($user_individual) {
            if (!$cache) {
                $cache = [
                    0 => [$user_individual],
                    1 => [],
                ];
                foreach ($user_individual->facts(['FAMC', 'FAMS'], false, Auth::PRIV_HIDE) as $fact) {
                    $family = $fact->target();
                    if ($family instanceof Family) {
                        $cache[1][] = $family;
                    }
                }
            }
        } else {
            // No individual linked to this account? Cannot use relationship privacy.
            return true;
        }

        // Double the distance, as we count the INDI-FAM and FAM-INDI links separately
        $distance *= 2;

        // Consider each path length in turn
        for ($n = 0; $n <= $distance; ++$n) {
            if (array_key_exists($n, $cache)) {
                // We have already calculated all records with this length
                if ($n % 2 === 0 && in_array($target, $cache[$n], true)) {
                    return true;
                }
            } else {
                // Need to calculate these paths
                $cache[$n] = [];
                if ($n % 2 === 0) {
                    // Add FAM->INDI links
                    foreach ($cache[$n - 1] as $family) {
                        foreach ($family->facts(['HUSB', 'WIFE', 'CHIL'], false, Auth::PRIV_HIDE) as $fact) {
                            $individual = $fact->target();
                            // Don’t backtrack
                            if ($individual instanceof self && !in_array($individual, $cache[$n - 2], true)) {
                                $cache[$n][] = $individual;
                            }
                        }
                    }
                    if (in_array($target, $cache[$n], true)) {
                        return true;
                    }
                } else {
                    // Add INDI->FAM links
                    foreach ($cache[$n - 1] as $individual) {
                        foreach ($individual->facts(['FAMC', 'FAMS'], false, Auth::PRIV_HIDE) as $fact) {
                            $family = $fact->target();
                            // Don’t backtrack
                            if ($family instanceof Family && !in_array($family, $cache[$n - 2], true)) {
                                $cache[$n][] = $family;
                            }
                        }
                    }
                }
            }
        }

        return false;
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
        $SHOW_PRIVATE_RELATIONSHIPS = (bool) $this->tree->getPreference('SHOW_PRIVATE_RELATIONSHIPS');

        $rec = '0 @' . $this->xref . '@ INDI';
        if ($this->tree->getPreference('SHOW_LIVING_NAMES') >= $access_level) {
            // Show all the NAME tags, including subtags
            foreach ($this->facts(['NAME']) as $fact) {
                $rec .= "\n" . $fact->gedcom();
            }
        }
        // Just show the 1 FAMC/FAMS tag, not any subtags, which may contain private data
        preg_match_all('/\n1 (?:FAMC|FAMS) @(' . Gedcom::REGEX_XREF . ')@/', $this->gedcom, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $rela = Registry::familyFactory()->make($match[1], $this->tree);
            if ($rela && ($SHOW_PRIVATE_RELATIONSHIPS || $rela->canShow($access_level))) {
                $rec .= $match[0];
            }
        }
        // Don’t privatize sex.
        if (preg_match('/\n1 SEX [MFU]/', $this->gedcom, $match)) {
            $rec .= $match[0];
        }

        return $rec;
    }

    /**
     * Calculate whether this individual is living or dead.
     * If not known to be dead, then assume living.
     *
     * @return bool
     */
    public function isDead(): bool
    {
        $MAX_ALIVE_AGE = (int) $this->tree->getPreference('MAX_ALIVE_AGE');
        $today_jd      = Carbon::now()->julianDay();

        // "1 DEAT Y" or "1 DEAT/2 DATE" or "1 DEAT/2 PLAC"
        if (preg_match('/\n1 (?:' . implode('|', Gedcom::DEATH_EVENTS) . ')(?: Y|(?:\n[2-9].+)*\n2 (DATE|PLAC) )/', $this->gedcom)) {
            return true;
        }

        // If any event occured more than $MAX_ALIVE_AGE years ago, then assume the individual is dead
        if (preg_match_all('/\n2 DATE (.+)/', $this->gedcom, $date_matches)) {
            foreach ($date_matches[1] as $date_match) {
                $date = new Date($date_match);
                if ($date->isOK() && $date->maximumJulianDay() <= $today_jd - 365 * $MAX_ALIVE_AGE) {
                    return true;
                }
            }
            // The individual has one or more dated events. All are less than $MAX_ALIVE_AGE years ago.
            // If one of these is a birth, the individual must be alive.
            if (preg_match('/\n1 BIRT(?:\n[2-9].+)*\n2 DATE /', $this->gedcom)) {
                return false;
            }
        }

        // If we found no conclusive dates then check the dates of close relatives.

        // Check parents (birth and adopted)
        foreach ($this->childFamilies(Auth::PRIV_HIDE) as $family) {
            foreach ($family->spouses(Auth::PRIV_HIDE) as $parent) {
                // Assume parents are no more than 45 years older than their children
                preg_match_all('/\n2 DATE (.+)/', $parent->gedcom, $date_matches);
                foreach ($date_matches[1] as $date_match) {
                    $date = new Date($date_match);
                    if ($date->isOK() && $date->maximumJulianDay() <= $today_jd - 365 * ($MAX_ALIVE_AGE + 45)) {
                        return true;
                    }
                }
            }
        }

        // Check spouses
        foreach ($this->spouseFamilies(Auth::PRIV_HIDE) as $family) {
            preg_match_all('/\n2 DATE (.+)/', $family->gedcom, $date_matches);
            foreach ($date_matches[1] as $date_match) {
                $date = new Date($date_match);
                // Assume marriage occurs after age of 10
                if ($date->isOK() && $date->maximumJulianDay() <= $today_jd - 365 * ($MAX_ALIVE_AGE - 10)) {
                    return true;
                }
            }
            // Check spouse dates
            $spouse = $family->spouse($this, Auth::PRIV_HIDE);
            if ($spouse) {
                preg_match_all('/\n2 DATE (.+)/', $spouse->gedcom, $date_matches);
                foreach ($date_matches[1] as $date_match) {
                    $date = new Date($date_match);
                    // Assume max age difference between spouses of 40 years
                    if ($date->isOK() && $date->maximumJulianDay() <= $today_jd - 365 * ($MAX_ALIVE_AGE + 40)) {
                        return true;
                    }
                }
            }
            // Check child dates
            foreach ($family->children(Auth::PRIV_HIDE) as $child) {
                preg_match_all('/\n2 DATE (.+)/', $child->gedcom, $date_matches);
                // Assume children born after age of 15
                foreach ($date_matches[1] as $date_match) {
                    $date = new Date($date_match);
                    if ($date->isOK() && $date->maximumJulianDay() <= $today_jd - 365 * ($MAX_ALIVE_AGE - 15)) {
                        return true;
                    }
                }
                // Check grandchildren
                foreach ($child->spouseFamilies(Auth::PRIV_HIDE) as $child_family) {
                    foreach ($child_family->children(Auth::PRIV_HIDE) as $grandchild) {
                        preg_match_all('/\n2 DATE (.+)/', $grandchild->gedcom, $date_matches);
                        // Assume grandchildren born after age of 30
                        foreach ($date_matches[1] as $date_match) {
                            $date = new Date($date_match);
                            if ($date->isOK() && $date->maximumJulianDay() <= $today_jd - 365 * ($MAX_ALIVE_AGE - 30)) {
                                return true;
                            }
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * Find the highlighted media object for an individual
     *
     * @return MediaFile|null
     */
    public function findHighlightedMediaFile(): ?MediaFile
    {
        $fact = $this->facts(['OBJE'])
            ->first(static function (Fact $fact): bool {
                $media = $fact->target();

                return $media instanceof Media && $media->firstImageFile() instanceof MediaFile;
            });

        if ($fact instanceof Fact && $fact->target() instanceof Media) {
            return $fact->target()->firstImageFile();
        }

        return null;
    }

    /**
     * Display the prefered image for this individual.
     * Use an icon if no image is available.
     *
     * @param int      $width      Pixels
     * @param int      $height     Pixels
     * @param string   $fit        "crop" or "contain"
     * @param string[] $attributes Additional HTML attributes
     *
     * @return string
     */
    public function displayImage($width, $height, $fit, $attributes): string
    {
        $media_file = $this->findHighlightedMediaFile();

        if ($media_file !== null) {
            return $media_file->displayImage($width, $height, $fit, $attributes);
        }

        if ($this->tree->getPreference('USE_SILHOUETTE')) {
            return '<i class="icon-silhouette-' . $this->sex() . '"></i>';
        }

        return '';
    }

    /**
     * Get the date of birth
     *
     * @return Date
     */
    public function getBirthDate(): Date
    {
        foreach ($this->getAllBirthDates() as $date) {
            if ($date->isOK()) {
                return $date;
            }
        }

        return new Date('');
    }

    /**
     * Get the place of birth
     *
     * @return Place
     */
    public function getBirthPlace(): Place
    {
        foreach ($this->getAllBirthPlaces() as $place) {
            return $place;
        }

        return new Place('', $this->tree);
    }

    /**
     * Get the year of birth
     *
     * @return string the year of birth
     */
    public function getBirthYear(): string
    {
        return $this->getBirthDate()->minimumDate()->format('%Y');
    }

    /**
     * Get the date of death
     *
     * @return Date
     */
    public function getDeathDate(): Date
    {
        foreach ($this->getAllDeathDates() as $date) {
            if ($date->isOK()) {
                return $date;
            }
        }

        return new Date('');
    }

    /**
     * Get the place of death
     *
     * @return Place
     */
    public function getDeathPlace(): Place
    {
        foreach ($this->getAllDeathPlaces() as $place) {
            return $place;
        }

        return new Place('', $this->tree);
    }

    /**
     * get the death year
     *
     * @return string the year of death
     */
    public function getDeathYear(): string
    {
        return $this->getDeathDate()->minimumDate()->format('%Y');
    }

    /**
     * Get the range of years in which a individual lived. e.g. “1870–”, “1870–1920”, “–1920”.
     * Provide the place and full date using a tooltip.
     * For consistent layout in charts, etc., show just a “–” when no dates are known.
     * Note that this is a (non-breaking) en-dash, and not a hyphen.
     *
     * @return string
     */
    public function lifespan(): string
    {
        // Just the first part of the place name
        $birth_place = strip_tags($this->getBirthPlace()->shortName());
        $death_place = strip_tags($this->getDeathPlace()->shortName());
        // Remove markup from dates
        $birth_date = strip_tags($this->getBirthDate()->display());
        $death_date = strip_tags($this->getDeathDate()->display());

        /* I18N: A range of years, e.g. “1870–”, “1870–1920”, “–1920” */
        return I18N::translate(
            '%1$s–%2$s',
            '<span title="' . $birth_place . ' ' . $birth_date . '">' . $this->getBirthYear() . '</span>',
            '<span title="' . $death_place . ' ' . $death_date . '">' . $this->getDeathYear() . '</span>'
        );
    }

    /**
     * Get all the birth dates - for the individual lists.
     *
     * @return Date[]
     */
    public function getAllBirthDates(): array
    {
        foreach (Gedcom::BIRTH_EVENTS as $event) {
            $tmp = $this->getAllEventDates([$event]);
            if ($tmp) {
                return $tmp;
            }
        }

        return [];
    }

    /**
     * Gat all the birth places - for the individual lists.
     *
     * @return Place[]
     */
    public function getAllBirthPlaces(): array
    {
        foreach (Gedcom::BIRTH_EVENTS as $event) {
            $places = $this->getAllEventPlaces([$event]);
            if ($places !== []) {
                return $places;
            }
        }

        return [];
    }

    /**
     * Get all the death dates - for the individual lists.
     *
     * @return Date[]
     */
    public function getAllDeathDates(): array
    {
        foreach (Gedcom::DEATH_EVENTS as $event) {
            $tmp = $this->getAllEventDates([$event]);
            if ($tmp) {
                return $tmp;
            }
        }

        return [];
    }

    /**
     * Get all the death places - for the individual lists.
     *
     * @return Place[]
     */
    public function getAllDeathPlaces(): array
    {
        foreach (Gedcom::DEATH_EVENTS as $event) {
            $places = $this->getAllEventPlaces([$event]);
            if ($places !== []) {
                return $places;
            }
        }

        return [];
    }

    /**
     * Generate an estimate for the date of birth, based on dates of parents/children/spouses
     *
     * @return Date
     */
    public function getEstimatedBirthDate(): Date
    {
        if ($this->estimated_birth_date === null) {
            foreach ($this->getAllBirthDates() as $date) {
                if ($date->isOK()) {
                    $this->estimated_birth_date = $date;
                    break;
                }
            }
            if ($this->estimated_birth_date === null) {
                $min = [];
                $max = [];
                $tmp = $this->getDeathDate();
                if ($tmp->isOK()) {
                    $min[] = $tmp->minimumJulianDay() - $this->tree->getPreference('MAX_ALIVE_AGE') * 365;
                    $max[] = $tmp->maximumJulianDay();
                }
                foreach ($this->childFamilies() as $family) {
                    $tmp = $family->getMarriageDate();
                    if ($tmp->isOK()) {
                        $min[] = $tmp->maximumJulianDay() - 365 * 1;
                        $max[] = $tmp->minimumJulianDay() + 365 * 30;
                    }
                    $husband = $family->husband();
                    if ($husband instanceof self) {
                        $tmp = $husband->getBirthDate();
                        if ($tmp->isOK()) {
                            $min[] = $tmp->maximumJulianDay() + 365 * 15;
                            $max[] = $tmp->minimumJulianDay() + 365 * 65;
                        }
                    }
                    $wife = $family->wife();
                    if ($wife instanceof self) {
                        $tmp = $wife->getBirthDate();
                        if ($tmp->isOK()) {
                            $min[] = $tmp->maximumJulianDay() + 365 * 15;
                            $max[] = $tmp->minimumJulianDay() + 365 * 45;
                        }
                    }
                    foreach ($family->children() as $child) {
                        $tmp = $child->getBirthDate();
                        if ($tmp->isOK()) {
                            $min[] = $tmp->maximumJulianDay() - 365 * 30;
                            $max[] = $tmp->minimumJulianDay() + 365 * 30;
                        }
                    }
                }
                foreach ($this->spouseFamilies() as $family) {
                    $tmp = $family->getMarriageDate();
                    if ($tmp->isOK()) {
                        $min[] = $tmp->maximumJulianDay() - 365 * 45;
                        $max[] = $tmp->minimumJulianDay() - 365 * 15;
                    }
                    $spouse = $family->spouse($this);
                    if ($spouse) {
                        $tmp = $spouse->getBirthDate();
                        if ($tmp->isOK()) {
                            $min[] = $tmp->maximumJulianDay() - 365 * 25;
                            $max[] = $tmp->minimumJulianDay() + 365 * 25;
                        }
                    }
                    foreach ($family->children() as $child) {
                        $tmp = $child->getBirthDate();
                        if ($tmp->isOK()) {
                            $min[] = $tmp->maximumJulianDay() - 365 * ($this->sex() === 'F' ? 45 : 65);
                            $max[] = $tmp->minimumJulianDay() - 365 * 15;
                        }
                    }
                }
                if ($min && $max) {
                    $gregorian_calendar = new GregorianCalendar();

                    [$year] = $gregorian_calendar->jdToYmd(intdiv(max($min) + min($max), 2));
                    $this->estimated_birth_date = new Date('EST ' . $year);
                } else {
                    $this->estimated_birth_date = new Date(''); // always return a date object
                }
            }
        }

        return $this->estimated_birth_date;
    }

    /**
     * Generate an estimated date of death.
     *
     * @return Date
     */
    public function getEstimatedDeathDate(): Date
    {
        if ($this->estimated_death_date === null) {
            foreach ($this->getAllDeathDates() as $date) {
                if ($date->isOK()) {
                    $this->estimated_death_date = $date;
                    break;
                }
            }
            if ($this->estimated_death_date === null) {
                if ($this->getEstimatedBirthDate()->minimumJulianDay()) {
                    $max_alive_age              = (int) $this->tree->getPreference('MAX_ALIVE_AGE');
                    $this->estimated_death_date = $this->getEstimatedBirthDate()->addYears($max_alive_age, 'BEF');
                } else {
                    $this->estimated_death_date = new Date(''); // always return a date object
                }
            }
        }

        return $this->estimated_death_date;
    }

    /**
     * Get the sex - M F or U
     * Use the un-privatised gedcom record. We call this function during
     * the privatize-gedcom function, and we are allowed to know this.
     *
     * @return string
     */
    public function sex(): string
    {
        if (preg_match('/\n1 SEX ([MF])/', $this->gedcom . $this->pending, $match)) {
            return $match[1];
        }

        return 'U';
    }

    /**
     * Generate the CSS class to be used for drawing this individual
     *
     * @return string
     */
    public function getBoxStyle(): string
    {
        $tmp = [
            'M' => '',
            'F' => 'F',
            'U' => 'NN',
        ];

        return 'person_box' . $tmp[$this->sex()];
    }

    /**
     * Get a list of this individual’s spouse families
     *
     * @param int|null $access_level
     *
     * @return Collection<Family>
     */
    public function spouseFamilies($access_level = null): Collection
    {
        $access_level = $access_level ?? Auth::accessLevel($this->tree);

        if ($this->tree->getPreference('SHOW_PRIVATE_RELATIONSHIPS') === '1') {
            $access_level = Auth::PRIV_HIDE;
        }

        $families = new Collection();
        foreach ($this->facts(['FAMS'], false, $access_level) as $fact) {
            $family = $fact->target();
            if ($family instanceof Family && $family->canShow($access_level)) {
                $families->push($family);
            }
        }

        return new Collection($families);
    }

    /**
     * Get the current spouse of this individual.
     *
     * Where an individual has multiple spouses, assume they are stored
     * in chronological order, and take the last one found.
     *
     * @return Individual|null
     */
    public function getCurrentSpouse(): ?Individual
    {
        $family = $this->spouseFamilies()->last();

        if ($family instanceof Family) {
            return $family->spouse($this);
        }

        return null;
    }

    /**
     * Count the children belonging to this individual.
     *
     * @return int
     */
    public function numberOfChildren(): int
    {
        if (preg_match('/\n1 NCHI (\d+)(?:\n|$)/', $this->gedcom(), $match)) {
            return (int) $match[1];
        }

        $children = [];
        foreach ($this->spouseFamilies() as $fam) {
            foreach ($fam->children() as $child) {
                $children[$child->xref()] = true;
            }
        }

        return count($children);
    }

    /**
     * Get a list of this individual’s child families (i.e. their parents).
     *
     * @param int|null $access_level
     *
     * @return Collection<Family>
     */
    public function childFamilies($access_level = null): Collection
    {
        $access_level = $access_level ?? Auth::accessLevel($this->tree);

        if ($this->tree->getPreference('SHOW_PRIVATE_RELATIONSHIPS') === '1') {
            $access_level = Auth::PRIV_HIDE;
        }

        $families = new Collection();

        foreach ($this->facts(['FAMC'], false, $access_level) as $fact) {
            $family = $fact->target();
            if ($family instanceof Family && $family->canShow($access_level)) {
                $families->push($family);
            }
        }

        return $families;
    }

    /**
     * Get a list of step-parent families.
     *
     * @return Collection<Family>
     */
    public function childStepFamilies(): Collection
    {
        $step_families = new Collection();
        $families      = $this->childFamilies();
        foreach ($families as $family) {
            foreach ($family->spouses() as $parent) {
                foreach ($parent->spouseFamilies() as $step_family) {
                    if (!$families->containsStrict($step_family)) {
                        $step_families->add($step_family);
                    }
                }
            }
        }

        return $step_families->uniqueStrict(static function (Family $family): string {
            return $family->xref();
        });
    }

    /**
     * Get a list of step-parent families.
     *
     * @return Collection<Family>
     */
    public function spouseStepFamilies(): Collection
    {
        $step_families = [];
        $families      = $this->spouseFamilies();

        foreach ($families as $family) {
            $spouse = $family->spouse($this);

            if ($spouse instanceof Individual) {
                foreach ($family->spouse($this)->spouseFamilies() as $step_family) {
                    if (!$families->containsStrict($step_family)) {
                        $step_families[] = $step_family;
                    }
                }
            }
        }

        return new Collection($step_families);
    }

    /**
     * A label for a parental family group
     *
     * @param Family $family
     *
     * @return string
     */
    public function getChildFamilyLabel(Family $family): string
    {
        preg_match('/\n1 FAMC @' . $family->xref() . '@(?:\n[2-9].*)*\n2 PEDI (.+)/', $this->gedcom(), $match);

        $values = [
            'birth'   => I18N::translate('Family with parents'),
            'adopted' => I18N::translate('Family with adoptive parents'),
            'foster'  => I18N::translate('Family with foster parents'),
            'sealing' => /* I18N: “sealing” is a Mormon ceremony. */
                I18N::translate('Family with sealing parents'),
            'rada'    => /* I18N: “rada” is an Arabic word, pronounced “ra DAH”. It is child-to-parent pedigree, established by wet-nursing. */
                I18N::translate('Family with rada parents'),
        ];

        return $values[$match[1] ?? 'birth'] ?? $values['birth'];
    }

    /**
     * Create a label for a step family
     *
     * @param Family $step_family
     *
     * @return string
     */
    public function getStepFamilyLabel(Family $step_family): string
    {
        foreach ($this->childFamilies() as $family) {
            if ($family !== $step_family) {
                // Must be a step-family
                foreach ($family->spouses() as $parent) {
                    foreach ($step_family->spouses() as $step_parent) {
                        if ($parent === $step_parent) {
                            // One common parent - must be a step family
                            if ($parent->sex() === 'M') {
                                // Father’s family with someone else
                                if ($step_family->spouse($step_parent)) {
                                    /* I18N: A step-family. %s is an individual’s name */
                                    return I18N::translate('Father’s family with %s', $step_family->spouse($step_parent)->fullName());
                                }

                                /* I18N: A step-family. */
                                return I18N::translate('Father’s family with an unknown individual');
                            }

                            // Mother’s family with someone else
                            if ($step_family->spouse($step_parent)) {
                                /* I18N: A step-family. %s is an individual’s name */
                                return I18N::translate('Mother’s family with %s', $step_family->spouse($step_parent)->fullName());
                            }

                            /* I18N: A step-family. */
                            return I18N::translate('Mother’s family with an unknown individual');
                        }
                    }
                }
            }
        }

        // Perahps same parents - but a different family record?
        return I18N::translate('Family with parents');
    }

    /**
     * Get the description for the family.
     *
     * For example, "XXX's family with new wife".
     *
     * @param Family $family
     *
     * @return string
     */
    public function getSpouseFamilyLabel(Family $family): string
    {
        $spouse = $family->spouse($this);
        if ($spouse) {
            /* I18N: %s is the spouse name */
            return I18N::translate('Family with %s', $spouse->fullName());
        }

        return $family->fullName();
    }

    /**
     * If this object has no name, what do we call it?
     *
     * @return string
     */
    public function getFallBackName(): string
    {
        return '@P.N. /@N.N./';
    }

    /**
     * Convert a name record into ‘full’ and ‘sort’ versions.
     * Use the NAME field to generate the ‘full’ version, as the
     * gedcom spec says that this is the individual’s name, as they would write it.
     * Use the SURN field to generate the sortable names. Note that this field
     * may also be used for the ‘true’ surname, perhaps spelt differently to that
     * recorded in the NAME field. e.g.
     *
     * 1 NAME Robert /de Gliderow/
     * 2 GIVN Robert
     * 2 SPFX de
     * 2 SURN CLITHEROW
     * 2 NICK The Bald
     *
     * full=>'Robert de Gliderow 'The Bald''
     * sort=>'CLITHEROW, ROBERT'
     *
     * Handle multiple surnames, either as;
     *
     * 1 NAME Carlos /Vasquez/ y /Sante/
     * or
     * 1 NAME Carlos /Vasquez y Sante/
     * 2 GIVN Carlos
     * 2 SURN Vasquez,Sante
     *
     * @param string $type
     * @param string $full
     * @param string $gedcom
     *
     * @return void
     */
    protected function addName(string $type, string $full, string $gedcom): void
    {
        ////////////////////////////////////////////////////////////////////////////
        // Extract the structured name parts - use for "sortable" names and indexes
        ////////////////////////////////////////////////////////////////////////////

        $sublevel = 1 + (int) substr($gedcom, 0, 1);
        $GIVN     = preg_match("/\n{$sublevel} GIVN (.+)/", $gedcom, $match) ? $match[1] : '';
        $SURN     = preg_match("/\n{$sublevel} SURN (.+)/", $gedcom, $match) ? $match[1] : '';

        // SURN is an comma-separated list of surnames...
        if ($SURN !== '') {
            $SURNS = preg_split('/ *, */', $SURN);
        } else {
            $SURNS = [];
        }

        // ...so is GIVN - but nobody uses it like that
        $GIVN = str_replace('/ *, */', ' ', $GIVN);

        ////////////////////////////////////////////////////////////////////////////
        // Extract the components from NAME - use for the "full" names
        ////////////////////////////////////////////////////////////////////////////

        // Fix bad slashes. e.g. 'John/Smith' => 'John/Smith/'
        if (substr_count($full, '/') % 2 === 1) {
            $full .= '/';
        }

        // GEDCOM uses "//" to indicate an unknown surname
        $full = preg_replace('/\/\//', '/@N.N./', $full);

        // Extract the surname.
        // Note, there may be multiple surnames, e.g. Jean /Vasquez/ y /Cortes/
        if (preg_match('/\/.*\//', $full, $match)) {
            $surname = str_replace('/', '', $match[0]);
        } else {
            $surname = '';
        }

        // If we don’t have a SURN record, extract it from the NAME
        if (!$SURNS) {
            if (preg_match_all('/\/([^\/]*)\//', $full, $matches)) {
                // There can be many surnames, each wrapped with '/'
                $SURNS = $matches[1];
                foreach ($SURNS as $n => $SURN) {
                    // Remove surname prefixes, such as "van de ", "d'" and "'t " (lower case only)
                    $SURNS[$n] = preg_replace('/^(?:[a-z]+ |[a-z]+\' ?|\'[a-z]+ )+/', '', $SURN);
                }
            } else {
                // It is valid not to have a surname at all
                $SURNS = [''];
            }
        }

        // If we don’t have a GIVN record, extract it from the NAME
        if (!$GIVN) {
            $GIVN = preg_replace(
                [
                    '/ ?\/.*\/ ?/',
                    // remove surname
                    '/ ?".+"/',
                    // remove nickname
                    '/ {2,}/',
                    // multiple spaces, caused by the above
                    '/^ | $/',
                    // leading/trailing spaces, caused by the above
                ],
                [
                    ' ',
                    ' ',
                    ' ',
                    '',
                ],
                $full
            );
        }

        // Add placeholder for unknown given name
        if (!$GIVN) {
            $GIVN = '@P.N.';
            $pos  = (int) strpos($full, '/');
            $full = substr($full, 0, $pos) . '@P.N. ' . substr($full, $pos);
        }

        // Remove slashes - they don’t get displayed
        // $fullNN keeps the @N.N. placeholders, for the database
        // $full is for display on-screen
        $fullNN = str_replace('/', '', $full);

        // Insert placeholders for any missing/unknown names
        $full = str_replace('@N.N.', I18N::translateContext('Unknown surname', '…'), $full);
        $full = str_replace('@P.N.', I18N::translateContext('Unknown given name', '…'), $full);
        // Format for display
        $full = '<span class="NAME" dir="auto" translate="no">' . preg_replace('/\/([^\/]*)\//', '<span class="SURN">$1</span>', e($full)) . '</span>';
        // Localise quotation marks around the nickname
        $full = preg_replace_callback('/&quot;([^&]*)&quot;/', static function (array $matches): string {
            return '<q class="wt-nickname">' . $matches[1] . '</q>';
        }, $full);

        // A suffix of “*” indicates a preferred name
        $full = preg_replace('/([^ >]*)\*/', '<span class="starredname">\\1</span>', $full);

        // Remove prefered-name indicater - they don’t go in the database
        $GIVN   = str_replace('*', '', $GIVN);
        $fullNN = str_replace('*', '', $fullNN);

        foreach ($SURNS as $SURN) {
            // Scottish 'Mc and Mac ' prefixes both sort under 'Mac'
            if (strcasecmp(substr($SURN, 0, 2), 'Mc') === 0) {
                $SURN = substr_replace($SURN, 'Mac', 0, 2);
            } elseif (strcasecmp(substr($SURN, 0, 4), 'Mac ') === 0) {
                $SURN = substr_replace($SURN, 'Mac', 0, 4);
            }

            $this->getAllNames[] = [
                'type'    => $type,
                'sort'    => $SURN . ',' . $GIVN,
                'full'    => $full,
                // This is used for display
                'fullNN'  => $fullNN,
                // This goes into the database
                'surname' => $surname,
                // This goes into the database
                'givn'    => $GIVN,
                // This goes into the database
                'surn'    => $SURN,
                // This goes into the database
            ];
        }
    }

    /**
     * Extract names from the GEDCOM record.
     *
     * @return void
     */
    public function extractNames(): void
    {
        $access_level = $this->canShowName() ? Auth::PRIV_HIDE : Auth::accessLevel($this->tree);

        $this->extractNamesFromFacts(
            1,
            'NAME',
            $this->facts(['NAME'], false, $access_level)
        );
    }

    /**
     * Extra info to display when displaying this record in a list of
     * selection items or favorites.
     *
     * @return string
     */
    public function formatListDetails(): string
    {
        return
            $this->formatFirstMajorFact(Gedcom::BIRTH_EVENTS, 1) .
            $this->formatFirstMajorFact(Gedcom::DEATH_EVENTS, 1);
    }
}
