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

namespace Fisharebest\Webtrees\Services;

use Closure;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Exceptions\InternalServerErrorException;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Place;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Soundex;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use stdClass;
use function mb_stripos;

/**
 * Search trees for genealogy records.
 */
class SearchService
{
    /**
     * @param Tree[]   $trees
     * @param string[] $search
     *
     * @return Collection
     */
    public function searchFamilies(array $trees, array $search): Collection
    {
        $query = DB::table('families');

        $this->whereTrees($query, 'f_file', $trees);
        $this->whereSearch($query, 'f_gedcom', $search);

        return $query
            ->get()
            ->each($this->rowLimiter())
            ->map(Family::rowMapper())
            ->filter(GedcomRecord::accessFilter())
            ->filter($this->rawGedcomFilter($search));
    }

    /**
     * Search for families by name.
     *
     * @param Tree[]   $trees
     * @param string[] $search
     * @param int      $offset
     * @param int      $limit
     *
     * @return Collection
     */
    public function searchFamilyNames(array $trees, array $search, int $offset = 0, int $limit = PHP_INT_MAX): Collection
    {
        $query = DB::table('families')
            ->leftJoin('name AS husb_name', static function (JoinClause $join): void {
                $join
                    ->on('husb_name.n_file', '=', 'families.f_file')
                    ->on('husb_name.n_id', '=', 'families.f_husb')
                    ->where('husb_name.n_type', '<>', '_MARNM');
            })
            ->leftJoin('name AS wife_name', static function (JoinClause $join): void {
                $join
                    ->on('wife_name.n_file', '=', 'families.f_file')
                    ->on('wife_name.n_id', '=', 'families.f_wife')
                    ->where('wife_name.n_type', '<>', '_MARNM');
            });

        $prefix = DB::connection()->getTablePrefix();
        $field  = new Expression('COALESCE(' . $prefix . "husb_name.n_full, '') || COALESCE(" . $prefix . "wife_name.n_full, '')");

        $this->whereTrees($query, 'f_file', $trees);
        $this->whereSearch($query, $field, $search);

        $query
            ->orderBy('husb_name.n_sort')
            ->orderBy('wife_name.n_sort')
            ->select(['families.*', 'husb_name.n_sort', 'wife_name.n_sort'])
            ->distinct();

        return $this->paginateQuery($query, Family::rowMapper(), GedcomRecord::accessFilter(), $offset, $limit);
    }

    /**
     * @param Tree[]   $trees
     * @param string[] $search
     *
     * @return Collection
     */
    public function searchIndividuals(array $trees, array $search): Collection
    {
        $query = DB::table('individuals');

        $this->whereTrees($query, 'i_file', $trees);
        $this->whereSearch($query, 'i_gedcom', $search);

        return $query
            ->get()
            ->each($this->rowLimiter())
            ->map(Individual::rowMapper())
            ->filter(GedcomRecord::accessFilter())
            ->filter($this->rawGedcomFilter($search));
    }

    /**
     * Search for individuals by name.
     *
     * @param Tree[]   $trees
     * @param string[] $search
     * @param int      $offset
     * @param int      $limit
     *
     * @return Collection
     */
    public function searchIndividualNames(array $trees, array $search, int $offset = 0, int $limit = PHP_INT_MAX): Collection
    {
        $query = DB::table('individuals')
            ->join('name', static function (JoinClause $join): void {
                $join
                    ->on('name.n_file', '=', 'individuals.i_file')
                    ->on('name.n_id', '=', 'individuals.i_id');
            })
            ->orderBy('n_sort')
            ->select(['individuals.*', 'n_num']);

        $this->whereTrees($query, 'i_file', $trees);
        $this->whereSearch($query, 'n_full', $search);

        return $this->paginateQuery($query, Individual::rowMapper(), GedcomRecord::accessFilter(), $offset, $limit);
    }

