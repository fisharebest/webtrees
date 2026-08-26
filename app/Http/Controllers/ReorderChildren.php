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
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function array_merge;
use function array_search;
use function implode;
use function redirect;
use function uksort;

final class ReorderChildren
{
    use ViewResponseTrait;

    public function get(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $xref   = Validator::attributes($request)->isXref()->string('xref');
        $family = Registry::familyFactory()->make($xref, $tree);
        $family = Auth::checkFamilyAccess($family, true);
        $url    = Validator::queryParams($request)->isLocalUrl()->string('url', $family->url());

        $title = $family->fullName() . ' — ' . I18N::translate('Re-order children');

        return $this->viewResponse('edit/reorder-children', [
            'family' => $family,
            'title'  => $title,
            'tree'   => $tree,
            'url'    => $url,
        ]);
    }

    public function post(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $xref = Validator::attributes($request)->isXref()->string('xref');

        $family = Registry::familyFactory()->make($xref, $tree);
        $family = Auth::checkFamilyAccess($family, true);

        $order = Validator::parsedBody($request)->list('order');
        $url   = Validator::parsedBody($request)->isLocalUrl()->string('url', $family->url());

        $fake_facts = ['0 @' . $family->xref() . '@ FAM'];
        $sort_facts = [];
        $keep_facts = [];

        // Split facts into FAMS and other
        foreach ($family->facts() as $fact) {
            if ($fact->tag() === 'FAM:CHIL') {
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

        $family->updateRecord($gedcom, false);

        return redirect($url);
    }
}
