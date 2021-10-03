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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;
use function is_string;
use function preg_replace;
use function redirect;
use function trim;

/**
 * Edit the raw GEDCOM of a fact.
 */
class EditRawFactAction implements RequestHandlerInterface
{
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

        $record = Registry::gedcomRecordFactory()->make($xref, $tree);
        $record = Auth::checkRecordAccess($record, true);

        $fact_id = $request->getAttribute('fact_id');
        assert(is_string($fact_id));

        $params = (array) $request->getParsedBody();

        $gedcom = $params['gedcom'];

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

        $base_url = $request->getAttribute('base_url');
        $url      = Validator::parsedBody($request)->isLocalUrl($base_url)->string('url') ?? $record->url();

        return redirect($url);
    }
}