    /**
     * Search for media objects.
     *
     * @param Tree[]   $trees
     * @param string[] $search
     * @param int      $offset
     * @param int      $limit
     *
     * @return Collection
     */
    public function searchMedia(array $trees, array $search, int $offset = 0, int $limit = PHP_INT_MAX): Collection
    {
        $query = DB::table('media');

        $this->whereTrees($query, 'media.m_file', $trees);
        $this->whereSearch($query, 'm_gedcom', $search);

        return $this->paginateQuery($query, Media::rowMapper(), GedcomRecord::accessFilter(), $offset, $limit);
    }

    /**
     * Search for notes.
     *
     * @param Tree[]   $trees
     * @param string[] $search
     * @param int      $offset
     * @param int      $limit
     *
     * @return Collection
     */
    public function searchNotes(array $trees, array $search, int $offset = 0, int $limit = PHP_INT_MAX): Collection
    {
        $query = DB::table('other')
            ->where('o_type', '=', 'NOTE');

        $this->whereTrees($query, 'o_file', $trees);
        $this->whereSearch($query, 'o_gedcom', $search);

        return $this->paginateQuery($query, Note::rowMapper(), GedcomRecord::accessFilter(), $offset, $limit);
    }

    /**
     * Search for repositories.
     *
     * @param Tree[]   $trees
     * @param string[] $search
     * @param int      $offset
     * @param int      $limit
     *
     * @return Collection
     */
    public function searchRepositories(array $trees, array $search, int $offset = 0, int $limit = PHP_INT_MAX): Collection
    {
        $query = DB::table('other')
            ->where('o_type', '=', 'REPO');

        $this->whereTrees($query, 'o_file', $trees);
        $this->whereSearch($query, 'o_gedcom', $search);

        return $this->paginateQuery($query, Repository::rowMapper(), GedcomRecord::accessFilter(), $offset, $limit);
    }

    /**
     * Search for sources.
     *
     * @param Tree[]   $trees
     * @param string[] $search
     * @param int      $offset
     * @param int      $limit
     *
     * @return Collection
     */
    public function searchSources(array $trees, array $search, int $offset = 0, int $limit = PHP_INT_MAX): Collection
    {
        $query = DB::table('sources');

        $this->whereTrees($query, 's_file', $trees);
        $this->whereSearch($query, 's_gedcom', $search);

        return $this->paginateQuery($query, Source::rowMapper(), GedcomRecord::accessFilter(), $offset, $limit);
    }

    /**
     * Search for sources by name.
     *
     * @param Tree[]   $trees
     * @param string[] $search
     * @param int      $offset
     * @param int      $limit
     *
     * @return Collection
     */
    public function searchSourcesByName(array $trees, array $search, int $offset = 0, int $limit = PHP_INT_MAX): Collection
    {
        $query = DB::table('sources')
            ->orderBy('s_name');

        $this->whereTrees($query, 's_file', $trees);
        $this->whereSearch($query, 's_name', $search);

        return $this->paginateQuery($query, Source::rowMapper(), GedcomRecord::accessFilter(), $offset, $limit);
    }

    /**
     * Search for submitters.
     *
     * @param Tree[]   $trees
     * @param string[] $search
     * @param int      $offset
     * @param int      $limit
     *
     * @return Collection
     */
    public function searchSubmitters(array $trees, array $search, int $offset = 0, int $limit = PHP_INT_MAX): Collection
    {
        $query = DB::table('other')
            ->where('o_type', '=', 'SUBM');

        $this->whereTrees($query, 'o_file', $trees);
        $this->whereSearch($query, 'o_gedcom', $search);

        return $this->paginateQuery($query, GedcomRecord::rowMapper(), GedcomRecord::accessFilter(), $offset, $limit);
    }

