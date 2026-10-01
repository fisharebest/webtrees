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

use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use Fisharebest\Database\Query\JoinOn;
use Fisharebest\Database\Query\QueryBuilder;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ServerRequestInterface;

use function addcslashes;

/**
 * Manage site logs
 */
class SiteLogsService
{
    /**
     * Generate a query for filtering the changes log.
     */
    public function logsQuery(ServerRequestInterface $request): QueryBuilder
    {
        $tree     = Validator::queryParams($request)->string('tree');
        $from     = Validator::queryParams($request)->string('from');
        $to       = Validator::queryParams($request)->string('to');
        $type     = Validator::queryParams($request)->string('type');
        $text     = Validator::queryParams($request)->string('text');
        $ip       = Validator::queryParams($request)->string('ip');
        $username = Validator::queryParams($request)->string('username');

        $query = DB::query()
            ->select(
                'log.*',
                DB::alias(DB::coalesce(DB::column('user_name'), DB::value('<--->')), 'user_name'),
                DB::alias(DB::coalesce(DB::column('gedcom_name'), DB::value('<--->')), 'gedcom_name'),
            )
            ->from('log')
            ->leftJoin('user', static fn (JoinOn $j): JoinOn => $j->on('user.user_id', 'log.user_id'))
            ->leftJoin('gedcom', static fn (JoinOn $j): JoinOn => $j->on('gedcom.gedcom_id', 'log.gedcom_id'));

        $tz  = new DateTimeZone(Auth::user()->getPreference(UserInterface::PREF_TIME_ZONE, 'UTC'));
        $utc = new DateTimeZone('UTC');

        if ($from !== '') {
            $from_time = DateTimeImmutable::createFromFormat('!Y-m-d', $from, $tz)
                ->setTimezone($utc)
                ->format('Y-m-d H:i:s');

            $query = $query->whereGreaterThanOrEqual('log_time', $from_time);
        }

        if ($to !== '') {
            $to_time = DateTimeImmutable::createFromFormat('!Y-m-d', $to, $tz)
                ->add(new DateInterval('P1D'))
                ->setTimezone($utc)
                ->format('Y-m-d H:i:s');

            $query = $query->whereLessThan('log_time', $to_time);
        }

        if ($type !== '') {
            $query = $query->whereEqual('log_type', $type);
        }

        if ($text !== '') {
            $query = $query->whereLike('log_message', '%' . addcslashes($text, '\\%_') . '%');
        }

        if ($ip !== '') {
            $query = $query->whereLike('ip_address', addcslashes($ip, '\\%_') . '%');
        }

        if ($username === '<--->') {
            $query = $query->whereNull('user.user_id');
        } elseif ($username !== '') {
            $query = $query->whereEqual('user_name', $username);
        }

        if ($tree === '<-->') {
            $query = $query->whereNull('gedcom.gedcom_id');
        } elseif ($tree !== '') {
            $query = $query->whereEqual('gedcom_name', $tree);
        }

        return $query;
    }
}
