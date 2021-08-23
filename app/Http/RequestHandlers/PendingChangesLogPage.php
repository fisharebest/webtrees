<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\Carbon;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Show pending changes.
 */
class PendingChangesLogPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    private TreeService $tree_service;

    private UserService $user_service;

    /**
     * @param TreeService $tree_service
     * @param UserService $user_service
     */
    public function __construct(TreeService $tree_service, UserService $user_service)
    {
        $this->tree_service = $tree_service;
        $this->user_service = $user_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $trees = $this->tree_service->titles();

        $users = ['' => ''];
        foreach ($this->user_service->all() as $user) {
            $user_name         = $user->userName();
            $users[$user_name] = $user_name;
        }

        // First and last change in the database.
        $earliest = DB::table('change')->min('change_time');
        $latest   = DB::table('change')->max('change_time');

        $earliest = Carbon::make($earliest) ?? Carbon::now();
        $latest   = Carbon::make($latest) ?? Carbon::now();

        $earliest = $earliest->toDateString();
        $latest   = $latest->toDateString();

        $from     = $request->getQueryParams()['from'] ?? $earliest;
        $to       = $request->getQueryParams()['to'] ?? $latest;
        $type     = $request->getQueryParams()['type'] ?? '';
        $oldged   = $request->getQueryParams()['oldged'] ?? '';
        $newged   = $request->getQueryParams()['newged'] ?? '';
        $xref     = $request->getQueryParams()['xref'] ?? '';
        $username = $request->getQueryParams()['username'] ?? '';

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

    /**
     * Labels for the various statuses.
     *
     * @return array<string,string>
     */
    private function changeStatuses(): array
    {
        return [
            ''         => '',
            /* I18N: the status of an edit accepted/rejected/pending */
            'accepted' => I18N::translate('accepted'),
            /* I18N: the status of an edit accepted/rejected/pending */
            'rejected' => I18N::translate('rejected'),
            /* I18N: the status of an edit accepted/rejected/pending */
            'pending'  => I18N::translate('pending'),
        ];
    }
}
