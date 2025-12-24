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
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_merge;
use function array_search;
use function implode;
use function redirect;
use function uksort;

final class ReorderFamiliesAction implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree  = Validator::attributes($request)->tree();
        $xref  = Validator::attributes($request)->isXref()->string('xref');
        $order = Validator::parsedBody($request)->array('order');

        $individual = Registry::individualFactory()->make($xref, $tree);
        $individual = Auth::checkIndividualAccess($individual, true);

        $fake_facts = ['0 @' . $individual->xref() . '@ INDI'];
        $sort_facts = [];
        $keep_facts = [];

        // Split facts into FAMS and other
        foreach ($individual->facts() as $fact) {
            $tag = $fact->tag();

            if ($tag === 'INDI:FAMC' || $tag === 'INDI:FAMS') {
                $sort_facts[$fact->id()] = $fact->gedcom();
            } else {
                $keep_facts[] = $fact->gedcom();
            }
        }

        // Sort the facts
        $callback = static fn (string $x, string $y): int => array_search($x, $order, true) <=> array_search($y, $order, true);
        uksort($sort_facts, $callback);

        // Merge the facts
        $gedcom = implode("\n", array_merge($fake_facts, $sort_facts, $keep_facts));

        $individual->updateRecord($gedcom, false);

        return redirect($individual->url());
    }
}
