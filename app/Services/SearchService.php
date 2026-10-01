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

namespace Fisharebest\Webtrees\Services;

use Closure;
use Fisharebest\Database\Query\CaseSensitivity;
use Fisharebest\Database\Query\Expression;
use Fisharebest\Database\Query\QueryBuilder;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\DB;
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
use Illuminate\Support\Collection;

use function addcslashes;
use function array_filter;
use function array_map;
use function array_unique;
use function explode;
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
    protected const int MAX_SEARCH_RESULTS = 5000;

    public function __construct(
        private readonly TreeService $tree_service,
    ) {
    }

    /**
     * @param array<Tree>   $trees
     * @param array<string> $search
     *
     * @return Collection<int,Family>
     */
    public function searchFamilies(array $trees, array $search): Collection
    {
        return DB::query()
            ->select('f_file', 'f_id', 'f_gedcom')
            ->from('families')
            ->whereIn('f_file', array_map(static fn (Tree $tree): int => $tree->id(), $trees))
            ->whereNested($this->whereSearch('f_gedcom', $search))
            ->get(in: Collection::class)
            ->map($this->rowLimiter())
            ->map($this->familyRowMapper())
            ->filter(GedcomRecord::accessFilter())
            ->filter($this->rawGedcomFilter(array_map(I18N::language()->normalize(...), $search)));
    }

    /**
     * Search for families by name.
     *
     * @param array<Tree>   $trees
     * @param array<string> $search
     *
     * @return Collection<int,Family>
     */
    public function searchFamilyNames(array $trees, array $search, int $offset = 0, int $limit = PHP_INT_MAX): Collection
    {
        $query = DB::query()
            ->select('f_file', 'f_id', 'f_gedcom')
            ->distinct()
            ->from('families')
            ->whereIn('f_file', array_map(static fn (Tree $tree): int => $tree->id(), $trees))
            ->leftJoin('name AS h', ['h.n_file' => 'families.f_file', 'h.n_id' => 'families.f_husb'])
            ->leftJoin('name AS w', ['w.n_file' => 'families.f_file', 'w.n_id' => 'families.f_wife'])
            ->whereNotEqual('h.n_type', '_MARNM')
            ->whereNotEqual('w.n_type', '_MARNM')
            ->whereNested($this->whereSearch(DB::concat('h.n_full', 'w.n_full'), $search))
            ->orderBy('h.n_sort')
            ->orderBy('w.n_sort');

        return $this->paginateQuery($query, $this->familyRowMapper(), GedcomRecord::accessFilter(), $offset, $limit);
    }

    /**
     * @return Collection<int,Family>
     */
    public function searchFamiliesInPlace(Place $place): Collection
    {
        return DB::query()
            ->select('f_file', 'f_id', 'f_gedcom')
            ->from('families')
            ->join('placelinks', ['f_file' => 'pl_file', 'f_id' => 'pl_gid'])
            ->whereEqual('f_file', $place->tree()->id())
            ->whereEqual('pl_p_id', $place->id())
            ->get(in: Collection::class)
            ->map($this->rowLimiter())
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
        return DB::query()
            ->select('*')
            ->from('individuals')
            ->whereIn('i_file', array_map(static fn (Tree $tree): int => $tree->id(), $trees))
            ->whereNested($this->whereSearch('i_gedcom', $search))
            ->get(in: Collection::class)
            ->map($this->rowLimiter())
            ->map($this->individualRowMapper())
            ->filter(GedcomRecord::accessFilter())
            ->filter($this->rawGedcomFilter(array_map(I18N::language()->normalize(...), $search)));
    }

    /**
     * Search for individuals by name.
     *
     * @param array<Tree>   $trees
     * @param array<string> $search
     *
     * @return Collection<int,Individual>
     */
    public function searchIndividualNames(array $trees, array $search, int $offset = 0, int $limit = PHP_INT_MAX): Collection
    {
        $query = DB::query()
            ->select('i_file', 'i_id', 'i_gedcom')
            ->from('individuals')
            ->join('name', ['n_file' => 'i_file', 'n_id' => 'i_id'])
            ->whereIn('i_file', array_map(static fn (Tree $tree): int => $tree->id(), $trees))
            ->whereNested($this->whereSearch('n_full', $search))
            ->orderBy('n_sort');

        return $this->paginateQuery($query, $this->individualRowMapper(), GedcomRecord::accessFilter(), $offset, $limit);
    }

    /**
     * @return Collection<int,Individual>
     */
    public function searchIndividualsInPlace(Place $place): Collection
    {
        return DB::query()
            ->select('i_file', 'i_id', 'i_gedcom')
            ->from('individuals')
            ->join('placelinks', ['i_file' => 'pl_file', 'i_id' => 'pl_gid'])
            ->whereEqual('i_file', $place->tree()->id())
            ->whereEqual('pl_p_id', $place->id())
            ->get(in: Collection::class)
            ->map($this->rowLimiter())
            ->map($this->individualRowMapper())
            ->filter(GedcomRecord::accessFilter());
    }

    /**
     * Search for submissions.
     *
     * @param array<Tree>   $trees
     * @param array<string> $search
     *
     * @return Collection<int,Location>
     */
    public function searchLocations(array $trees, array $search, int $offset = 0, int $limit = PHP_INT_MAX): Collection
    {
        $query = DB::query()
            ->select('o_file', 'o_id', 'o_gedcom')
            ->from('other')
            ->whereEqual('o_type', Location::RECORD_TYPE)
            ->whereIn('o_file', array_map(static fn (Tree $tree): int => $tree->id(), $trees))
            ->whereNested($this->whereSearch('o_gedcom', $search));

        return $this->paginateQuery($query, $this->locationRowMapper(), GedcomRecord::accessFilter(), $offset, $limit);
    }

    /**
     * Search for media objects.
     *
     * @param array<Tree>   $trees
     * @param array<string> $search
     *
     * @return Collection<int,Media>
     */
    public function searchMedia(array $trees, array $search, int $offset = 0, int $limit = PHP_INT_MAX): Collection
    {
        $query = DB::query()
            ->select('m_file', 'm_id', 'm_gedcom')
            ->from('media')
            ->whereIn('m_file', array_map(static fn (Tree $tree): int => $tree->id(), $trees))
            ->whereNested($this->whereSearch('m_gedcom', $search));

        return $this->paginateQuery($query, $this->mediaRowMapper(), GedcomRecord::accessFilter(), $offset, $limit);
    }

    /**
     * Search for notes.
     *
     * @param array<Tree>   $trees
     * @param array<string> $search
     *
     * @return Collection<int,Note>
     */
    public function searchNotes(array $trees, array $search, int $offset = 0, int $limit = PHP_INT_MAX): Collection
    {
        $query = DB::query()
            ->select('o_file', 'o_id', 'o_gedcom')
            ->from('other')
            ->whereEqual('o_type', Note::RECORD_TYPE)
            ->whereIn('o_file', array_map(static fn (Tree $tree): int => $tree->id(), $trees))
            ->whereNested($this->whereSearch('o_gedcom', $search));

        return $this->paginateQuery($query, $this->noteRowMapper(), GedcomRecord::accessFilter(), $offset, $limit);
    }

    /**
     * Search for notes.
     *
     * @param array<Tree>   $trees
     * @param array<string> $search
     *
     * @return Collection<int,SharedNote>
     */
    public function searchSharedNotes(array $trees, array $search, int $offset = 0, int $limit = PHP_INT_MAX): Collection
    {
        $query = DB::query()
            ->select('o_file', 'o_id', 'o_gedcom')
            ->from('other')
            ->whereEqual('o_type', SharedNote::RECORD_TYPE)
            ->whereIn('o_file', array_map(static fn (Tree $tree): int => $tree->id(), $trees))
            ->whereNested($this->whereSearch('o_gedcom', $search));

        return $this->paginateQuery($query, $this->sharedNoteRowMapper(), GedcomRecord::accessFilter(), $offset, $limit);
    }

    /**
     * Search for repositories.
     *
     * @param array<Tree>   $trees
     * @param array<string> $search
     *
     * @return Collection<int,Repository>
     */
    public function searchRepositories(array $trees, array $search, int $offset = 0, int $limit = PHP_INT_MAX): Collection
    {
        $query = DB::query()
            ->select('o_file', 'o_id', 'o_gedcom')
            ->from('other')
            ->whereEqual('o_type', Repository::RECORD_TYPE)
            ->whereIn('o_file', array_map(static fn (Tree $tree): int => $tree->id(), $trees))
            ->whereNested($this->whereSearch('o_gedcom', $search));

        return $this->paginateQuery($query, $this->repositoryRowMapper(), GedcomRecord::accessFilter(), $offset, $limit);
    }

    /**
     * Search for sources.
     *
     * @param array<Tree>   $trees
     * @param array<string> $search
     *
     * @return Collection<int,Source>
     */
    public function searchSources(array $trees, array $search, int $offset = 0, int $limit = PHP_INT_MAX): Collection
    {
        $query = DB::query()
            ->select('s_file', 's_id', 's_gedcom')
            ->from('sources')
            ->whereIn('s_file', array_map(static fn (Tree $tree): int => $tree->id(), $trees))
            ->whereNested($this->whereSearch('s_gedcom', $search))
            ->orderBy('s_name');

        return $this->paginateQuery($query, $this->sourceRowMapper(), GedcomRecord::accessFilter(), $offset, $limit);
    }

    /**
     * Search for sources by name.
     *
     * @param array<Tree>   $trees
     * @param array<string> $search
     *
     * @return Collection<int,Source>
     */
    public function searchSourcesByName(array $trees, array $search, int $offset = 0, int $limit = PHP_INT_MAX): Collection
    {
        $query = DB::query()
            ->select('s_file', 's_id', 's_gedcom')
            ->from('sources')
            ->whereIn('s_file', array_map(static fn (Tree $tree): int => $tree->id(), $trees))
            ->whereNested($this->whereSearch('s_name', $search))
            ->orderBy('s_name');

        return $this->paginateQuery($query, $this->sourceRowMapper(), GedcomRecord::accessFilter(), $offset, $limit);
    }

    /**
     * Search for sources.
     *
     * @param array<Tree>   $trees
     * @param array<string> $search
     *
     * @return Collection<int,string>
     */
    public function searchSurnames(array $trees, array $search, int $offset = 0, int $limit = PHP_INT_MAX): Collection
    {
        $query = DB::query()
            ->from('name')
            ->whereIn('n_file', array_map(static fn (Tree $tree): int => $tree->id(), $trees))
            ->whereNested($this->whereSearch('n_surname', $search))
            ->groupBy('n_surname')
            ->orderBy('n_surname')
            ->skip($offset)
            ->take($limit);

        return new Collection($query->pluck('n_surname'));
    }

    /**
     * Search for submissions.
     *
     * @param array<Tree>   $trees
     * @param array<string> $search
     *
     * @return Collection<int,Submission>
     */
    public function searchSubmissions(array $trees, array $search, int $offset = 0, int $limit = PHP_INT_MAX): Collection
    {
        $query = DB::query()
            ->select('o_file', 'o_id', 'o_gedcom')
            ->from('other')
            ->whereEqual('o_type', Submission::RECORD_TYPE)
            ->whereIn('o_file', array_map(static fn (Tree $tree): int => $tree->id(), $trees))
            ->whereNested($this->whereSearch('o_gedcom', $search));

        return $this->paginateQuery($query, $this->submissionRowMapper(), GedcomRecord::accessFilter(), $offset, $limit);
    }

    /**
     * Search for submitters.
     *
     * @param array<Tree>   $trees
     * @param array<string> $search
     *
     * @return Collection<int,Submitter>
     */
    public function searchSubmitters(array $trees, array $search, int $offset = 0, int $limit = PHP_INT_MAX): Collection
    {
        $query = DB::query()
            ->select('o_file', 'o_id', 'o_gedcom')
            ->from('other')
            ->whereEqual('o_type', Submitter::RECORD_TYPE)
            ->whereIn('o_file', array_map(static fn (Tree $tree): int => $tree->id(), $trees))
            ->whereNested($this->whereSearch('o_gedcom', $search));

        return $this->paginateQuery($query, $this->submitterRowMapper(), GedcomRecord::accessFilter(), $offset, $limit);
    }

    /**
     * Search for places.
     *
     * @return Collection<int,Place>
     */
    public function searchPlaces(Tree $tree, string $search, int $offset = 0, int $limit = PHP_INT_MAX): Collection
    {
        $query = DB::query()
            ->withRecursiveCte(
                'cte',
                DB::query()
                    ->select('p_parent_id', 'p_place AS full_name')
                    ->from('places')
                    ->whereEqual('p_file', $tree->id())
                    ->whereLike('p_place', '%' . addcslashes($search, '\\%_') . '%', case_sensitivity: CaseSensitivity::Insensitive),
                static fn (string $name): QueryBuilder => DB::query()
                    ->select('places.p_parent_id', DB::concat('p_place', DB::value(', '), 'full_name'))
                    ->from('places')
                    ->join('cte', ['cte.p_parent_id' => 'p_id']),
            )
            ->select('full_name')
            ->from('cte')
            ->whereEqual('p_parent_id', 0);

        $row_mapper = static fn (object $row): Place => new Place($row->full_name, $tree);
        $filter     = static fn (): bool => true;

        return $this->paginateQuery($query, $row_mapper, $filter, $offset, $limit);
    }

    /**
     * @param array<string,string> $fields
     * @param array<string,string> $modifiers
     *
     * @return Collection<int,Individual>
     */
    public function searchIndividualsAdvanced(Tree $tree, array $fields, array $modifiers): Collection
    {
        $fields = array_filter($fields, static fn (string $x): bool => $x !== '');

        $query = DB::query()
            ->select('i_file', 'i_id', 'i_gedcom')
            ->distinct()
            ->from('individuals')
            ->whereEqual('i_file', $tree->id());

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
            $query = $query
                ->join('link AS l1', ['l1.l_file' => 'i_file', 'l1.l_from' => 'i_id'])
                ->whereEqual('l1.type', 'FAMC');

            if ($father_name) {
                $query = $query
                    ->join('link AS l2', ['l2.l_file' => 'l1.l_file', 'l2.l_from' => 'l1.l_from'])
                    ->whereEqual('l2.l_type', 'HUSB')
                    ->join('name AS father_name', ['father_name.n_file' => 'l3.l_file', 'father_name.n_id' => 'l3.l_to']);
            }

            if ($mother_name) {
                $query = $query
                    ->join('link AS l3', ['l3.l_file' => 'l1.l_file', 'l3.l_from' => 'l1.l_from'])
                    ->whereEqual('l2.l_type', 'WIFE')
                    ->join('name AS mother_name', ['mother_name.n_file' => 'l3.l_file', 'mother_name.n_id' => 'l3.l_to']);
            }
        }

        if ($spouse_family) {
            $query = $query
                ->join('link AS l4', ['l4.l_file' => 'i_file', 'l4.l_from' => 'i_id'])
                ->whereEqual('l4.l_type', 'FAMS')
                ->join('families', ['f_file' => 'l4.l_file', 'f_id' => 'l4.l_to']);
        }

        if ($indi_name) {
            $query = $query->join('name AS individual_name', ['individual_name.n_file' => 'i_file', 'individual_name.n_id' => 'i_id']);
        }

        foreach (array_unique($indi_dates) as $indi_date) {
            $query = $query->join('dates AS date_' . $indi_date, ['date_' . $indi_date . '.d_file' => 'i_file', 'date_' . $indi_date . '.d_gid' => 'i_id']);
        }

        foreach (array_unique($fam_dates) as $fam_date) {
            $query = $query->join('dates AS date_' . $fam_date, ['date_' . $fam_date . '.d_file' => 'f_file', 'date_' . $fam_date . '.d_gid' => 'f_id']);
        }

        if ($indi_plac) {
            $query = $query->join('placelinks AS individual_placelinks', ['individual_placelinks.pl_file' => 'i_file', 'individual_placelinks.pl_gid' => 'i_id']);
            $query = $query->join('places AS individual_places', ['individual_places.p_file' => 'individual_placelinks.pl_file', 'individual_places.p_id' => 'individual_placelinks.pl_p_id']);
        }

        if ($fam_plac) {
            $query = $query->join('placelinks AS family_placelinks', ['family_placelinks.pl_file' => 'i_file', 'family_placelinks.pl_gid' => 'i_id']);
            $query = $query->join('places AS family_places', ['family_places.p_file' => 'family_placelinks.pl_file', 'family_places.p_id' => 'family_placelinks.pl_p_id']);
        }

        foreach ($fields as $field_name => $field_value) {
            $parts = explode(':', $field_name . ':::');
            if (str_starts_with($field_name, 'INDI:NAME:')) {
                switch ($field_name) {
                    case 'INDI:NAME:GIVN':
                        switch ($modifiers[$field_name]) {
                            case 'EXACT':
                                $query = $query->whereEqual('individual_name.n_givn', $field_value);
                                break;
                            case 'BEGINS':
                                $query = $query->whereLike('individual_name.n_givn', $field_value . '%', case_sensitivity: CaseSensitivity::Insensitive);
                                break;
                            case 'CONTAINS':
                                $query = $query->whereLike('individual_name.n_givn', '%' . $field_value . '%', case_sensitivity: CaseSensitivity::Insensitive);
                                break;
                            case 'SDX_STD':
                                $sdx = Soundex::russell($field_value);
                                if ($sdx !== '') {
                                    $query = $this->wherePhonetic($query, 'individual_name.n_soundex_givn_std', $sdx);
                                } else {
                                    // No phonetic content? Use a substring match
                                    $query = $query->whereLike('individual_name.n_givn', '%' . $field_value . '%', case_sensitivity: CaseSensitivity::Insensitive);
                                }
                                break;
                            case 'SDX': // SDX uses DM by default.
                            case 'SDX_DM':
                                $sdx = Soundex::daitchMokotoff($field_value);
                                if ($sdx !== '') {
                                    $query = $this->wherePhonetic($query, 'individual_name.n_soundex_givn_std', $sdx);
                                } else {
                                    // No phonetic content? Use a substring match
                                    $query = $query->whereLike('individual_name.n_givn', '%' . $field_value . '%', case_sensitivity: CaseSensitivity::Insensitive);
                                }
                                break;
                        }
                        unset($fields[$field_name]);
                        break;
                    case 'INDI:NAME:SURN':
                        switch ($modifiers[$field_name]) {
                            case 'EXACT':
                                $query = $query->whereNested(static fn (QueryBuilder $query): QueryBuilder => $query
                                    ->whereEqual('individual_name.n_surn', $field_value)
                                    ->orWhereEqual('individual_name.n_surname', $field_value));
                                break;
                            case 'BEGINS':
                                $query = $query->whereNested(static fn (QueryBuilder $query): QueryBuilder => $query
                                    ->whereLike('individual_name.n_surn', $field_value . '%', case_sensitivity: CaseSensitivity::Insensitive)
                                    ->orWhereLike('individual_name.n_surname', $field_value . '%', case_sensitivity: CaseSensitivity::Insensitive));
                                break;
                            case 'CONTAINS':
                                $query = $query->whereNested(static fn (QueryBuilder $query): QueryBuilder => $query
                                    ->whereLike('individual_name.n_surn', '%' . $field_value . '%', case_sensitivity: CaseSensitivity::Insensitive)
                                    ->orWhereLike('individual_name.n_surname', '%' . $field_value . '%', case_sensitivity: CaseSensitivity::Insensitive));
                                break;
                            case 'SDX_STD':
                                $sdx = Soundex::russell($field_value);
                                if ($sdx !== '') {
                                    $query = $this->wherePhonetic($query, 'individual_name.n_soundex_surn_std', $sdx);
                                } else {
                                    // No phonetic content? Use a substring match
                                    $query = $query->whereNested(static fn (QueryBuilder $query): QueryBuilder => $query
                                        ->whereLike('individual_name.n_surn', '%' . $field_value . '%', case_sensitivity: CaseSensitivity::Insensitive)
                                        ->orWhereLike('individual_name.n_surname', '%' . $field_value . '%', case_sensitivity: CaseSensitivity::Insensitive));
                                }
                                break;
                            case 'SDX': // SDX uses DM by default.
                            case 'SDX_DM':
                                $sdx = Soundex::daitchMokotoff($field_value);
                                if ($sdx !== '') {
                                    $query = $this->wherePhonetic($query, 'individual_name.n_soundex_surn_dm', $sdx);
                                } else {
                                    // No phonetic content? Use a substring match
                                    $query = $query->whereNested(static fn (QueryBuilder $query): QueryBuilder => $query
                                    ->whereLike('individual_name.n_surn', '%' . $field_value . '%', case_sensitivity: CaseSensitivity::Insensitive)
                                    ->orWhereLike('individual_name.n_surname', '%' . $field_value . '%', case_sensitivity: CaseSensitivity::Insensitive));
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
                        $query = $query->whereLike('individuals.i_gedcom', $like, case_sensitivity: CaseSensitivity::Insensitive);
                        break;
                }
            } elseif (str_starts_with($field_name, 'INDI:') && str_ends_with($field_name, ':DATE')) {
                $date = new Date($field_value);
                if ($date->isOK()) {
                    $delta = 365 * (int) ($modifiers[$field_name] ?? 0);
                    $query = $query
                        ->whereEqual('date_' . $parts[1] . '.d_fact', $parts[1])
                        ->whereGreaterThanOrEqual('date_' . $parts[1] . '.d_julianday1', $date->minimumJulianDay() - $delta)
                        ->whereLessThanOrEqual('date_' . $parts[1] . '.d_julianday2', $date->maximumJulianDay() + $delta);
                }
                unset($fields[$field_name]);
            } elseif (str_starts_with($field_name, 'FAM:') && str_ends_with($field_name, ':DATE')) {
                $date = new Date($field_value);
                if ($date->isOK()) {
                    $delta = 365 * (int) ($modifiers[$field_name] ?? 0);
                    $query = $query
                        ->whereEqual('date_' . $parts[1] . '.d_fact', $parts[1])
                        ->whereGreaterThanOrEqual('date_' . $parts[1] . '.d_julianday1', $date->minimumJulianDay() - $delta)
                        ->whereLessThanOrEqual('date_' . $parts[1] . '.d_julianday2', $date->maximumJulianDay() + $delta);
                }
                unset($fields[$field_name]);
            } elseif (str_starts_with($field_name, 'INDI:') && str_ends_with($field_name, ':PLAC')) {
                // SQL can only link a place to a person/family, not to an event.
                $query = $query->whereEqual('individual_places.p_id', $field_value);
            } elseif (str_starts_with($field_name, 'FAM:') && str_ends_with($field_name, ':PLAC')) {
                // SQL can only link a place to a person/family, not to an event.
                $query = $query->whereEqual('family_places.p_id', $field_value);
            } elseif (str_starts_with($field_name, 'MOTHER:NAME:') || str_starts_with($field_name, 'FATHER:NAME:')) {
                $table = str_starts_with($field_name, 'FATHER:NAME:') ? 'father_name' : 'mother_name';
                switch ($parts[2]) {
                    case 'GIVN':
                        switch ($modifiers[$field_name]) {
                            case 'EXACT':
                                $query = $query->whereEqual($table . '.n_givn', $field_value);
                                break;
                            case 'BEGINS':
                                $query = $query->whereLike($table . '.n_givn', $field_value . '%', case_sensitivity: CaseSensitivity::Insensitive);
                                break;
                            case 'CONTAINS':
                                $query = $query->whereLike($table . '.n_givn', '%' . $field_value . '%', case_sensitivity: CaseSensitivity::Insensitive);
                                break;
                            case 'SDX_STD':
                                $sdx = Soundex::russell($field_value);
                                if ($sdx !== '') {
                                    $query = $this->wherePhonetic($query, 'individual_name.n_soundex_givn_std', $sdx);
                                } else {
                                    // No phonetic content? Use a substring match
                                    $query->whereLike($table . '.n_givn', '%' . $field_value . '%', case_sensitivity: CaseSensitivity::Insensitive);
                                }
                                break;
                            case 'SDX': // SDX uses DM by default.
                            case 'SDX_DM':
                                $sdx = Soundex::daitchMokotoff($field_value);
                                if ($sdx !== '') {
                                    $query = $this->wherePhonetic($query, 'individual_name.n_soundex_givn_dm', $sdx);
                                } else {
                                    // No phonetic content? Use a substring match
                                    $query->whereLike($table . '.n_givn', '%' . $field_value . '%', case_sensitivity: CaseSensitivity::Insensitive);
                                }
                                break;
                        }
                        break;
                    case 'SURN':
                        switch ($modifiers[$field_name]) {
                            case 'EXACT':
                                $query->whereEqual($table . '.n_surn', $field_value);
                                break;
                            case 'BEGINS':
                                $query->whereLike($table . '.n_surn', $field_value . '%', case_sensitivity: CaseSensitivity::Insensitive);
                                break;
                            case 'CONTAINS':
                                $query->whereLike($table . '.n_surn', '%' . $field_value . '%', case_sensitivity: CaseSensitivity::Insensitive);
                                break;
                            case 'SDX_STD':
                                $sdx = Soundex::russell($field_value);
                                if ($sdx !== '') {
                                    $query = $this->wherePhonetic($query, 'individual_name.n_soundex_surn_std', $sdx);
                                } else {
                                    // No phonetic content? Use a substring match
                                    $query->whereLike($table . '.n_surn', '%' . $field_value . '%', case_sensitivity: CaseSensitivity::Insensitive);
                                }
                                break;
                            case 'SDX': // SDX uses DM by default.
                            case 'SDX_DM':
                                $sdx = Soundex::daitchMokotoff($field_value);
                                if ($sdx !== '') {
                                    $query = $this->wherePhonetic($query, 'individual_name.n_soundex_surn_dm', $sdx);
                                } else {
                                    // No phonetic content? Use a substring match
                                    $query->whereLike($table . '.n_surn', '%' . $field_value . '%', case_sensitivity: CaseSensitivity::Insensitive);
                                }
                                break;
                        }
                        break;
                }
                unset($fields[$field_name]);
            } elseif (str_starts_with($field_name, 'FAM:')) {
                // e.g. searches for occupation, religion, note, etc.
                // Initial matching only.  Need PHP to apply filter.
                $query->whereLike('spouse_families.f_gedcom', "%\n1 " . $parts[1] . ' %' . $field_value . '%', case_sensitivity: CaseSensitivity::Insensitive);
            } elseif (str_starts_with($field_name, 'INDI:') && str_ends_with($field_name, ':TYPE')) {
                // Initial matching only.  Need PHP to apply filter.
                $query->whereLike('individuals.i_gedcom', "%\n1 " . $parts[1] . "%\n2 TYPE %" . $field_value . '%', case_sensitivity: CaseSensitivity::Insensitive);
            } elseif (str_starts_with($field_name, 'INDI:')) {
                // e.g. searches for occupation, religion, note, etc.
                // Initial matching only.  Need PHP to apply filter.
                $query->whereLike('individuals.i_gedcom', "%\n1 " . $parts[1] . '%' . $parts[2] . '%' . $field_value . '%', case_sensitivity: CaseSensitivity::Insensitive);
            }
        }

        return $query
            ->get(in: Collection::class)
            ->map($this->rowLimiter())
            ->map($this->individualRowMapper())
            ->filter(GedcomRecord::accessFilter())
            ->filter(static function (Individual $individual) use ($fields, $tree): bool {
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

                    if (str_starts_with($field_name, 'INDI:') && str_ends_with($field_name, ':PLAC')) {
                        $place = Place::find((int) $field_value, $tree);

                        foreach ($individual->facts([$parts[1]]) as $fact) {
                            if ($fact->place()->gedcomName() === $place->gedcomName() || str_ends_with($fact->place()->gedcomName(), ', ' . $place->gedcomName())) {
                                continue 2;
                            }
                        }
                        return false;
                    }

                    if (str_starts_with($field_name, 'FAM:') && str_ends_with($field_name, ':PLAC')) {
                        $place = Place::find((int) $field_value, $tree);

                        foreach ($individual->spouseFamilies() as $family) {
                            foreach ($family->facts([$parts[1]]) as $fact) {
                                if ($fact->place()->gedcomName() === $place->gedcomName() || str_ends_with($fact->place()->gedcomName(), ', ' . $place->gedcomName())) {
                                    continue 3;
                                }
                            }
                        }
                        return false;
                    }

                    $regex = '/' . preg_quote($field_value, '/') . '/i';

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
     * @param array<Tree> $trees
     *
     * @return Collection<int,Individual>
     */
    public function searchIndividualsPhonetic(string $soundex, string $lastname, string $firstname, string $place, array $trees): Collection
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

        $query = DB::query()
            ->from('individuals')
            ->select('i_file', 'i_id', 'i_gedcom')
            ->distinct()
            ->whereIn('i_file', array_map(static fn (Tree $tree): int => $tree->id(), $trees));

        if ($plac_sdx !== '') {
            $query = $query
                ->join('placelinks', ['pl_file' => 'i_file', 'pl_gid' => 'i_id'])
                ->join('places', ['p_file' => 'pl_file', 'p_id' => 'pl_p_id']);

            $query = $this->wherePhonetic($query, $plac_field, $plac_sdx);
        }

        if ($givn_sdx !== '' || $surn_sdx !== '') {
            $query = $query->join('name', ['n_file' => 'i_file', 'n_id' => 'i_id']);
            $query = $this->wherePhonetic($query, $givn_field, $givn_sdx);
            $query = $this->wherePhonetic($query, $surn_field, $surn_sdx);
        }

        return $query
            ->get(in: Collection::class)
            ->map($this->rowLimiter())
            ->map($this->individualRowMapper())
            ->filter(GedcomRecord::accessFilter());
    }

    /**
     * Paginate a search query.
     *
     * @param QueryBuilder $query      Searches the database for the desired records.
     * @param Closure      $row_mapper Converts a row from the query into a record.
     * @param int          $offset     Skip this many rows.
     * @param int          $limit      Take this many rows.
     *
     * @return Collection<int,mixed>
     */
    private function paginateQuery(QueryBuilder $query, Closure $row_mapper, Closure $row_filter, int $offset, int $limit): Collection
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
     * Apply search filters to a SQL query column.
     *
     * @param array<string> $search_terms
     */
    private function whereSearch(string|Expression $column, array $search_terms): QueryBuilder
    {
        $subquery = DB::query();

        foreach ($search_terms as $search_term) {
            $subquery = $subquery->whereLike($column, '%' . addcslashes($search_term, '\\%_') . '%', case_sensitivity: CaseSensitivity::Insensitive);
        }

        return $subquery;
    }

    /**
     * Apply soundex search filters to a SQL query column.
     */
    private function wherePhonetic(QueryBuilder $query, string $field, string $soundex): QueryBuilder
    {
        return $query->whereNested(static function (QueryBuilder $q) use ($soundex, $field): QueryBuilder {
            foreach (explode(':', $soundex) as $sdx) {
                $q = $q->orWhereLike($field, '%' . $sdx . '%');
            }

            return $q;
        });
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
            $regex = '/^([0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}|[0-9A-F]{36})$/i';

            if (array_filter($search_terms, static fn (string $term): bool => preg_match($regex, $term) === 1) !== []) {
                $gedcom = preg_replace('/\n\d _WT_USER .*/', '', $record->gedcom());
            } else {
                $gedcom = preg_replace('/\n\d (UID|_UID|_WT_USER) .*/', '', $record->gedcom());
            }

            // Ignore matches in links
            $gedcom = preg_replace('/\n\d ' . Gedcom::REGEX_TAG . '( @' . Gedcom::REGEX_XREF . '@)?/', '', $gedcom);

            // Remove accents when searching using PHP.
            $gedcom = I18N::language()->normalize($gedcom);

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
     * @template T
     *
     * @return Closure(T):T
     */
    private function rowLimiter(int $limit = self::MAX_SEARCH_RESULTS): Closure
    {
        return static function (mixed $value) use ($limit): mixed {
            static $n = 0;

            if (++$n > $limit) {
                $message = I18N::translate('The search returned too many results.');

                throw new HttpServiceUnavailableException($message);
            }

            return $value;
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
     * @return Closure(object):Note
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
     * @return Closure(object):Repository
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
     * @return Closure(object):Source
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
