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

namespace Fisharebest\Webtrees\Services;

use Closure;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Http\Exceptions\HttpServiceUnavailableException;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Location;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Place;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\SharedNote;
use Fisharebest\Webtrees\Soundex;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Submission;
use Fisharebest\Webtrees\Submitter;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;

use function addcslashes;
use function array_filter;
use function array_map;
use function array_unique;
use function explode;
use function implode;
use function mb_stripos;
use function preg_match;
use function preg_quote;
use function preg_replace;
use function str_ends_with;
use function str_starts_with;

use const PHP_INT_MAX;

/**
 * Search trees for genealogy records.
 */
class SearchService
{
    // Do not attempt to show search results larger than this/
    protected const MAX_SEARCH_RESULTS = 5000;

    private TreeService $tree_service;

    /**
     * SearchService constructor.
     *
     * @param TreeService $tree_service
     */
    public function __construct(
        TreeService $tree_service
    ) {
        $this->tree_service = $tree_service;
    }

    /**
     * @param array<Tree>   $trees
     * @param array<string> $search
     *
     * @return Collection<int,Family>
     */
    public function searchFamilies(array $trees, array $search): Collection
    {
        $query = DB::table('families');

        $this->whereTrees($query, 'f_file', $trees);
        $this->whereSearch($query, 'f_gedcom', $search);

        return $query
            ->get()
            ->each($this->rowLimiter())
            ->map($this->familyRowMapper())
            ->filter(GedcomRecord::accessFilter())
            ->filter($this->rawGedcomFilter($search));
    }

    /**
     * Search for families by name.
     *
     * @param array<Tree>   $trees
     * @param array<string> $search
     * @param int           $offset
     * @param int           $limit
     *
     * @return Collection<int,Family>
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
            ->select(['families.*', 'husb_name.n_sort', 'wife_name.n_sort']);

        return $this->paginateQuery($query, $this->familyRowMapper(), GedcomRecord::accessFilter(), $offset, $limit);
    }

    /**
     * @param Place $place
     *
     * @return Collection<int,Family>
     */
    public function searchFamiliesInPlace(Place $place): Collection
    {
        return DB::table('families')
            ->join('placelinks', static function (JoinClause $query) {
                $query
                    ->on('families.f_file', '=', 'placelinks.pl_file')
                    ->on('families.f_id', '=', 'placelinks.pl_gid');
            })
            ->where('f_file', '=', $place->tree()->id())
            ->where('pl_p_id', '=', $place->id())
            ->select(['families.*'])
            ->get()
            ->each($this->rowLimiter())
            ->map($this->familyRowMapper())
            ->filter(GedcomRecord::accessFilter());
    }

    /**
     * @param array<Tree>   $trees
     * @param array<string> $search
     *
     * @return Collection<int,Individual>
     */
    public function searchIndividuals(array $trees, array $search): Collection
    {
        $query = DB::table('individuals');

        $this->whereTrees($query, 'i_file', $trees);
        $this->whereSearch($query, 'i_gedcom', $search);

        return $query
            ->get()
            ->each($this->rowLimiter())
            ->map($this->individualRowMapper())
            ->filter(GedcomRecord::accessFilter())
            ->filter($this->rawGedcomFilter($search));
    }

    /**
     * Search for individuals by name.
     *
     * @param array<Tree>   $trees
     * @param array<string> $search
     * @param int           $offset
     * @param int           $limit
     *
     * @return Collection<int,Individual>
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
            ->select(['individuals.*', 'n_sort']);

        $this->whereTrees($query, 'i_file', $trees);
        $this->whereSearch($query, 'n_full', $search);

        return $this->paginateQuery($query, $this->individualRowMapper(), GedcomRecord::accessFilter(), $offset, $limit);
    }

    /**
     * @param Place $place
     *
     * @return Collection<int,Individual>
     */
    public function searchIndividualsInPlace(Place $place): Collection
    {
        return DB::table('individuals')
            ->join('placelinks', static function (JoinClause $join) {
                $join
                    ->on('i_file', '=', 'pl_file')
                    ->on('i_id', '=', 'pl_gid');
            })
            ->where('i_file', '=', $place->tree()->id())
            ->where('pl_p_id', '=', $place->id())
            ->select(['individuals.*'])
            ->get()
            ->each($this->rowLimiter())
            ->map($this->individualRowMapper())
            ->filter(GedcomRecord::accessFilter());
    }

