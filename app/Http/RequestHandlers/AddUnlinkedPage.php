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

use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;
use function route;

/**
 * Create a new unlinked individual.
 */
class AddUnlinkedPage implements RequestHandlerInterface
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

        // Create a dummy individual, so that we can create new/empty facts.
        $element = Registry::elementFactory()->make('INDI:NAME');
        $dummy   = Registry::individualFactory()->new('', '0 @@ INDI', null, $tree);
        $facts   = [
            'i' => [
                new Fact('1 SEX', $dummy, ''),
                new Fact('1 NAME ' . $element->default($tree), $dummy, ''),
                new Fact('1 BIRT', $dummy, ''),
                new Fact('1 DEAT', $dummy, ''),
            ],
        ];

        return $this->viewResponse('edit/new-individual', [
            'cancel_url' => route('manage-trees', ['tree' => $tree->name()]),
            'facts'      => $facts,
            'post_url'   => route(AddUnlinkedAction::class, ['tree' => $tree->name()]),
            'tree'       => $tree,
            'title'      => I18N::translate('Create an individual'),
            'url'        => $request->getQueryParams()['url'] ?? route('manage-trees', ['tree' => $tree->name()]),
        ]);
    }
}
