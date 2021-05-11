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

/**
 * Link an existing individual as a new spouse.
 */
class LinkSpouseToIndividualPage implements RequestHandlerInterface
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

        $individual = Registry::individualFactory()->make($xref, $tree);
        $individual = Auth::checkIndividualAccess($individual, true);

        // Create a dummy family record, so we can create new/empty facts.
        $dummy = Registry::familyFactory()->new('', '0 @@ FAM', null, $tree);
        $facts = [
            'f' => [
                new Fact('1 MARR', $dummy, ''),
            ],
        ];

        if ($individual->sex() === 'F') {
            $title = $individual->fullName() . ' - ' . I18N::translate('Add a husband using an existing individual');
            $label = I18N::translate('Husband');
        } else {
            $title = $individual->fullName() . ' - ' . I18N::translate('Add a wife using an existing individual');
            $label = I18N::translate('Wife');
        }

        return $this->viewResponse('edit/link-spouse-to-individual', [
            'post_url'   => route(LinkSpouseToIndividualAction::class, ['tree' => $tree->name(), 'xref' => $xref]),
            'cancel_url' => $individual->url(),
            'label'      => $label,
            'title'      => $title,
            'facts'      => $facts,
            'tree'       => $tree,
            'xref'       => $xref,
        ]);
    }
}