    /**
     * Search for submissions.
     *
     * @param array<Tree>   $trees
     * @param array<string> $search
     * @param int           $offset
     * @param int           $limit
     *
     * @return Collection<int,Location>
     */
    public function searchLocations(array $trees, array $search, int $offset = 0, int $limit = PHP_INT_MAX): Collection
    {
        $query = DB::table('other')
            ->where('o_type', '=', Location::RECORD_TYPE);

        $this->whereTrees($query, 'o_file', $trees);
        $this->whereSearch($query, 'o_gedcom', $search);

        return $this->paginateQuery($query, $this->locationRowMapper(), GedcomRecord::accessFilter(), $offset, $limit);
    }

    /**
     * Search for media objects.
     *
     * @param array<Tree>   $trees
     * @param array<string> $search
     * @param int           $offset
     * @param int           $limit
     *
     * @return Collection<int,Media>
     */
    public function searchMedia(array $trees, array $search, int $offset = 0, int $limit = PHP_INT_MAX): Collection
    {
        $query = DB::table('media');

        $this->whereTrees($query, 'media.m_file', $trees);
        $this->whereSearch($query, 'm_gedcom', $search);

        return $this->paginateQuery($query, $this->mediaRowMapper(), GedcomRecord::accessFilter(), $offset, $limit);
    }

    /**
     * Search for notes.
     *
     * @param array<Tree>   $trees
     * @param array<string> $search
     * @param int           $offset
     * @param int           $limit
     *
     * @return Collection<int,Note>
     */
    public function searchNotes(array $trees, array $search, int $offset = 0, int $limit = PHP_INT_MAX): Collection
    {
        $query = DB::table('other')
            ->where('o_type', '=', Note::RECORD_TYPE);

        $this->whereTrees($query, 'o_file', $trees);
        $this->whereSearch($query, 'o_gedcom', $search);

        return $this->paginateQuery($query, $this->noteRowMapper(), GedcomRecord::accessFilter(), $offset, $limit);
    }

    /**
     * Search for notes.
     *
     * @param array<Tree>   $trees
     * @param array<string> $search
     * @param int           $offset
     * @param int           $limit
     *
     * @return Collection<int,SharedNote>
     */
    public function searchSharedNotes(array $trees, array $search, int $offset = 0, int $limit = PHP_INT_MAX): Collection
    {
        $query = DB::table('other')
            ->where('o_type', '=', SharedNote::RECORD_TYPE);

        $this->whereTrees($query, 'o_file', $trees);
        $this->whereSearch($query, 'o_gedcom', $search);

        return $this->paginateQuery($query, $this->sharedNoteRowMapper(), GedcomRecord::accessFilter(), $offset, $limit);
    }

    /**
     * Search for repositories.
     *
     * @param array<Tree>   $trees
     * @param array<string> $search
     * @param int           $offset
     * @param int           $limit
     *
     * @return Collection<int,Repository>
     */
    public function searchRepositories(array $trees, array $search, int $offset = 0, int $limit = PHP_INT_MAX): Collection
    {
        $query = DB::table('other')
            ->where('o_type', '=', Repository::RECORD_TYPE);

        $this->whereTrees($query, 'o_file', $trees);
        $this->whereSearch($query, 'o_gedcom', $search);

        return $this->paginateQuery($query, $this->repositoryRowMapper(), GedcomRecord::accessFilter(), $offset, $limit);
    }

    /**
     * Search for sources.
     *
     * @param array<Tree>   $trees
     * @param array<string> $search
     * @param int      $offset
     * @param int      $limit
     *
     * @return Collection<int,Source>
     */
    public function searchSources(array $trees, array $search, int $offset = 0, int $limit = PHP_INT_MAX): Collection
    {
        $query = DB::table('sources');

        $this->whereTrees($query, 's_file', $trees);
        $this->whereSearch($query, 's_gedcom', $search);

        return $this->paginateQuery($query, $this->sourceRowMapper(), GedcomRecord::accessFilter(), $offset, $limit);
    }

