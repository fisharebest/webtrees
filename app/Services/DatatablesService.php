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
use Fisharebest\Database\Query\Expression;
use Fisharebest\Database\Query\OrderedExpression;
use Fisharebest\Database\Query\Ordinal;
use Fisharebest\Database\Query\Predicates;
use Fisharebest\Database\Query\QueryBuilder;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Validator;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SortDirection;

use function addcslashes;
use function array_map;
use function count;
use function strtr;

/**
 * Paginate and search queries for datatables.
 *
 * @link https://www.datatables.net/usage/server-side
 */
class DatatablesService
{
    /**
     * Apply filtering and pagination to a collection, and generate a response suitable for datatables.
     *
     * @param ServerRequestInterface   $request        Includes the datatables request parameters.
     * @param Collection<int,mixed>    $collection     All the data.
     * @param array<string>|array<int> $search_columns The names of searchable columns.
     * @param array<string>|array<int> $sort_columns   Sort column mapping.
     * @param Closure                  $callback       Converts a row-object to an array-of-columns.
     */
    public function handleCollection(ServerRequestInterface $request, Collection $collection, array $search_columns, array $sort_columns, Closure $callback): ResponseInterface
    {
        $search = Validator::parsedBody($request)->array('search')['value'] ?? '';
        $start  = Validator::parsedBody($request)->integer('start', 0);
        $length = Validator::parsedBody($request)->integer('length', 0);
        $order  = Validator::parsedBody($request)->arrayArray('order');
        $draw   = Validator::parsedBody($request)->integer('draw', 0);

        // Count unfiltered records
        $recordsTotal = $collection->count();

        // Filtering
        if ($search !== '') {
            $collection = $collection->filter(static function (array $row) use ($search, $search_columns): bool {
                foreach ($search_columns as $search_column) {
                    if (stripos($row[$search_column], $search) !== false) {
                        return true;
                    }
                }

                return false;
            });
        }

        // Sorting
        if ($order !== []) {
            $collection = $collection->sort(static function (array $row1, array $row2) use ($order, $sort_columns): int {
                foreach ($order as $column) {
                    $key = $sort_columns[$column['column']];
                    $dir = $column['dir'];

                    if ($dir === 'asc') {
                        $comparison = $row1[$key] <=> $row2[$key];
                    } else {
                        $comparison = $row2[$key] <=> $row1[$key];
                    }

                    if ($comparison !== 0) {
                        return $comparison;
                    }
                }

                return 0;
            });
        }

        // Paginating
        $recordsFiltered = $collection->count();

        if ($length > 0) {
            $data = $collection->slice($start, $length);
        } else {
            $data = $collection;
        }

        $data = $data->map($callback)->values()->all();

        return response([
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }

    /**
     * Apply filtering and pagination to a database query, and generate a response suitable for datatables.
     *
     * @param ServerRequestInterface                            $request        Includes the datatables request parameters.
     * @param QueryBuilder                                      $query          A query to fetch the unfiltered rows and columns.
     * @param array<string>                                     $search_columns The names of searchable columns.
     * @param array<string|Expression>                          $sort_columns   Sort column mapping.
     * @param Closure                                           $callback       Converts a row-object to an array-of-columns.
     * @param list<Expression|Ordinal|OrderedExpression|string> $default_sort   Applied after any user-specified sort.
     */
    public function handleQuery(
        ServerRequestInterface $request,
        QueryBuilder $query,
        array $search_columns,
        array $sort_columns,
        Closure $callback,
        array $default_sort,
    ): ResponseInterface {
        $search = Validator::parsedBody($request)->array('search')['value'] ?? '';
        $start  = Validator::parsedBody($request)->integer('start', 0);
        $length = Validator::parsedBody($request)->integer('length', 0);
        $order  = Validator::parsedBody($request)->arrayArray('order');
        $draw   = Validator::parsedBody($request)->integer('draw', 0);

        // Count unfiltered records
        $recordsTotal = $query->count();

        // Filtering
        if ($search !== '' && $search_columns !== []) {
            $query = $query->whereNested(static function (Predicates $p) use ($search, $search_columns): Predicates {
                $like = '%' . addcslashes($search, '\\%_') . '%';
                $like = strtr($like, [' ' => '%']);

                foreach ($search_columns as $search_column) {
                    $p = $p->orLike($search_column, $like);
                }

                return $p;
            });
        }

        // Sorting
        $sort_expressions = [];

        foreach ($order as $value) {
            // Columns in datatables are numbered from zero.
            // Columns in MySQL are numbered starting with one.
            // If not specified, the Nth table column maps onto the Nth query column.
            $sort_column = $sort_columns[$value['column']] ?? DB::ordinal(position: 1 + (int) $value['column']);

            $sort_expressions[] = DB::order(
                $sort_column,
                $value['dir'] === 'asc' ? SortDirection::Ascending : SortDirection::Descending,
            );
        }

        $query = $query->orderBy(...$sort_expressions, ...$default_sort);

        // Paginating
        if ($length > 0) {
            $recordsFiltered = $query->count();

            $data = $query->offset($start)->limit($length)->get();
        } else {
            $data = $query->get();

            $recordsFiltered = count($data);
        }

        $data = array_map($callback, $data);

        return response([
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }
}
