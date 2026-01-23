<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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
use Fisharebest\Webtrees\Validator;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function addcslashes;
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
     *
     * @return ResponseInterface
     */
    public function handleCollection(ServerRequestInterface $request, Collection $collection, array $search_columns, array $sort_columns, Closure $callback): ResponseInterface
    {
        $search = Validator::queryParams($request)->array('search')['value'] ?? '';
        $start  = Validator::queryParams($request)->integer('start', 0);
        $length = Validator::queryParams($request)->integer('length', 0);
        $order  = Validator::queryParams($request)->array('order');
        $draw   = Validator::queryParams($request)->integer('draw', 0);

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
                    if (!isset($sort_columns[$column['column']])) {
                        continue;
                    }

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
     * @param ServerRequestInterface           $request        Includes the datatables request parameters.
     * @param Builder                          $query          A query to fetch the unfiltered rows and columns.
     * @param array<string>                    $search_columns The names of searchable columns.
     * @param array<string|Expression<string>> $sort_columns   Sort column mapping.
     * @param Closure                          $callback       Converts a row-object to an array-of-columns.
     *
     * @return ResponseInterface
     */
    public function handleQuery(ServerRequestInterface $request, Builder $query, array $search_columns, array $sort_columns, Closure $callback): ResponseInterface
    {
        $search = Validator::queryParams($request)->array('search')['value'] ?? '';
        $start  = Validator::queryParams($request)->integer('start', 0);
        $length = Validator::queryParams($request)->integer('length', 0);
        $order  = Validator::queryParams($request)->array('order');
        $draw   = Validator::queryParams($request)->integer('draw', 0);

        // Count unfiltered records
        $recordsTotal = (clone $query)->count();

        // Filtering
        if ($search !== '') {
            $query->where(static function (Builder $query) use ($search, $search_columns): void {
                $like = '%' . addcslashes($search, '\\%_') . '%';
                $like = strtr($like, [' ' => '%']);

                foreach ($search_columns as $search_column) {
                    $query->orWhere($search_column, 'LIKE', $like);
                }
            });
        }

        // Sorting
        if ($order !== []) {
            foreach ($order as $value) {
                // Columns in datatables are numbered from zero.
                // Columns in MySQL are numbered starting with one.
                // If not specified, the Nth table column maps onto the Nth query column.
                $sort_column = $sort_columns[$value['column']] ?? new Expression(1 + $value['column']);

                $query->orderBy($sort_column, $value['dir']);
            }
        } else {
            $query->orderBy(new Expression(1));
        }

        // Paginating
        if ($length > 0) {
            $recordsFiltered = (clone $query)->count();

            $query->skip($start)->limit($length);
            $data = $query->get();
        } else {
            $data = $query->get();

            $recordsFiltered = $data->count();
        }

        $data = $data->map($callback)->all();

        return response([
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }
}