    /**
     * Search for places.
     *
     * @param Tree   $tree
     * @param string $search
     * @param int    $offset
     * @param int    $limit
     *
     * @return Collection
     */
    public function searchPlaces(Tree $tree, string $search, int $offset = 0, int $limit = PHP_INT_MAX): Collection
    {
        $query = DB::table('places AS p0')
            ->where('p0.p_file', '=', $tree->id())
            ->leftJoin('places AS p1', 'p1.p_id', '=', 'p0.p_parent_id')
            ->leftJoin('places AS p2', 'p2.p_id', '=', 'p1.p_parent_id')
            ->leftJoin('places AS p3', 'p3.p_id', '=', 'p2.p_parent_id')
            ->leftJoin('places AS p4', 'p4.p_id', '=', 'p3.p_parent_id')
            ->leftJoin('places AS p5', 'p5.p_id', '=', 'p4.p_parent_id')
            ->leftJoin('places AS p6', 'p6.p_id', '=', 'p5.p_parent_id')
            ->leftJoin('places AS p7', 'p7.p_id', '=', 'p6.p_parent_id')
            ->leftJoin('places AS p8', 'p8.p_id', '=', 'p7.p_parent_id')
            ->orderBy('p0.p_place')
            ->orderBy('p1.p_place')
            ->orderBy('p2.p_place')
            ->orderBy('p3.p_place')
            ->orderBy('p4.p_place')
            ->orderBy('p5.p_place')
            ->orderBy('p6.p_place')
            ->orderBy('p7.p_place')
            ->orderBy('p8.p_place')
            ->select([
                'p0.p_place AS place0',
                'p1.p_place AS place1',
                'p2.p_place AS place2',
                'p3.p_place AS place3',
                'p4.p_place AS place4',
                'p5.p_place AS place5',
                'p6.p_place AS place6',
                'p7.p_place AS place7',
                'p8.p_place AS place8',
            ]);

        // Filter each level of the hierarchy.
        foreach (explode(',', $search, 9) as $level => $string) {
            $query->whereContains('p' . $level . '.p_place', $string);
        }

        $row_mapper = static function (stdClass $row) use ($tree): Place {
            $place = implode(', ', array_filter((array) $row));

            return new Place($place, $tree);
        };

        $filter = static function (): bool {
            return true;
        };

        return $this->paginateQuery($query, $row_mapper, $filter, $offset, $limit);
    }

