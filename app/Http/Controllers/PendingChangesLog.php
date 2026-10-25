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
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Enums\ChangeStatus;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function date;

final class PendingChangesLog
{
    use ViewResponseTrait;

    public function __construct(
        private UserInterface $user,
        private TreeService $tree_service,
        private UserService $user_service,
    ) {
    }

    public function get(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        $trees = $this->tree_service->titles();
        $users = ['' => ''];

        foreach ($this->user_service->all() as $user) {
            $user_name         = $user->userName();
            $users[$user_name] = $user_name;
        }

        // First and last change in the database
        $earliest = DB::table('change')->min('change_time') ?? date('Y-m-d H:i:s');
        $latest   = DB::table('change')->max('change_time') ?? date('Y-m-d H:i:s');

        $earliest = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $earliest, new DateTimeZone('UTC'))
            ->setTimezone(new DateTimeZone($this->user->getPreference(UserInterface::PREF_TIME_ZONE, 'UTC')))
            ->format('Y-m-d');

        $latest = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $latest, new DateTimeZone('UTC'))
            ->setTimezone(new DateTimeZone($this->user->getPreference(UserInterface::PREF_TIME_ZONE, 'UTC')))
            ->format('Y-m-d');

        $from     = Validator::queryParams($request)->string('from', $earliest);
        $to       = Validator::queryParams($request)->string('to', $latest);
        $type     = Validator::queryParams($request)->string('type', '');
        $oldged   = Validator::queryParams($request)->string('oldged', '');
        $newged   = Validator::queryParams($request)->string('newged', '');
        $xref     = Validator::queryParams($request)->string('xref', '');
        $username = Validator::queryParams($request)->string('username', '');

        return $this->viewResponse('admin/changes-log', [
            'earliest' => $earliest,
            'from'     => $from,
            'latest'   => $latest,
            'newged'   => $newged,
            'oldged'   => $oldged,
            'statuses' => $this->changeStatuses(),
            'title'    => I18N::translate('Changes log'),
            'to'       => $to,
            'tree'     => $tree,
            'trees'    => $trees,
            'type'     => $type,
            'username' => $username,
            'users'    => $users,
            'xref'     => $xref,
        ]);
    }

    public function post(ServerRequestInterface $request): ResponseInterface
    {
        return redirect(route(self::class, [
            'tree'     => Validator::parsedBody($request)->string('tree'),
            'from'     => Validator::parsedBody($request)->string('from'),
            'to'       => Validator::parsedBody($request)->string('to'),
            'type'     => Validator::parsedBody($request)->string('type'),
            'oldged'   => Validator::parsedBody($request)->string('oldged'),
            'newged'   => Validator::parsedBody($request)->string('newged'),
            'xref'     => Validator::parsedBody($request)->string('xref'),
            'username' => Validator::parsedBody($request)->string('username'),
        ]));
    }

    /**
     * Labels for the various statuses.
     *
     * @return array<string,string>
     */
    private function changeStatuses(): array
    {
        return [
            ''                            => '',
            ChangeStatus::Accepted->value => ChangeStatus::Accepted->label(),
            ChangeStatus::Rejected->value => ChangeStatus::Rejected->label(),
            ChangeStatus::Pending->value  => ChangeStatus::Pending->label(),
        ];
    }
}
