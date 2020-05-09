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

use Fisharebest\Webtrees\Carbon;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;

use function addcslashes;

/**
 * Manage site logs
 */
class SiteLogsService
{
    /**
     * Generate a query for filtering the changes log.
     *
     * @param string[] $params
     *
     * @return Builder
     */
    public function logsQuery(array $params): Builder
    {
        $tree     = $params['tree'];
        $from     = $params['from'];
        $to       = $params['to'];
        $type     = $params['type'];
        $text     = $params['text'];
        $ip       = $params['ip'];
        $username = $params['username'];

        $query = DB::table('log')
            ->leftJoin('user', 'user.user_id', '=', 'log.user_id')
            ->leftJoin('gedcom', 'gedcom.gedcom_id', '=', 'log.gedcom_id')
            ->select(['log.*', new Expression("COALESCE(user_name, '<none>') AS user_name"), new Expression("COALESCE(gedcom_name, '<none>') AS gedcom_name")]);

        if ($from !== '') {
            $query->where('log_time', '>=', $from);
        }

        if ($to !== '') {
            // before end of the day
            $query->where('log_time', '<', Carbon::make($to)->addDay());
        }

        if ($type !== '') {
            $query->where('log_type', '=', $type);
        }

        if ($text !== '') {
            $query->where('log_message', 'LIKE', '%' . addcslashes($text, '\\%_') . '%');
        }

        if ($ip !== '') {
            $query->where('ip_address', 'LIKE', addcslashes($ip, '\\%_') . '%');
        }

        if ($username !== '') {
            $query->where('user_name', '=', $username);
        }

        if ($tree !== '') {
            $query->where('gedcom_name', '=', $tree);
        }

        return $query;
    }
}
