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

namespace Fisharebest\Webtrees\DB;

use Fisharebest\Webtrees\DB;
use Illuminate\Database\Query\Expression;

/**
 * SQL GROUP_CONCAT function.
 *
 * @extends Expression<string>
 */
class GroupConcat extends Expression
{
    public function __construct(string $expression, string $separator = ',', string $order_by = '', string $alias = '')
    {
        $quoted_expression = DB::connection()->getSchemaGrammar()->wrap($expression);
        $quoted_separator  = DB::connection()->getPdo()->quote($separator);
        $quoted_order_by   = DB::connection()->getSchemaGrammar()->wrap($order_by);

        $sql = match (DB::connection()->getDriverName()) {
            DB::SQL_SERVER => 'STRING_AGG(' . $quoted_expression . ',' . $quoted_separator . ') WITHIN GROUP (ORDER BY ' . $quoted_order_by . ' ASC)',
            default        => 'GROUP_CONCAT(' . $quoted_expression . ',' . $quoted_separator . ' ORDER BY ' . $quoted_order_by . ')',
        };

        if ($alias !== '') {
            $sql .= ' AS ' . DB::connection()->getSchemaGrammar()->wrap($alias);
        }

        parent::__construct($sql);
    }
}
