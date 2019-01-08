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
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use stdClass;

/**
 * Search trees for genealogy records.
 */
class SearchService
{
    /**
     * Search for families by name.
     *
     * @param Tree   $tree
     * @param string $search
     * @param int    $offset
     * @param int    $limit
     *
     * @return Collection|Family[]
     */
    public function searchFamiliesByName(Tree $tree, string $search, int $offset = 0, int $limit = PHP_INT_MAX): Collection
    {
        $prefix = DB::connection()->getTablePrefix();

        $query = DB::table('families')
            ->where('f_file', '=', $tree->id())
            ->join('name AS husb_name', function (JoinClause $join) use ($search): void {
                $join
                    ->on('husb_name.n_file', '=', 'families.f_file')
                    ->on('husb_name.n_id', '=', 'families.f_husb')
                    ->where('husb_name.n_type', '<>', '_MARNM');
            })
            ->join('name AS wife_name', function (JoinClause $join) use ($search): void {
                $join
                    ->on('wife_name.n_file', '=', 'families.f_file')
                    ->on('wife_name.n_id', '=', 'families.f_wife')
                    ->where('wife_name.n_type', '<>', '_MARNM');
            })
            ->whereContains(DB::raw("CONCAT(" . $prefix . "husb_name.n_full, ' ', " . $prefix . "wife_name.n_full)"), $search)
            ->orderBy('husb_name.n_sort')
            ->orderBy('wife_name.n_sort')
            ->select(['families.f_id', 'families.f_gedcom', 'husb_name.n_sort', 'wife_name.n_sort'])
            ->distinct();

        $row_mapper = Family::rowMapper($tree);

        return $this->paginateQuery($query, $row_mapper, $offset, $limit);
    }

    /**
     * Search for individuals by name.
     *
     * @param Tree   $tree
     * @param string $search
     * @param int    $offset
     * @param int    $limit
     *
     * @return Collection|Individual[]
     */
    public function searchIndividualsByName(Tree $tree, string $search, int $offset = 0, int $limit = PHP_INT_MAX): Collection
    {
        $query = DB::table('individuals')
            ->where('i_file', '=', $tree->id())
            ->join('name', function (JoinClause $join) use ($search): void {
                $join
                    ->on('name.n_file', '=', 'individuals.i_file')
                    ->on('name.n_id', '=', 'individuals.i_id')
                    ->whereContains('n_full', $search);
            })
            ->select(['individuals.i_id', 'individuals.i_gedcom', 'n_sort'])
            ->distinct();

        $row_mapper = Individual::rowMapper($tree);

        return $this->paginateQuery($query, $row_mapper, $offset, $limit);
    }

    /**
     * Search for media objects.
     *
     * @param Tree   $tree
     * @param string $search
     * @param int    $offset
     * @param int    $limit
     *
     * @return Collection|Media[]
     */
    public function searchMedia(Tree $tree, string $search, int $offset = 0, int $limit = PHP_INT_MAX): Collection
    {
        $query = DB::table('media')
            ->where('media.m_file', '=', $tree->id())
            ->join('media_file', function (JoinClause $join) use ($search): void {
                $join
                    ->on('media_file.m_file', '=', 'media.m_file')
                    ->on('media_file.m_id', '=', 'media.m_id');
            })
            ->where(function (Builder $query) use ($search): void {
                $query
                    ->whereContains('multimedia_file_refn', $search)
                    ->whereContains('descriptive_title', $search, 'or');
            })
            ->select(['media.m_id', 'media.m_gedcom'])
            ->distinct();

        $row_mapper = Media::rowMapper($tree);

        return $this->paginateQuery($query, $row_mapper, $offset, $limit);
    }

    /**
     * Search for notes.
     *
     * @param Tree   $tree
     * @param string $search
     * @param int    $offset
     * @param int    $limit
     *
     * @return Collection|Note[]
     */
    public function searchNotes(Tree $tree, string $search, int $offset = 0, int $limit = PHP_INT_MAX): Collection
    {
        $query = DB::table('other')
            ->where('o_file', '=', $tree->id())
            ->where('o_type', '=', 'NOTE')
            ->whereContains('o_gedcom', $search)
            ->orderBy('o_id')
            ->select(['o_id', 'o_gedcom']);

        $row_mapper = Note::rowMapper($tree);

        return $this->paginateQuery($query, $row_mapper, $offset, $limit);
    }

    /**
     * Search for repositories.
     *
     * @param Tree   $tree
     * @param string $search
     * @param int    $offset
     * @param int    $limit
     *
     * @return Collection|Repository[]
     */
    public function searchRepositories(Tree $tree, string $search, int $offset = 0, int $limit = PHP_INT_MAX): Collection
    {
        $query = DB::table('other')
            ->where('o_file', '=', $tree->id())
            ->where('o_type', '=', 'REPO')
            ->whereContains('o_gedcom', $search)
            ->orderBy('o_id')
            ->select(['o_id', 'o_gedcom']);

        $row_mapper = Repository::rowMapper($tree);

        return $this->paginateQuery($query, $row_mapper, $offset, $limit);
    }

    /**
    * Search for sources by name.
    *
    * @param Tree   $tree
    * @param string $search
    * @param int    $offset
    * @param int    $limit
    *
    * @return Collection|Source[]
    */
    public function searchSourcesByName(Tree $tree, string $search, int $offset = 0, int $limit = PHP_INT_MAX): Collection
    {
        $query = DB::table('sources')
            ->where('s_file', '=', $tree->id())
            ->whereContains('s_name', $search)
            ->orderBy('s_name')
            ->select(['s_id', 's_gedcom']);

        $row_mapper = Source::rowMapper($tree);

        return $this->paginateQuery($query, $row_mapper, $offset, $limit);
    }

    /**
     * Search for submitters.
     *
     * @param Tree   $tree
     * @param string $search
     * @param int    $offset
     * @param int    $limit
     *
     * @return Collection|GedcomRecord[]
     */
    public function searchSubmitters(Tree $tree, string $search, int $offset = 0, int $limit = PHP_INT_MAX): Collection
    {
        $query = DB::table('other')
            ->where('o_file', '=', $tree->id())
            ->where('o_type', '=', 'SUBM')
            ->whereContains('o_gedcom', $search)
            ->orderBy('o_id')
            ->select(['o_id', 'o_gedcom']);

        $row_mapper = GedcomRecord::rowMapper($tree);

        return $this->paginateQuery($query, $row_mapper, $offset, $limit);
    }

    /**
     * Paginate a search query.
     *
     * @param Builder $query      Searches the database for the desired records.
     * @param Closure $row_mapper Converts a row from the query into a record.
     * @param int     $offset     Skip this many rows.
     * @param int     $limit      Take this many rows.
     *
     * @return Collection
     */
    private function paginateQuery(Builder $query, Closure $row_mapper, int $offset, int $limit): Collection
    {
        $collection = new Collection();

        foreach ($query->cursor() as $row) {
            $record = $row_mapper($row);

            if ($record->canShow()) {
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
}
