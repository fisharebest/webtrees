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
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;
use function is_string;
use function route;

/**
 * Add a new child to a family.
 */
class AddChildToFamilyPage implements RequestHandlerInterface
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

        $sex = $request->getAttribute('sex');
        assert(is_string($sex));

        $family = Registry::familyFactory()->make($xref, $tree);
        $family = Auth::checkFamilyAccess($family, true);

        // Create a dummy individual, so that we can create new/empty facts.
        $element = Registry::elementFactory()->make('INDI:NAME');
        $dummy   = Registry::individualFactory()->new('', '0 @@ INDI', null, $tree);
        $facts   = [
            'i' => [
                new Fact('1 SEX ' . $sex, $dummy, ''),
                new Fact('1 NAME ' . $element->default($tree), $dummy, ''),
                new Fact('1 BIRT', $dummy, ''),
                new Fact('1 DEAT', $dummy, ''),
            ],
        ];

        $titles = [
            'M' => I18N::translate('Add a son'),
            'F' => I18N::translate('Add a daughter'),
            'U' => I18N::translate('Add a child'),
        ];

        $title = $titles[$sex] ?? $titles['U'];

        return $this->viewResponse('edit/new-individual', [
            'cancel_url' => $family->url(),
            'facts'      => $facts,
            'post_url'   => route(AddChildToFamilyAction::class, ['tree' => $tree->name(), 'xref' => $xref]),
            'title'      => $family->fullName() . ' - ' . $title,
            'tree'       => $tree,
            'url'        => $request->getQueryParams()['url'] ?? $family->url(),
        ]);
    }
}
