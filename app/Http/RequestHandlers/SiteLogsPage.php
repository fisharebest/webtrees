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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\Carbon;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function max;
use function min;

/**
 * Show logs.
 */
class SiteLogsPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    /** @var TreeService */
    private $tree_service;

    /** @var UserService */
    private $user_service;

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

        $earliest = DB::table('log')->min('log_time');
        $latest   = DB::table('log')->max('log_time');

        $earliest = Carbon::make($earliest) ?? Carbon::now();
        $latest   = Carbon::make($latest) ?? Carbon::now();

        $earliest = $earliest->toDateString();
        $latest   = $latest->toDateString();

        $params   = $request->getQueryParams();
        $action   = $params['action'] ?? '';
        $from     = $params['from'] ?? $earliest;
        $to       = $params['to'] ?? $latest;
        $type     = $params['type'] ?? '';
        $text     = $params['text'] ?? '';
        $ip       = $params['ip'] ?? '';
        $username = $params['username'] ?? '';
        $tree     = $params['tree'] ?? '';

        $from = max($from, $earliest);
        $to   = min(max($from, $to), $latest);

        $user_options = $this->user_service->all()->mapWithKeys(static function (User $user): array {
            return [$user->userName() => $user->userName()];
        });
        $user_options = (new Collection(['' => '']))->merge($user_options);

        $tree_options = $this->tree_service->all()->mapWithKeys(static function (Tree $tree): array {
            return [$tree->name() => $tree->title()];
        });
        $tree_options = (new Collection(['' => '']))->merge($tree_options);

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
}