    /**
     * @param Tree[]   $trees
     * @param string[] $fields
     * @param string[] $modifiers
     *
     * @return Collection
     */
    public function searchIndividualsAdvanced(array $trees, array $fields, array $modifiers): Collection
    {
        $fields = array_filter($fields);

        $query = DB::table('individuals')
            ->select(['individuals.*'])
            ->distinct();

        $this->whereTrees($query, 'i_file', $trees);

        // Join the following tables
        $father_name   = false;
        $mother_name   = false;
        $spouse_family = false;
        $indi_name     = false;
        $indi_date     = false;
        $fam_date      = false;
        $indi_plac     = false;
        $fam_plac      = false;

        foreach ($fields as $field_name => $field_value) {
            if ($field_value !== '') {
                if (substr($field_name, 0, 14) === 'FAMC:HUSB:NAME') {
                    $father_name = true;
                } elseif (substr($field_name, 0, 14) === 'FAMC:WIFE:NAME') {
                    $mother_name = true;
                } elseif (substr($field_name, 0, 4) === 'NAME') {
                    $indi_name = true;
                } elseif (strpos($field_name, ':DATE') !== false) {
                    if (substr($field_name, 0, 4) === 'FAMS') {
                        $fam_date      = true;
                        $spouse_family = true;
                    } else {
                        $indi_date = true;
                    }
                } elseif (strpos($field_name, ':PLAC') !== false) {
                    if (substr($field_name, 0, 4) === 'FAMS') {
                        $fam_plac      = true;
                        $spouse_family = true;
                    } else {
                        $indi_plac = true;
                    }
                } elseif ($field_name === 'FAMS:NOTE') {
                    $spouse_family = true;
                }
            }
        }

        if ($father_name || $mother_name) {
            $query->join('link AS l1', static function (JoinClause $join): void {
                $join
                    ->on('l1.l_file', '=', 'individuals.i_file')
                    ->on('l1.l_from', '=', 'individuals.i_id')
                    ->where('l1.l_type', '=', 'FAMC');
            });

            if ($father_name) {
                $query->join('link AS l2', static function (JoinClause $join): void {
                    $join
                        ->on('l2.l_file', '=', 'l1.l_file')
                        ->on('l2.l_from', '=', 'l1.l_to')
                        ->where('l2.l_type', '=', 'HUSB');
                });
                $query->join('name AS father_name', static function (JoinClause $join): void {
                    $join
                        ->on('father_name.n_file', '=', 'l2.l_file')
                        ->on('father_name.n_id', '=', 'l2.l_to');
                });
            }

            if ($mother_name) {
                $query->join('link AS l3', static function (JoinClause $join): void {
                    $join
                        ->on('l3.l_file', '=', 'l1.l_file')
                        ->on('l3.l_from', '=', 'l1.l_to')
                        ->where('l3.l_type', '=', 'WIFE');
                });
                $query->join('name AS mother_name', static function (JoinClause $join): void {
                    $join
                        ->on('mother_name.n_file', '=', 'l3.l_file')
                        ->on('mother_name.n_id', '=', 'l3.l_to');
                });
            }
        }

        if ($spouse_family) {
            $query->join('link AS l4', static function (JoinClause $join): void {
                $join
                    ->on('l4.l_file', '=', 'individuals.i_file')
                    ->on('l4.l_from', '=', 'individuals.i_id')
                    ->where('l4.l_type', '=', 'FAMS');
            });
            $query->join('families AS spouse_families', static function (JoinClause $join): void {
                $join
                    ->on('spouse_families.f_file', '=', 'l4.l_file')
                    ->on('spouse_families.f_id', '=', 'l4.l_to');
            });
        }

        if ($indi_name) {
            $query->join('name AS individual_name', static function (JoinClause $join): void {
                $join
                    ->on('individual_name.n_file', '=', 'individuals.i_file')
                    ->on('individual_name.n_id', '=', 'individuals.i_id');
            });
        }

        if ($indi_date) {
            $query->join('dates AS individual_dates', static function (JoinClause $join): void {
                $join
                    ->on('individual_dates.d_file', '=', 'individuals.i_file')
                    ->on('individual_dates.d_gid', '=', 'individuals.i_id');
            });
        }

        if ($fam_date) {
            $query->join('dates AS family_dates', static function (JoinClause $join): void {
                $join
                    ->on('family_dates.d_file', '=', 'spouse_families.f_file')
                    ->on('family_dates.d_gid', '=', 'spouse_families.f_id');
            });
        }

        if ($indi_plac) {
            $query->join('placelinks AS individual_placelinks', static function (JoinClause $join): void {
                $join
                    ->on('individual_placelinks.pl_file', '=', 'individuals.i_file')
                    ->on('individual_placelinks.pl_gid', '=', 'individuals.i_id');
            });
            $query->join('places AS individual_places', static function (JoinClause $join): void {
                $join
                    ->on('individual_places.p_file', '=', 'individual_placelinks.pl_file')
                    ->on('individual_places.p_id', '=', 'individual_placelinks.pl_p_id');
            });
        }

        if ($fam_plac) {
            $query->join('placelinks AS familyl_placelinks', static function (JoinClause $join): void {
                $join
                    ->on('familyl_placelinks.pl_file', '=', 'individuals.i_file')
                    ->on('familyl_placelinks.pl_gid', '=', 'individuals.i_id');
            });
            $query->join('places AS family_places', static function (JoinClause $join): void {
                $join
                    ->on('family_places.p_file', '=', 'familyl_placelinks.pl_file')
                    ->on('family_places.p_id', '=', 'familyl_placelinks.pl_p_id');
            });
        }

        foreach ($fields as $field_name => $field_value) {
            $parts = preg_split('/:/', $field_name . '::::');
            if ($parts[0] === 'NAME') {
                // NAME:*
                switch ($parts[1]) {
                    case 'GIVN':
                        switch ($modifiers[$field_name]) {
                            case 'EXACT':
                                $query->where('individual_name.n_givn', '=', $field_value);
                                break;
                            case 'BEGINS':
                                $query->where('individual_name.n_givn', 'LIKE', $field_value . '%');
                                break;
                            case 'CONTAINS':
                                $query->where('individual_name.n_givn', 'LIKE', '%' . $field_value . '%');
                                break;
                            case 'SDX_STD':
                                $sdx = Soundex::russell($field_value);
                                if ($sdx !== '') {
                                    $this->wherePhonetic($query, 'individual_name.n_soundex_givn_std', $sdx);
                                } else {
                                    // No phonetic content? Use a substring match
                                    $query->where('individual_name.n_givn', 'LIKE', '%' . $field_value . '%');
                                }
                                break;
                            case 'SDX': // SDX uses DM by default.
                            case 'SDX_DM':
                                $sdx = Soundex::daitchMokotoff($field_value);
                                if ($sdx !== '') {
                                    $this->wherePhonetic($query, 'individual_name.n_soundex_givn_dm', $sdx);
                                } else {
                                    // No phonetic content? Use a substring match
                                    $query->where('individual_name.n_givn', 'LIKE', '%' . $field_value . '%');
                                }
                                break;
                        }
                        break;
                    case 'SURN':
                        switch ($modifiers[$field_name]) {
                            case 'EXACT':
                                $query->where('individual_name.n_surn', '=', $field_value);
                                break;
                            case 'BEGINS':
                                $query->where('individual_name.n_surn', 'LIKE', $field_value . '%');
                                break;
                            case 'CONTAINS':
                                $query->where('individual_name.n_surn', 'LIKE', '%' . $field_value . '%');
                                break;
                            case 'SDX_STD':
                                $sdx = Soundex::russell($field_value);
                                if ($sdx !== '') {
                                    $this->wherePhonetic($query, 'individual_name.n_soundex_surn_std', $sdx);
                                } else {
                                    // No phonetic content? Use a substring match
                                    $query->where('individual_name.n_surn', 'LIKE', '%' . $field_value . '%');
                                }
                                break;
                            case 'SDX': // SDX uses DM by default.
                            case 'SDX_DM':
                                $sdx = Soundex::daitchMokotoff($field_value);
                                if ($sdx !== '') {
                                    $this->wherePhonetic($query, 'individual_name.n_soundex_surn_dm', $sdx);
                                } else {
                                    // No phonetic content? Use a substring match
                                    $query->where('individual_name.n_surn', 'LIKE', '%' . $field_value . '%');
                                }
                                break;
                        }
                        break;
                    case 'NICK':
                    case '_MARNM':
                    case '_HEB':
                    case '_AKA':
                        $query
                            ->where('individual_name', '=', $parts[1])
                            ->where('individual_name', 'LIKE', '%' . $field_value . '%');
                        break;
                }
                unset($fields[$field_name]);
            } elseif ($parts[1] === 'DATE') {
                // *:DATE
                $date = new Date($field_value);
                if ($date->isOK()) {
                    $delta = 365 * ($modifiers[$field_name] ?? 0);
                    $query
                        ->where('individual_dates.d_fact', '=', $parts[0])
                        ->where('individual_dates.d_julianday1', '>=', $date->minimumJulianDay() - $delta)
                        ->where('individual_dates.d_julianday2', '<=', $date->minimumJulianDay() + $delta);
                }
                unset($fields[$field_name]);
            } elseif ($parts[0] === 'FAMS' && $parts[2] === 'DATE') {
                // FAMS:*:DATE
                $date = new Date($field_value);
                if ($date->isOK()) {
                    $delta = 365 * $modifiers[$field_name];
                    $query
                        ->where('family_dates.d_fact', '=', $parts[1])
                        ->where('family_dates.d_julianday1', '>=', $date->minimumJulianDay() - $delta)
                        ->where('family_dates.d_julianday2', '<=', $date->minimumJulianDay() + $delta);
                }
                unset($fields[$field_name]);
            } elseif ($parts[1] === 'PLAC') {
                // *:PLAC
                // SQL can only link a place to a person/family, not to an event.
                $query->where('individual_places.p_place', 'LIKE', '%' . $field_value . '%');
            } elseif ($parts[0] === 'FAMS' && $parts[2] === 'PLAC') {
                // FAMS:*:PLAC
                // SQL can only link a place to a person/family, not to an event.
                $query->where('family_places.p_place', 'LIKE', '%' . $field_value . '%');
            } elseif ($parts[0] === 'FAMC' && $parts[2] === 'NAME') {
                $table = $parts[1] === 'HUSB' ? 'father_name' : 'mother_name';
                // NAME:*
                switch ($parts[3]) {
                    case 'GIVN':
                        switch ($modifiers[$field_name]) {
                            case 'EXACT':
                                $query->where($table . '.n_givn', '=', $field_value);
                                break;
                            case 'BEGINS':
                                $query->where($table . '.n_givn', 'LIKE', $field_value . '%');
                                break;
                            case 'CONTAINS':
                                $query->where($table . '.n_givn', 'LIKE', '%' . $field_value . '%');
                                break;
                            case 'SDX_STD':
                                $sdx = Soundex::russell($field_value);
                                if ($sdx !== '') {
                                    $this->wherePhonetic($query, $table . '.n_soundex_givn_std', $sdx);
                                } else {
                                    // No phonetic content? Use a substring match
                                    $query->where($table . '.n_givn', 'LIKE', '%' . $field_value . '%');
                                }
                                break;
                            case 'SDX': // SDX uses DM by default.
                            case 'SDX_DM':
                                $sdx = Soundex::daitchMokotoff($field_value);
                                if ($sdx !== '') {
                                    $this->wherePhonetic($query, $table . '.n_soundex_givn_dm', $sdx);
                                } else {
                                    // No phonetic content? Use a substring match
                                    $query->where($table . '.n_givn', 'LIKE', '%' . $field_value . '%');
                                }
                                break;
                        }
                        break;
                    case 'SURN':
                        switch ($modifiers[$field_name]) {
                            case 'EXACT':
                                $query->where($table . '.n_surn', '=', $field_value);
                                break;
                            case 'BEGINS':
                                $query->where($table . '.n_surn', 'LIKE', $field_value . '%');
                                break;
                            case 'CONTAINS':
                                $query->where($table . '.n_surn', 'LIKE', '%' . $field_value . '%');
                                break;
                            case 'SDX_STD':
                                $sdx = Soundex::russell($field_value);
                                if ($sdx !== '') {
                                    $this->wherePhonetic($query, $table . '.n_soundex_surn_std', $sdx);
                                } else {
                                    // No phonetic content? Use a substring match
                                    $query->where($table . '.n_surn', 'LIKE', '%' . $field_value . '%');
                                }
                                break;
                            case 'SDX': // SDX uses DM by default.
                            case 'SDX_DM':
                                $sdx = Soundex::daitchMokotoff($field_value);
                                if ($sdx !== '') {
                                    $this->wherePhonetic($query, $table . '.n_soundex_surn_dm', $sdx);
                                } else {
                                    // No phonetic content? Use a substring match
                                    $query->where($table . '.n_surn', 'LIKE', '%' . $field_value . '%');
                                }
                                break;
                        }
                        break;
                }
                unset($fields[$field_name]);
            } elseif ($parts[0] === 'FAMS') {
                // e.g. searches for occupation, religion, note, etc.
                // Initial matching only.  Need PHP to apply filter.
                $query->where('families.f_gedcom', 'LIKE', "%\n1 " . $parts[1] . ' %' . $field_value . '%');
            } elseif ($parts[1] === 'TYPE') {
                // e.g. FACT:TYPE or EVEN:TYPE
                // Initial matching only.  Need PHP to apply filter.
                $query->where('individuals.i_gedcom', 'LIKE', "%\n1 " . $parts[0] . '%\n2 TYPE %' . $field_value . '%');
            } else {
                // e.g. searches for occupation, religion, note, etc.
                // Initial matching only.  Need PHP to apply filter.
                $query->where('individuals.i_gedcom', 'LIKE', "%\n1 " . $parts[0] . '%' . $parts[1] . '%' . $field_value . '%');
            }
        }
        return $query
            ->get()
            ->each($this->rowLimiter())
            ->map(Individual::rowMapper())
            ->filter(GedcomRecord::accessFilter())
            ->filter(static function (Individual $individual) use ($fields): bool {
                // Check for searches which were only partially matched by SQL
                foreach ($fields as $field_name => $field_value) {
                    $regex = '/' . preg_quote($field_value, '/') . '/i';

                    $parts = explode(':', $field_name . '::::');

                    // *:PLAC
                    if ($parts[1] === 'PLAC') {
                        foreach ($individual->facts([$parts[0]]) as $fact) {
                            if (preg_match($regex, $fact->place()->gedcomName())) {
                                continue 2;
                            }
                        }
                        return false;
                    }

                    // FAMS:*:PLAC
                    if ($parts[0] === 'FAMS' && $parts[2] === 'PLAC') {
                        foreach ($individual->spouseFamilies() as $family) {
                            foreach ($family->facts([$parts[1]]) as $fact) {
                                if (preg_match($regex, $fact->place()->gedcomName())) {
                                    continue 2;
                                }
                            }
                        }
                        return false;
                    }

                    // e.g. searches for occupation, religion, note, etc.
                    if ($parts[0] === 'FAMS') {
                        foreach ($individual->spouseFamilies() as $family) {
                            foreach ($family->facts([$parts[1]]) as $fact) {
                                if (preg_match($regex, $fact->value())) {
                                    continue 3;
                                }
                            }
                        }
                        return false;
                    }

                    // e.g. FACT:TYPE or EVEN:TYPE
                    if ($parts[1] === 'TYPE' || $parts[1] === '_WT_USER') {
                        foreach ($individual->facts([$parts[0]]) as $fact) {
                            if (preg_match($regex, $fact->attribute($parts[1]))) {
                                continue 2;
                            }
                        }

                        return false;
                    }
                }

                return true;
            });
    }

