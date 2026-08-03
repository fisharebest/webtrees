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

namespace Fisharebest\Webtrees\Http\Controllers;

use DateTimeImmutable;
use DateTimeZone;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function date;
use function max;
use function min;
use function redirect;
use function route;

final class SiteLogs
{
    use ViewResponseTrait;

    public function __construct(
        private readonly TreeService $tree_service,
        private readonly UserService $user_service,
    ) {
    }

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        // First and last change in the database
        $earliest = DB::table('log')->min('log_time') ?? date('Y-m-d H:i:s');
        $latest   = DB::table('log')->max('log_time') ?? date('Y-m-d H:i:s');

        $earliest = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $earliest, new DateTimeZone('UTC'))
            ->setTimezone(new DateTimeZone(Auth::user()->getPreference(UserInterface::PREF_TIME_ZONE, 'UTC')))
            ->format('Y-m-d');

        $latest = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $latest, new DateTimeZone('UTC'))
            ->setTimezone(new DateTimeZone(Auth::user()->getPreference(UserInterface::PREF_TIME_ZONE, 'UTC')))
            ->format('Y-m-d');

        $action   = Validator::queryParams($request)->string('action', '');
        $from     = Validator::queryParams($request)->string('from', $earliest);
        $to       = Validator::queryParams($request)->string('to', $latest);
        $type     = Validator::queryParams($request)->string('type', '');
        $text     = Validator::queryParams($request)->string('text', '');
        $ip       = Validator::queryParams($request)->string('ip', '');
        $username = Validator::queryParams($request)->string('username', '');
        $tree     = Validator::queryParams($request)->string('tree', '');

        $from = max($from, $earliest);
        $to   = min(max($from, $to), $latest);

        $user_options = $this->user_service->all()->mapWithKeys(static fn (User $user): array => [$user->userName() => $user->userName()]);
        $user_options->prepend('<--->', '<--->');
        $user_options->prepend('', '');

        $tree_options = $this->tree_service->all()->mapWithKeys(static fn (Tree $tree): array => [$tree->name() => $tree->title()]);
        $tree_options->prepend('<--->', '<--->');
        $tree_options->prepend('', '');

        $title = I18N::translate('Website logs');

        return $this->viewResponse('admin/site-logs', [
            'action'       => $action,
            'earliest'     => $earliest,
            'from'         => $from,
            'tree'         => $tree,
            'ip'           => $ip,
            'latest'       => $latest,
            'tree_options' => $tree_options,
            'title'        => $title,
            'to'           => $to,
            'text'         => $text,
            'type'         => $type,
            'username'     => $username,
            'user_options' => $user_options,
        ]);
    }

    public function post(ServerRequestInterface $request): ResponseInterface
    {
        return redirect(route(self::class, [
            'tree'     => Validator::parsedBody($request)->string('tree'),
            'from'     => Validator::parsedBody($request)->string('from'),
            'to'       => Validator::parsedBody($request)->string('to'),
            'type'     => Validator::parsedBody($request)->string('type'),
            'text'     => Validator::parsedBody($request)->string('text'),
            'ip'       => Validator::parsedBody($request)->string('ip'),
            'username' => Validator::parsedBody($request)->string('username'),
        ]));
    }
}
