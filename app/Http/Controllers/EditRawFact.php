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
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function preg_replace;
use function redirect;
use function trim;

final class EditRawFact
{
    use ViewResponseTrait;

    public function get(ServerRequestInterface $request): ResponseInterface
    {

        $tree    = Validator::attributes($request)->tree();
        $xref    = Validator::attributes($request)->isXref()->string('xref');
        $record  = Registry::gedcomRecordFactory()->make($xref, $tree);
        $record  = Auth::checkRecordAccess($record, true);
        $fact_id = Validator::attributes($request)->string('fact_id');
        $title   = I18N::translate('Edit the raw GEDCOM') . ' - ' . $record->fullName();

        $fact = $record->facts([], false, null, true)
            ->first(static fn (Fact $fact): bool => $fact->id() === $fact_id);

        if ($fact instanceof Fact) {
            return $this->viewResponse('edit/raw-gedcom-fact', [
                'fact'  => $fact,
                'title' => $title,
                'tree'  => $tree,
                'url'   => Validator::queryParams($request)->isLocalUrl()->string('url', $fact->record()->url()),
            ]);
        }

        return redirect($record->url());
    }

    public function post(ServerRequestInterface $request): ResponseInterface
    {

        $tree    = Validator::attributes($request)->tree();
        $xref    = Validator::attributes($request)->isXref()->string('xref');
        $record  = Registry::gedcomRecordFactory()->make($xref, $tree);
        $record  = Auth::checkRecordAccess($record, true);
        $fact_id = Validator::attributes($request)->string('fact_id');
        $gedcom  = Validator::parsedBody($request)->string('gedcom');

        // Cleanup the client’s bad editing?
        $gedcom = preg_replace('/[\r\n]+/', "\n", $gedcom); // Empty lines
        $gedcom = trim($gedcom); // Leading/trailing spaces
        $record = Auth::checkRecordAccess($record, true);

        foreach ($record->facts([], false, null, true) as $fact) {
            if ($fact->id() === $fact_id && $fact->canEdit()) {
                $record->updateFact($fact_id, $gedcom, false);
                break;
            }
        }

        $url = Validator::parsedBody($request)->isLocalUrl()->string('url', $record->url());

        return redirect($url);
    }
}
