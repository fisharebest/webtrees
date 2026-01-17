<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Validator;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function redirect;

final class HeaderPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree   = Validator::attributes($request)->tree();
        $xref   = Validator::attributes($request)->isXref()->string('xref');
        $slug   = Validator::attributes($request)->string('slug', '');
        $header = Registry::headerFactory()->make($xref, $tree);
        $header = Auth::checkHeaderAccess($header, false);

        // Redirect to correct xref/slug
        if ($header->xref() !== $xref || Registry::slugFactory()->make($header) !== $slug) {
            return redirect($header->url(), StatusCodeInterface::STATUS_MOVED_PERMANENTLY);
        }

        return $this->viewResponse('record-page', [
            'clipboard_facts'      => new Collection(),
            'linked_families'      => null,
            'linked_individuals'   => null,
            'linked_locations'     => null,
            'linked_media_objects' => null,
            'linked_notes'         => null,
            'linked_repositories'  => null,
            'linked_sources'       => null,
            'linked_submitters'    => null,
            'meta_description'     => '',
            'meta_robots'          => 'index,follow',
            'record'               => $header,
            'title'                => $header->fullName(),
            'tree'                 => $tree,
        ])->withHeader('Link', '<' . $header->url() . '>; rel="canonical"');
    }
}