    /**
     * @param string $soundex
     * @param string $lastname
     * @param string $firstname
     * @param string $place
     * @param Tree[] $search_trees
     *
     * @return Collection
     */
    public function searchIndividualsPhonetic(string $soundex, string $lastname, string $firstname, string $place, array $search_trees): Collection
    {
        switch ($soundex) {
            default:
            case 'Russell':
                $givn_sdx   = Soundex::russell($firstname);
                $surn_sdx   = Soundex::russell($lastname);
                $plac_sdx   = Soundex::russell($place);
                $givn_field = 'n_soundex_givn_std';
                $surn_field = 'n_soundex_surn_std';
                $plac_field = 'p_std_soundex';
                break;
            case 'DaitchM':
                $givn_sdx   = Soundex::daitchMokotoff($firstname);
                $surn_sdx   = Soundex::daitchMokotoff($lastname);
                $plac_sdx   = Soundex::daitchMokotoff($place);
                $givn_field = 'n_soundex_givn_dm';
                $surn_field = 'n_soundex_surn_dm';
                $plac_field = 'p_dm_soundex';
                break;
        }

        // Nothing to search for? Return nothing.
        if ($givn_sdx === '' && $surn_sdx === '' && $plac_sdx === '') {
            return new Collection();
        }

        $query = DB::table('individuals')
            ->select(['individuals.*'])
            ->distinct();

        $this->whereTrees($query, 'i_file', $search_trees);

        if ($plac_sdx !== '') {
            $query->join('placelinks', static function (JoinClause $join): void {
                $join
                    ->on('placelinks.pl_file', '=', 'individuals.i_file')
                    ->on('placelinks.pl_gid', '=', 'individuals.i_id');
            });
            $query->join('places', static function (JoinClause $join): void {
                $join
                    ->on('places.p_file', '=', 'placelinks.pl_file')
                    ->on('places.p_id', '=', 'placelinks.pl_p_id');
            });

            $this->wherePhonetic($query, $plac_field, $plac_sdx);
        }

        if ($givn_sdx !== '' || $surn_sdx !== '') {
            $query->join('name', static function (JoinClause $join): void {
                $join
                    ->on('name.n_file', '=', 'individuals.i_file')
                    ->on('name.n_id', '=', 'individuals.i_id');
            });

            $this->wherePhonetic($query, $givn_field, $givn_sdx);
            $this->wherePhonetic($query, $surn_field, $surn_sdx);
        }

        return $query
            ->get()
            ->each($this->rowLimiter())
            ->map(Individual::rowMapper())
            ->filter(GedcomRecord::accessFilter());
    }