    /**
     * Search for sources by name.
     *
     * @param array<Tree>   $trees
     * @param array<string> $search
     * @param int           $offset
     * @param int           $limit
     *
     * @return Collection<int,Source>
     */
    public function searchSourcesByName(array $trees, array $search, int $offset = 0, int $limit = PHP_INT_MAX): Collection
    {
        $query = DB::table('sources')
            ->orderBy('s_name');

        $this->whereTrees($query, 's_file', $trees);
        $this->whereSearch($query, 's_name', $search);

        return $this->paginateQuery($query, $this->sourceRowMapper(), GedcomRecord::accessFilter(), $offset, $limit);
    }

    /**
     * Search for sources.
     *
     * @param array<Tree>   $trees
     * @param array<string> $search
     * @param int           $offset
     * @param int           $limit
     *
     * @return Collection<int,string>
     */
    public function searchSurnames(array $trees, array $search, int $offset = 0, int $limit = PHP_INT_MAX): Collection
    {
        $query = DB::table('name');

        $this->whereTrees($query, 'n_file', $trees);
        $this->whereSearch($query, 'n_surname', $search);

        return $query
            ->groupBy(['n_surname'])
            ->orderBy('n_surname')
            ->skip($offset)
            ->take($limit)
            ->pluck('n_surname');
    }

    /**
     * Search for submissions.
     *
     * @param array<Tree>   $trees
     * @param array<string> $search
     * @param int           $offset
     * @param int           $limit
     *
     * @return Collection<int,Submission>
     */
    public function searchSubmissions(array $trees, array $search, int $offset = 0, int $limit = PHP_INT_MAX): Collection
    {
        $query = DB::table('other')
            ->where('o_type', '=', Submission::RECORD_TYPE);

        $this->whereTrees($query, 'o_file', $trees);
        $this->whereSearch($query, 'o_gedcom', $search);

        return $this->paginateQuery($query, $this->submissionRowMapper(), GedcomRecord::accessFilter(), $offset, $limit);
    }

    /**
     * Search for submitters.
     *
     * @param array<Tree>   $trees
     * @param array<string> $search
     * @param int           $offset
     * @param int           $limit
     *
     * @return Collection<int,Submitter>
     */
    public function searchSubmitters(array $trees, array $search, int $offset = 0, int $limit = PHP_INT_MAX): Collection
    {
        $query = DB::table('other')
            ->where('o_type', '=', Submitter::RECORD_TYPE);

        $this->whereTrees($query, 'o_file', $trees);
        $this->whereSearch($query, 'o_gedcom', $search);

        return $this->paginateQuery($query, $this->submitterRowMapper(), GedcomRecord::accessFilter(), $offset, $limit);
    }

