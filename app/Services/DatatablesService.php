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
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Paginate and search queries for datatables.
 */
class DatatablesService
{
    /**
     * Apply filtering and pagination to a query, and generate a response suitable for datatables.
     *
     * @link http://www.datatables.net/usage/server-side
     *
     * @param ServerRequestInterface $request        Includes the datatables request parameters.
     * @param Builder                $query          A query to fetch the unfiltered rows and columns.
     * @param string[]               $search_columns The names of searchable columns.
     * @param string[]               $sort_columns   How to sort columns.
     * @param Closure                $callback       Converts a row-object to an array-of-columns.
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request, Builder $query, array $search_columns, array $sort_columns, Closure $callback): ResponseInterface
    {
        $search = $request->getQueryParams()['search']['value'] ?? '';
        $start  = (int) ($request->getQueryParams()['start'] ?? 0);
        $length = (int) ($request->getQueryParams()['length'] ?? 0);
        $order  = $request->getQueryParams()['order'] ?? [];
        $draw   = (int) ($request->getQueryParams()['draw'] ?? 0);

        // Count unfiltered records
        $recordsTotal = (clone $query)->count();

        // Filtering
        if ($search !== '') {
            $query->where(static function (Builder $query) use ($search, $search_columns): void {
                foreach ($search_columns as $search_column) {
                    $query->whereContains($search_column, $search, 'or');
                }
            });
        }

        // Sorting
        if (!empty($order)) {
            foreach ($order as $value) {
                // Columns in datatables are numbered from zero.
                // Columns in MySQL are numbered starting with one.
                // If not specified, the Nth table column maps onto the Nth query column.
                $sort_column = $sort_columns[$value['column']] ?? DB::raw(1 + $value['column']);

                $query->orderBy($sort_column, $value['dir']);
            }
        } else {
            $query->orderBy(DB::raw(1));
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