    /**
     * Paginate a search query.
     *
     * @param Builder $query      Searches the database for the desired records.
     * @param Closure $row_mapper Converts a row from the query into a record.
     * @param Closure $row_filter
     * @param int     $offset     Skip this many rows.
     * @param int     $limit      Take this many rows.
     *
     * @return Collection
     */
    private function paginateQuery(Builder $query, Closure $row_mapper, Closure $row_filter, int $offset, int $limit): Collection
    {
        $collection = new Collection();

        foreach ($query->cursor() as $row) {
            $record = $row_mapper($row);
            // If the object has a method "canShow()", then use it to filter for privacy.
            if ($row_filter($record)) {
                if ($offset > 0) {
                    $offset--;
                } else {
                    if ($limit > 0) {
                        $collection->push($record);
                    }

                    $limit--;

                    if ($limit === 0) {
                        break;
                    }
                }
            }
        }


        return $collection;
    }

    /**
     * Apply search filters to a SQL query column.  Apply collation rules to MySQL.
     *
     * @param Builder           $query
     * @param Expression|string $field
     * @param string[]          $search_terms
     */
    private function whereSearch(Builder $query, $field, array $search_terms): void
    {
        if ($field instanceof Expression) {
            $field = $field->getValue();
        }

        foreach ($search_terms as $search_term) {
            $query->whereContains(new Expression($field), $search_term);
        }
    }

