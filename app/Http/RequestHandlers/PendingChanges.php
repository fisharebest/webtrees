<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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

use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\PendingChangesService;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;
use function route;

/**
 * Show all pending changes.
 */
class PendingChanges implements RequestHandlerInterface
{
    use ViewResponseTrait;

    // Some servers may not have enough resources to show all the changes.
    private const MAX_CHANGES = 1000;

    /** @var PendingChangesService */
    private $pending_changes_service;

    /**
     * PendingChanges constructor.
     *
     * @param PendingChangesService $pending_changes_service
     */
    public function __construct(PendingChangesService $pending_changes_service)
    {
        $this->pending_changes_service = $pending_changes_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $n = (int) ($request->getQueryParams()['n'] ?? self::MAX_CHANGES);

        $url     = $request->getQueryParams()['url'] ?? route(TreePage::class, ['tree' => $tree->name()]);
        $xrefs   = $this->pending_changes_service->pendingXrefs($tree);
        $changes = $this->pending_changes_service->pendingChanges($tree, $n);
        $title   = I18N::translate('Pending changes');

        return $this->viewResponse('pending-changes-page', [
            'changes' => $changes,
            'count'   => $xrefs->count(),
            'title'   => $title,
            'tree'    => $tree,
            'url'     => $url,
        ]);
    }
}
