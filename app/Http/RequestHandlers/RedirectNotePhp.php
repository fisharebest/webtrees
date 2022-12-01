<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Redirect URLs created by webtrees 1.x (and PhpGedView).
 */
class RedirectNotePhp implements RequestHandlerInterface
{
    private TreeService $tree_service;

    /**
     * @param TreeService $tree_service
     */
    public function __construct(TreeService $tree_service)
    {
        $this->tree_service = $tree_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     * @throws HttpNotFoundException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $ged  = Validator::queryParams($request)->string('ged');
        $nid  = Validator::queryParams($request)->isXref()->string('nid');
        $tree = $this->tree_service->all()->get($ged);

        if ($tree instanceof Tree) {
            $note = Registry::noteFactory()->make($nid, $tree);

            if ($note instanceof Note) {
                return Registry::responseFactory()->redirectUrl($note->url(), StatusCodeInterface::STATUS_MOVED_PERMANENTLY);
            }
        }

        $message = I18N::translate('This note does not exist or you do not have permission to view it.');

        throw new HttpNotFoundException($message);
    }
}