    /**
     * Apply soundex search filters to a SQL query column.
     *
     * @param Builder           $query
     * @param Expression|string $field
     * @param string            $soundex
     */
    private function wherePhonetic(Builder $query, $field, string $soundex): void
    {
        if ($soundex !== '') {
            $query->where(static function (Builder $query) use ($soundex, $field): void {
                foreach (explode(':', $soundex) as $sdx) {
                    $query->orWhere($field, 'LIKE', '%' . $sdx . '%');
                }
            });
        }
    }

    /**
     * @param Builder $query
     * @param string  $tree_id_field
     * @param Tree[]  $trees
     */
    private function whereTrees(Builder $query, string $tree_id_field, array $trees): void
    {
        $tree_ids = array_map(static function (Tree $tree): int {
            return $tree->id();
        }, $trees);

        $query->whereIn($tree_id_field, $tree_ids);
    }

    /**
     * A closure to filter records by privacy-filtered GEDCOM data.
     *
     * @param array $search_terms
     *
     * @return Closure
     */
    private function rawGedcomFilter(array $search_terms): Closure
    {
        return static function (GedcomRecord $record) use ($search_terms): bool {
            // Ignore non-genealogy fields
            $gedcom = preg_replace('/\n\d (?:_UID) .*/', '', $record->gedcom());

            // Ignore matches in links
            $gedcom = preg_replace('/\n\d ' . Gedcom::REGEX_TAG . '( @' . Gedcom::REGEX_XREF . '@)?/', '', $gedcom);

            // Re-apply the filtering
            foreach ($search_terms as $search_term) {
                if (mb_stripos($gedcom, $search_term) === false) {
                    return false;
                }
            }

            return true;
        };
    }

    /**
     * Searching for short or common text can give more results than the system can process.
     *
     * @param int $limit
     *
     * @return Closure
     */
    private function rowLimiter(int $limit = 1000): Closure
    {
        return static function () use ($limit): void {
            static $n = 0;

            if (++$n > $limit) {
                $message = I18N::translate('The search returned too many results.');

                throw new InternalServerErrorException($message);
            }
        };
    }
}
