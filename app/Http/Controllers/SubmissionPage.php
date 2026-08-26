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

use Fisharebest\Webtrees\Enums\HttpStatusCode;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function redirect;

final class SubmissionPage
{
    use ViewResponseTrait;

    public function get(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $xref   = Validator::attributes($request)->isXref()->string('xref');
        $slug   = Validator::attributes($request)->string('slug', '');
        $record = Registry::submissionFactory()->make($xref, $tree);
        $record = Auth::checkSubmissionAccess($record, false);

        // Redirect to correct xref/slug
        if ($record->xref() !== $xref || Registry::slugFactory()->make($record) !== $slug) {
            return redirect($record->url(), HttpStatusCode::MovedPermanently);
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
            'record'               => $record,
            'title'                => $record->fullName(),
            'tree'                 => $tree,
        ])->withHeader('Link', '<' . $record->url() . '>; rel="canonical"');
    }
}
