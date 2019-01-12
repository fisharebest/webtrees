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
use Fisharebest\Localization\Locale\LocaleInterface;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Place;
use Fisharebest\Webtrees\Repository;
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
    /** @var LocaleInterface */
    private $locale;

    /**
     * SearchService constructor.
     *
     * @param LocaleInterface $locale
     */
    public function __construct(LocaleInterface $locale)
    {
        $this->locale = $locale;
    }

    /**
     * @param Tree[]   $trees
     * @param string[] $search
     *
     * @return Collection|Family[]
     */
    public function searchFamilies(array $trees, array $search): Collection
    {
        $query = DB::table('families');

        $this->whereTrees($query, 'f_file', $trees);
        $this->whereSearch($query, 'f_gedcom', $search);

        return $query
            ->get()
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
     * @return Collection|Family[]
     */
    public function searchFamilyNames(array $trees, array $search, int $offset = 0, int $limit = PHP_INT_MAX): Collection
    {
        $query = DB::table('families')
            ->join('name AS husb_name', function (JoinClause $join): void {
                $join
                    ->on('husb_name.n_file', '=', 'families.f_file')
                    ->on('husb_name.n_id', '=', 'families.f_husb');
            })
            ->join('name AS wife_name', function (JoinClause $join): void {
                $join
                    ->on('wife_name.n_file', '=', 'families.f_file')
                    ->on('wife_name.n_id', '=', 'families.f_wife');
            })
            ->where('wife_name.n_type', '<>', '_MARNM')
            ->where('husb_name.n_type', '<>', '_MARNM');

        $prefix = DB::connection()->getTablePrefix();
        $field  = DB::raw($prefix . 'husb_name.n_full || ' . $prefix . 'wife_name.n_full');

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
     * @return Collection|Individual[]
     */
    public function searchIndividuals(array $trees, array $search): Collection
    {
        $query = DB::table('individuals');

        $this->whereTrees($query, 'i_file', $trees);
        $this->whereSearch($query, 'i_gedcom', $search);

        return $query
            ->get()
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
     * @return Collection|Individual[]
     */
    public function searchIndividualNames(array $trees, array $search, int $offset = 0, int $limit = PHP_INT_MAX): Collection
    {
        $query = DB::table('individuals')
            ->join('name', function (JoinClause $join): void {
                $join
                    ->on('name.n_file', '=', 'individuals.i_file')
                    ->on('name.n_id', '=', 'individuals.i_id');
            })
            ->orderBy('n_sort')
            ->select(['individuals.*', 'n_sort', 'n_num'])
            ->distinct();

        $this->whereTrees($query, 'i_file', $trees);
        $this->whereSearch($query, 'n_full', $search);

        return $this->paginateQuery($query, Individual::rowMapper(), GedcomRecord::accessFilter(), $offset, $limit);
    }

    /**
     * Search for media objects.
     *
     * @param Tree[]   $trees
     * @param string[] $search
     * @param int    $offset
     * @param int    $limit
     *
     * @return Collection|Media[]
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
     * @return Collection|Note[]
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
     * @return Collection|Repository[]
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
     * @return Collection|Source[]
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
     * @return Collection|Source[]
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
     * @return Collection|GedcomRecord[]
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
     * @return Collection|Place[]
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

        $row_mapper = function (stdClass $row) use ($tree): Place {
            $place = implode(', ', array_filter((array) $row));

            return new Place($place, $tree);
        };

        $filter = function (): bool {
            return true;
        };

        return $this->paginateQuery($query, $row_mapper, $filter, $offset, $limit);
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

        $field = DB::raw($field . ' /*! COLLATE ' . 'utf8_' . $this->locale->collation() . ' */');

        foreach ($search_terms as $search_term) {
            $query->whereContains($field, $search_term);
        }
    }

    /**
     * @param Builder $query
     * @param string  $tree_id_field
     * @param Tree[]  $trees
     */
    private function whereTrees(Builder $query, string $tree_id_field, array $trees): void
    {
        $tree_ids = array_map(function (Tree $tree) {
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
        return function (GedcomRecord $record) use ($search_terms): bool {
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
}
