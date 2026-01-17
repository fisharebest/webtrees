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
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Header;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function explode;
use function in_array;
use function preg_replace;
use function redirect;
use function trim;

final class EditRawRecordAction implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree     = Validator::attributes($request)->tree();
        $xref     = Validator::attributes($request)->isXref()->string('xref');
        $record   = Registry::gedcomRecordFactory()->make($xref, $tree);
        $record   = Auth::checkRecordAccess($record, true);
        $level0   = Validator::parsedBody($request)->string('level0');
        $facts    = Validator::parsedBody($request)->array('fact');
        $fact_ids = Validator::parsedBody($request)->array('fact_id');

        // Generate the level-0 line for the record.
        switch ($record->tag()) {
            case GedcomRecord::RECORD_TYPE:
                // Unknown type? - copy the existing data.
                $gedcom = explode("\n", $record->gedcom(), 2)[0];
                break;
            case Header::RECORD_TYPE:
                $gedcom = '0 HEAD';
                break;
            default:
                $gedcom = '0 @' . $xref . '@ ' . $record->tag();
        }

        if ($level0 !== '') {
            $gedcom = $level0;
        }

        // Retain any private facts
        foreach ($record->facts([], false, Auth::PRIV_HIDE, true) as $fact) {
            if (!in_array($fact->id(), $fact_ids, true)) {
                $gedcom .= "\n" . $fact->gedcom();
            }
        }
        // Append the updated facts
        foreach ($facts as $fact) {
            $gedcom .= "\n" . trim($fact);
        }

        // Empty lines and MSDOS line endings.
        $gedcom = preg_replace('/[\r\n]+/', "\n", $gedcom);
        $gedcom = trim($gedcom);

        $record->updateRecord($gedcom, false);

        return redirect($record->url());
    }
}
