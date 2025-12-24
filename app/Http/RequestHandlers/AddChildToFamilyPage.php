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
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\GedcomEditService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function route;

final class AddChildToFamilyPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    public function __construct(
        private readonly GedcomEditService $gedcom_edit_service,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree   = Validator::attributes($request)->tree();
        $xref   = Validator::attributes($request)->isXref()->string('xref');
        $sex    = Validator::attributes($request)->string('sex');
        $family = Registry::familyFactory()->make($xref, $tree);
        $family = Auth::checkFamilyAccess($family, true);

        // Name facts.
        $surname_tradition = Registry::surnameTraditionFactory()
            ->make($tree->getPreference('SURNAME_TRADITION'));

        $names = $surname_tradition->newChildNames($family->husband(), $family->wife(), $sex);

        $facts = [
            'i' => $this->gedcom_edit_service->newIndividualFacts($tree, $sex, $names),
        ];

        $titles = [
            'M' => I18N::translate('Add a son'),
            'F' => I18N::translate('Add a daughter'),
            'U' => I18N::translate('Add a child'),
        ];

        $title = $titles[$sex] ?? $titles['U'];

        return $this->viewResponse('edit/new-individual', [
            'facts'               => $facts,
            'gedcom_edit_service' => $this->gedcom_edit_service,
            'post_url'            => route(AddChildToFamilyAction::class, ['tree' => $tree->name(), 'xref' => $xref]),
            'title'               => $family->fullName() . ' - ' . $title,
            'tree'                => $tree,
            'url'                 => Validator::queryParams($request)->isLocalUrl()->string('url', $family->url()),
        ]);
    }
}
