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

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;
use function is_string;
use function redirect;

/**
 * Show a submitter's page.
 */
class SubmitterPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $xref = $request->getAttribute('xref');
        assert(is_string($xref));

        $record = Registry::submitterFactory()->make($xref, $tree);
        $record = Auth::checkSubmitterAccess($record, false);

        // Redirect to correct xref/slug
        $slug = Registry::slugFactory()->make($record);

        if ($record->xref() !== $xref || $request->getAttribute('slug') !== $slug) {
            return redirect($record->url(), StatusCodeInterface::STATUS_MOVED_PERMANENTLY);
        }

        return $this->viewResponse('record-page', [
            'clipboard_facts'      => new Collection(),
            'linked_families'      => $record->linkedFamilies('SUBM'),
            'linked_individuals'   => $record->linkedIndividuals('SUBM'),
            'linked_media_objects' => null,
            'linked_notes'         => null,
            'linked_sources'       => null,
            'meta_description'     => '',
            'meta_robots'          => 'index,follow',
            'record'               => $record,
            'title'                => $record->fullName(),
            'tree'                 => $tree,
        ]);
    }
}
