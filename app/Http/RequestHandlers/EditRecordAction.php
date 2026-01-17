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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Header;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\GedcomEditService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function redirect;

final class EditRecordAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly GedcomEditService $gedcom_edit_service,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree      = Validator::attributes($request)->tree();
        $xref      = Validator::attributes($request)->isXref()->string('xref');
        $record    = Registry::gedcomRecordFactory()->make($xref, $tree);
        $record    = Auth::checkRecordAccess($record, true);
        $keep_chan = Validator::parsedBody($request)->boolean('keep_chan', false);
        $levels    = Validator::parsedBody($request)->array('levels');
        $tags      = Validator::parsedBody($request)->array('tags');
        $values    = Validator::parsedBody($request)->array('values');

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
