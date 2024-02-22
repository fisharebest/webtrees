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

use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Validator;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use Psr\Http\Message\ServerRequestInterface;

use function addcslashes;

/**
 * Manage site logs
 */
class SiteLogsService
{
    /**
     * Generate a query for filtering the changes log.
     *
     * @param ServerRequestInterface $request
     *
     * @return Builder
     */
    public function logsQuery(ServerRequestInterface $request): Builder
    {
        $tree     = Validator::queryParams($request)->string('tree');
        $from     = Validator::queryParams($request)->string('from');
        $to       = Validator::queryParams($request)->string('to');
        $type     = Validator::queryParams($request)->string('type');
        $text     = Validator::queryParams($request)->string('text');
        $ip       = Validator::queryParams($request)->string('ip');
        $username = Validator::queryParams($request)->string('username');

        $query = DB::table('log')
            ->leftJoin('user', 'user.user_id', '=', 'log.user_id')
            ->leftJoin('gedcom', 'gedcom.gedcom_id', '=', 'log.gedcom_id')
            ->select(['log.*', new Expression("COALESCE(user_name, '<none>') AS user_name"), new Expression("COALESCE(gedcom_name, '<none>') AS gedcom_name")]);

        $tz  = new DateTimeZone(Auth::user()->getPreference(UserInterface::PREF_TIME_ZONE, 'UTC'));
        $utc = new DateTimeZone('UTC');

        if ($from !== '') {
            $from_time = DateTimeImmutable::createFromFormat('!Y-m-d', $from, $tz)
                ->setTimezone($utc)
                ->format('Y-m-d H:i:s');

            $query->where('log_time', '>=', $from_time);
        }

        if ($to !== '') {
            $to_time = DateTimeImmutable::createFromFormat('!Y-m-d', $to, $tz)
                ->add(new DateInterval('P1D'))
                ->setTimezone($utc)
                ->format('Y-m-d H:i:s');

            $query->where('log_time', '<', $to_time);
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
