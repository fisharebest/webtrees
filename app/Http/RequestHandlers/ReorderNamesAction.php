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
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_merge;
use function array_search;
use function assert;
use function implode;
use function is_array;
use function is_string;
use function redirect;
use function uksort;

/**
 * Reorder the names of an individual.
 */
class ReorderNamesAction implements RequestHandlerInterface
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

        $individual = Registry::individualFactory()->make($xref, $tree);
        $individual = Auth::checkIndividualAccess($individual, true);

        $params = (array) $request->getParsedBody();
        $order  = $params['order'];
        assert(is_array($order));

        $fake_facts = ['0 @' . $individual->xref() . '@ INDI'];
        $sort_facts = [];
        $keep_facts = [];

        // Split facts into NAME and other
        foreach ($individual->facts() as $fact) {
            if ($fact->tag() === 'INDI:NAME') {
                $sort_facts[$fact->id()] = $fact->gedcom();
            } else {
                $keep_facts[] = $fact->gedcom();
            }
        }

        // Sort the facts
        uksort($sort_facts, static function (string $x, string $y) use ($order): int {
            return array_search($x, $order, true) <=> array_search($y, $order, true);
        });

        // Merge the facts
        $gedcom = implode("\n", array_merge($fake_facts, $sort_facts, $keep_facts));

        $individual->updateRecord($gedcom, false);

        return redirect($individual->url());
    }
}