    /**
     * Search for places.
     *
     * @param Tree   $tree
     * @param string $search
     * @param int    $offset
     * @param int    $limit
     *
     * @return Collection<int,Place>
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
            $query->where('p' . $level . '.p_place', 'LIKE', '%' . addcslashes($string, '\\%_') . '%');
        }

        $row_mapper = static function (object $row) use ($tree): Place {
            $place = implode(', ', array_filter((array) $row));

            return new Place($place, $tree);
        };

        $filter = static function (): bool {
            return true;
        };

        return $this->paginateQuery($query, $row_mapper, $filter, $offset, $limit);
    }

    /**
     * @param array<Tree>          $trees
     * @param array<string,string> $fields
     * @param array<string,string> $modifiers
     *
     * @return Collection<int,Individual>
     */
    public function searchIndividualsAdvanced(array $trees, array $fields, array $modifiers): Collection
    {
        $fields = array_filter($fields, static fn (string $x): bool => $x !== '');

        $query = DB::table('individuals')
            ->select(['individuals.*'])
            ->distinct();

        $this->whereTrees($query, 'i_file', $trees);

        // Join the following tables
        $father_name   = false;
        $mother_name   = false;
        $spouse_family = false;
        $indi_name     = false;
        $indi_dates    = [];
        $fam_dates     = [];
        $indi_plac     = false;
        $fam_plac      = false;

        foreach ($fields as $field_name => $field_value) {
            if (str_starts_with($field_name, 'FATHER:NAME')) {
                $father_name = true;
            } elseif (str_starts_with($field_name, 'MOTHER:NAME')) {
                $mother_name = true;
            } elseif (str_starts_with($field_name, 'INDI:NAME:GIVN')) {
                $indi_name = true;
            } elseif (str_starts_with($field_name, 'INDI:NAME:SURN')) {
                $indi_name = true;
            } elseif (str_starts_with($field_name, 'FAM:')) {
                $spouse_family = true;
                if (str_ends_with($field_name, ':DATE')) {
                    $fam_dates[] = explode(':', $field_name)[1];
                } elseif (str_ends_with($field_name, ':PLAC')) {
                    $fam_plac = true;
                }
            } elseif (str_starts_with($field_name, 'INDI:')) {
                if (str_ends_with($field_name, ':DATE')) {
                    $indi_dates[] = explode(':', $field_name)[1];
                } elseif (str_ends_with($field_name, ':PLAC')) {
                    $indi_plac = true;
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

        foreach (array_unique($indi_dates) as $indi_date) {
            $query->join('dates AS date_' . $indi_date, static function (JoinClause $join) use ($indi_date): void {
                $join
                    ->on('date_' . $indi_date . '.d_file', '=', 'individuals.i_file')
                    ->on('date_' . $indi_date . '.d_gid', '=', 'individuals.i_id');
            });
        }

        foreach (array_unique($fam_dates) as $fam_date) {
            $query->join('dates AS date_' . $fam_date, static function (JoinClause $join) use ($fam_date): void {
                $join
                    ->on('date_' . $fam_date . '.d_file', '=', 'spouse_families.f_file')
                    ->on('date_' . $fam_date . '.d_gid', '=', 'spouse_families.f_id');
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
            $parts = explode(':', $field_name . ':::');
            if (str_starts_with($field_name, 'INDI:NAME:')) {
                switch ($field_name) {
                    case 'INDI:NAME:GIVN':
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
                        unset($fields[$field_name]);
                        break;
                    case 'INDI:NAME:SURN':
                        switch ($modifiers[$field_name]) {
                            case 'EXACT':
                                $query->where(function (Builder $query) use ($field_value): void {
                                    $query
                                        ->where('individual_name.n_surn', '=', $field_value)
                                        ->orWhere('individual_name.n_surname', '=', $field_value);
                                });
                                break;
                            case 'BEGINS':
                                $query->where(function (Builder $query) use ($field_value): void {
                                    $query
                                        ->where('individual_name.n_surn', 'LIKE', $field_value . '%')
                                        ->orWhere('individual_name.n_surname', 'LIKE', $field_value . '%');
                                });
                                break;
                            case 'CONTAINS':
                                $query->where(function (Builder $query) use ($field_value): void {
                                    $query
                                        ->where('individual_name.n_surn', 'LIKE', '%' . $field_value . '%')
                                        ->orWhere('individual_name.n_surname', 'LIKE', '%' . $field_value . '%');
                                });
                                break;
                            case 'SDX_STD':
                                $sdx = Soundex::russell($field_value);
                                if ($sdx !== '') {
                                    $this->wherePhonetic($query, 'individual_name.n_soundex_surn_std', $sdx);
                                } else {
                                    // No phonetic content? Use a substring match
                                    $query->where(function (Builder $query) use ($field_value): void {
                                        $query
                                            ->where('individual_name.n_surn', 'LIKE', '%' . $field_value . '%')
                                            ->orWhere('individual_name.n_surname', 'LIKE', '%' . $field_value . '%');
                                    });
                                }
                                break;
                            case 'SDX': // SDX uses DM by default.
                            case 'SDX_DM':
                                $sdx = Soundex::daitchMokotoff($field_value);
                                if ($sdx !== '') {
                                    $this->wherePhonetic($query, 'individual_name.n_soundex_surn_dm', $sdx);
                                } else {
                                    // No phonetic content? Use a substring match
                                    $query->where(function (Builder $query) use ($field_value): void {
                                        $query
                                            ->where('individual_name.n_surn', 'LIKE', '%' . $field_value . '%')
                                            ->orWhere('individual_name.n_surname', 'LIKE', '%' . $field_value . '%');
                                    });
                                }
                                break;
                        }
                        unset($fields[$field_name]);
                        break;
                    case 'INDI:NAME:NICK':
                    case 'INDI:NAME:_MARNM':
                    case 'INDI:NAME:_HEB':
                    case 'INDI:NAME:_AKA':
                        $like = "%\n1 NAME%\n2 " . $parts[2] . ' %' . preg_quote($field_value, '/') . '%';
                        $query->where('individuals.i_gedcom', 'LIKE', $like);
                        break;
                }
            } elseif (str_starts_with($field_name, 'INDI:') && str_ends_with($field_name, ':DATE')) {
                $date = new Date($field_value);
                if ($date->isOK()) {
                    $delta = 365 * (int) ($modifiers[$field_name] ?? 0);
                    $query
                        ->where('date_' . $parts[1] . '.d_fact', '=', $parts[1])
                        ->where('date_' . $parts[1] . '.d_julianday1', '>=', $date->minimumJulianDay() - $delta)
                        ->where('date_' . $parts[1] . '.d_julianday2', '<=', $date->maximumJulianDay() + $delta);
                }
                unset($fields[$field_name]);
            } elseif (str_starts_with($field_name, 'FAM:') && str_ends_with($field_name, ':DATE')) {
                $date = new Date($field_value);
                if ($date->isOK()) {
                    $delta = 365 * (int) ($modifiers[$field_name] ?? 0);
                    $query
                        ->where('date_' . $parts[1] . '.d_fact', '=', $parts[1])
                        ->where('date_' . $parts[1] . '.d_julianday1', '>=', $date->minimumJulianDay() - $delta)
                        ->where('date_' . $parts[1] . '.d_julianday2', '<=', $date->maximumJulianDay() + $delta);
                }
                unset($fields[$field_name]);
            } elseif (str_starts_with($field_name, 'INDI:') && str_ends_with($field_name, ':PLAC')) {
                // SQL can only link a place to a person/family, not to an event.
                $query->where('individual_places.p_place', 'LIKE', '%' . $field_value . '%');
            } elseif (str_starts_with($field_name, 'FAM:') && str_ends_with($field_name, ':PLAC')) {
                // SQL can only link a place to a person/family, not to an event.
                $query->where('family_places.p_place', 'LIKE', '%' . $field_value . '%');
            } elseif (str_starts_with($field_name, 'MOTHER:NAME:') || str_starts_with($field_name, 'FATHER:NAME:')) {
                $table = str_starts_with($field_name, 'FATHER:NAME:') ? 'father_name' : 'mother_name';
                switch ($parts[2]) {
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
            } elseif (str_starts_with($field_name, 'FAM:')) {
                // e.g. searches for occupation, religion, note, etc.
                // Initial matching only.  Need PHP to apply filter.
                $query->where('spouse_families.f_gedcom', 'LIKE', "%\n1 " . $parts[1] . ' %' . $field_value . '%');
            } elseif (str_starts_with($field_name, 'INDI:') && str_ends_with($field_name, ':TYPE')) {
                // Initial matching only.  Need PHP to apply filter.
                $query->where('individuals.i_gedcom', 'LIKE', "%\n1 " . $parts[1] . "%\n2 TYPE %" . $field_value . '%');
            } elseif (str_starts_with($field_name, 'INDI:')) {
                // e.g. searches for occupation, religion, note, etc.
                // Initial matching only.  Need PHP to apply filter.
                $query->where('individuals.i_gedcom', 'LIKE', "%\n1 " . $parts[1] . '%' . $parts[2] . '%' . $field_value . '%');
            }
        }

        return $query
            ->get()
            ->each($this->rowLimiter())
            ->map($this->individualRowMapper())
            ->filter(GedcomRecord::accessFilter())
            ->filter(static function (Individual $individual) use ($fields): bool {
                // Check for searches which were only partially matched by SQL
                foreach ($fields as $field_name => $field_value) {
                    $parts = explode(':', $field_name . '::::');

                    if (str_starts_with($field_name, 'INDI:NAME:') && $field_name !== 'INDI:NAME:GIVN' && $field_name !== 'INDI:NAME:SURN') {
                        $regex = '/\n1 NAME.*(?:\n2.*)*\n2 ' . $parts[2] . ' .*' . preg_quote($field_value, '/') . '/i';

                        if (preg_match($regex, $individual->gedcom()) === 1) {
                            continue;
                        }

                        return false;
                    }

                    $regex = '/' . preg_quote($field_value, '/') . '/i';

                    if (str_starts_with($field_name, 'INDI:') && str_ends_with($field_name, ':PLAC')) {
                        foreach ($individual->facts([$parts[1]]) as $fact) {
                            if (preg_match($regex, $fact->place()->gedcomName()) === 1) {
                                continue 2;
                            }
                        }
                        return false;
                    }

                    if (str_starts_with($field_name, 'FAM:') && str_ends_with($field_name, ':PLAC')) {
                        foreach ($individual->spouseFamilies() as $family) {
                            foreach ($family->facts([$parts[1]]) as $fact) {
                                if (preg_match($regex, $fact->place()->gedcomName()) === 1) {
                                    continue 3;
                                }
                            }
                        }
                        return false;
                    }

                    if ($field_name === 'INDI:FACT:TYPE' || $field_name === 'INDI:EVEN:TYPE' || $field_name === 'INDI:CHAN:_WT_USER') {
                        foreach ($individual->facts([$parts[1]]) as $fact) {
                            if (preg_match($regex, $fact->attribute($parts[2])) === 1) {
                                continue 2;
                            }
                        }

                        return false;
                    }

                    if (str_starts_with($field_name, 'INDI:')) {
                        foreach ($individual->facts([$parts[1]]) as $fact) {
                            if (preg_match($regex, $fact->value()) === 1) {
                                continue 2;
                            }
                        }

                        return false;
                    }

                    if (str_starts_with($field_name, 'FAM:')) {
                        foreach ($individual->spouseFamilies() as $family) {
                            foreach ($family->facts([$parts[1]]) as $fact) {
                                if (preg_match($regex, $fact->value()) === 1) {
                                    continue 3;
                                }
                            }
                        }
                        return false;
                    }
                }

                return true;
            });
    }

    /**
     * @param string      $soundex
     * @param string      $lastname
     * @param string      $firstname
     * @param string      $place
     * @param array<Tree> $search_trees
     *
     * @return Collection<int,Individual>
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
            ->map($this->individualRowMapper())
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
     * @return Collection<int,mixed>
     */
    private function paginateQuery(Builder $query, Closure $row_mapper, Closure $row_filter, int $offset, int $limit): Collection
    {
        $collection = new Collection();

        foreach ($query->cursor() as $row) {
            $record = $row_mapper($row);
            // searchIndividualNames() and searchFamilyNames() can return duplicate rows,
            // where individuals have multiple names - and we need to sort results by name.
            if ($collection->containsStrict($record)) {
                continue;
            }
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
     * @param array<string>     $search_terms
     */
    private function whereSearch(Builder $query, $field, array $search_terms): void
    {
        if ($field instanceof Expression) {
            $field = $field->getValue();
        }

        foreach ($search_terms as $search_term) {
            $query->where(new Expression($field), 'LIKE', '%' . addcslashes($search_term, '\\%_') . '%');
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
     * @param Builder     $query
     * @param string      $tree_id_field
     * @param array<Tree> $trees
     */
    private function whereTrees(Builder $query, string $tree_id_field, array $trees): void
    {
        $tree_ids = array_map(static function (Tree $tree): int {
            return $tree->id();
        }, $trees);

        $query->whereIn($tree_id_field, $tree_ids);
    }

    /**
     * Find the media object that uses a particular media file.
     *
     * @param string $file
     *
     * @return array<Media>
     */
    public function findMediaObjectsForMediaFile(string $file): array
    {
        return DB::table('media')
            ->join('media_file', static function (JoinClause $join): void {
                $join
                    ->on('media_file.m_file', '=', 'media.m_file')
                    ->on('media_file.m_id', '=', 'media.m_id');
            })
            ->join('gedcom_setting', 'media.m_file', '=', 'gedcom_setting.gedcom_id')
            ->where(new Expression('setting_value || multimedia_file_refn'), '=', $file)
            ->select(['media.*'])
            ->distinct()
            ->get()
            ->map($this->mediaRowMapper())
            ->all();
    }

    /**
     * A closure to filter records by privacy-filtered GEDCOM data.
     *
     * @param array<string> $search_terms
     *
     * @return Closure(GedcomRecord):bool
     */
    private function rawGedcomFilter(array $search_terms): Closure
    {
        return static function (GedcomRecord $record) use ($search_terms): bool {
            // Ignore non-genealogy fields
            $gedcom = preg_replace('/\n\d (?:_UID|_WT_USER) .*/', '', $record->gedcom());

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
     * @return Closure():void
     */
    private function rowLimiter(int $limit = self::MAX_SEARCH_RESULTS): Closure
    {
        return static function () use ($limit): void {
            static $n = 0;

            if (++$n > $limit) {
                $message = I18N::translate('The search returned too many results.');

                throw new HttpServiceUnavailableException($message);
            }
        };
    }

    /**
     * Convert a row from any tree in the families table into a family object.
     *
     * @return Closure(object):Family
     */
    private function familyRowMapper(): Closure
    {
        return function (object $row): Family {
            $tree = $this->tree_service->find((int) $row->f_file);

            return Registry::familyFactory()->mapper($tree)($row);
        };
    }

    /**
     * Convert a row from any tree in the individuals table into an individual object.
     *
     * @return Closure(object):Individual
     */
    private function individualRowMapper(): Closure
    {
        return function (object $row): Individual {
            $tree = $this->tree_service->find((int) $row->i_file);

            return Registry::individualFactory()->mapper($tree)($row);
        };
    }

    /**
     * Convert a row from any tree in the media table into a location object.
     *
     * @return Closure(object):Location
     */
    private function locationRowMapper(): Closure
    {
        return function (object $row): Location {
            $tree = $this->tree_service->find((int) $row->o_file);

            return Registry::locationFactory()->mapper($tree)($row);
        };
    }

    /**
     * Convert a row from any tree in the media table into an media object.
     *
     * @return Closure(object):Media
     */
    private function mediaRowMapper(): Closure
    {
        return function (object $row): Media {
            $tree = $this->tree_service->find((int) $row->m_file);

            return Registry::mediaFactory()->mapper($tree)($row);
        };
    }

    /**
     * Convert a row from any tree in the other table into a note object.
     *
     * @return Closure:Note
     */
    private function noteRowMapper(): Closure
    {
        return function (object $row): Note {
            $tree = $this->tree_service->find((int) $row->o_file);

            return Registry::noteFactory()->mapper($tree)($row);
        };
    }

    /**
     * Convert a row from any tree in the other table into a repository object.
     *
     * @return Closure:Repository
     */
    private function repositoryRowMapper(): Closure
    {
        return function (object $row): Repository {
            $tree = $this->tree_service->find((int) $row->o_file);

            return Registry::repositoryFactory()->mapper($tree)($row);
        };
    }

    /**
     * Convert a row from any tree in the other table into a note object.
     *
     * @return Closure(object):SharedNote
     */
    private function sharedNoteRowMapper(): Closure
    {
        return function (object $row): Note {
            $tree = $this->tree_service->find((int) $row->o_file);

            return Registry::sharedNoteFactory()->mapper($tree)($row);
        };
    }

    /**
     * Convert a row from any tree in the sources table into a source object.
     *
     * @return Closure:Source
     */
    private function sourceRowMapper(): Closure
    {
        return function (object $row): Source {
            $tree = $this->tree_service->find((int) $row->s_file);

            return Registry::sourceFactory()->mapper($tree)($row);
        };
    }

    /**
     * Convert a row from any tree in the other table into a submission object.
     *
     * @return Closure(object):Submission
     */
    private function submissionRowMapper(): Closure
    {
        return function (object $row): Submission {
            $tree = $this->tree_service->find((int) $row->o_file);

            return Registry::submissionFactory()->mapper($tree)($row);
        };
    }

    /**
     * Convert a row from any tree in the other table into a submitter object.
     *
     * @return Closure(object):Submitter
     */
    private function submitterRowMapper(): Closure
    {
        return function (object $row): Submitter {
            $tree = $this->tree_service->find((int) $row->o_file);

            return Registry::submitterFactory()->mapper($tree)($row);
        };
    }
}
