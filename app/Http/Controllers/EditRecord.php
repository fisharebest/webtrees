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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Header;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\GedcomEditService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function array_key_exists;
use function redirect;
use function route;

final class EditRecord
{
    use ViewResponseTrait;

    public function __construct(
        private GedcomEditService $gedcom_edit_service
    ) {
    }

    public function get(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $xref           = Validator::attributes($request)->isXref()->string('xref');
        $record         = Registry::gedcomRecordFactory()->make($xref, $tree);
        $record         = Auth::checkRecordAccess($record, true);
        $include_hidden = Validator::queryParams($request)->boolean('include_hidden', false);
        $can_edit_raw   = Auth::isAdmin() || $tree->getPreference('SHOW_GEDCOM_RECORD') === '1';
        $subtags        = Registry::elementFactory()->make($record->tag())->subtags();

        $gedcom = $this->gedcom_edit_service->insertMissingRecordSubtags($record, $include_hidden);
        $hidden = $this->gedcom_edit_service->insertMissingRecordSubtags($record, true);

        if ($gedcom === $hidden) {
            $hidden_url = '';
        } else {
            $hidden_url = route(EditRecord::class, [
                'include_hidden'  => true,
                'tree'    => $tree->name(),
                'xref'    => $xref,
            ]);
        }

        return $this->viewResponse('edit/edit-record', [
            'can_edit_raw' => $can_edit_raw,
            'gedcom'       => $gedcom,
            'has_chan'     => array_key_exists('CHAN', $subtags),
            'hidden_url'   => $hidden_url,
            'record'       => $record,
            'title'        => $record->fullName(),
            'tree'         => $tree,
        ]);
    }

    public function post(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $xref      = Validator::attributes($request)->isXref()->string('xref');
        $record    = Registry::gedcomRecordFactory()->make($xref, $tree);
        $record    = Auth::checkRecordAccess($record, true);
        $keep_chan = Validator::parsedBody($request)->boolean('keep_chan', false);
        $levels    = Validator::parsedBody($request)->list('levels');
        $tags      = Validator::parsedBody($request)->list('tags');
        $values    = Validator::parsedBody($request)->list('values');

        if ($record->tag() === Header::RECORD_TYPE) {
            $gedcom = '0 ' . $record->tag();
        } else {
            $gedcom = '0 @' . $record->xref() . '@ ' . $record->tag();
        }

        $gedcom .= $this->gedcom_edit_service->editLinesToGedcom($record::RECORD_TYPE, $levels, $tags, $values);

        $record->updateRecord($gedcom, !$keep_chan);

        return redirect($record->url());
    }
}
