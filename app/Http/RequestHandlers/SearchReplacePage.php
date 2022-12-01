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

use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Search and replace genealogy data
 */
class SearchReplacePage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    /**
     * Search and replace.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree    = Validator::attributes($request)->tree();
        $context = Validator::queryParams($request)->string('context', 'all');
        $replace = Validator::queryParams($request)->string('replace', '');
        $search  = Validator::queryParams($request)->string('search', '');
        $title   = I18N::translate('Search and replace');

        return $this->viewResponse('search-replace-page', [
            'context' => $context,
            'replace' => $replace,
            'search'  => $search,
            'title'   => $title,
            'tree'    => $tree,
        ]);
    }
}
