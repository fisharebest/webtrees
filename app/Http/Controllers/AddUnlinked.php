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

use Fisharebest\Webtrees\Enums\Sex;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\GedcomEditService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function redirect;
use function route;

final class AddUnlinked
{
    use ViewResponseTrait;

    public function __construct(
        private GedcomEditService $gedcom_edit_service
    ) {
    }

    public function get(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $name = Registry::elementFactory()->make('INDI:NAME')->default($tree);

        $facts = [
            'i' => $this->gedcom_edit_service->newIndividualFacts($tree, Sex::Unknown, ['1 NAME ' . $name]),
        ];

        $url = route(ManageTrees::class, ['tree' => $tree->name()]);

        return $this->viewResponse('edit/new-individual', [
            'facts'               => $facts,
            'gedcom_edit_service' => $this->gedcom_edit_service,
            'post_url'            => route(AddUnlinked::class, ['tree' => $tree->name()]),
            'tree'                => $tree,
            'title'               => I18N::translate('Create an individual'),
            'url'                 => Validator::queryParams($request)->isLocalUrl()->string('url', $url),
        ]);
    }

    public function post(ServerRequestInterface $request, Tree $tree): ResponseInterface
    {
        $levels = Validator::parsedBody($request)->list('ilevels');
        $tags   = Validator::parsedBody($request)->list('itags');
        $values = Validator::parsedBody($request)->list('ivalues');
        $gedcom = $this->gedcom_edit_service->editLinesToGedcom(Individual::RECORD_TYPE, $levels, $tags, $values);

        $individual = $tree->createIndividual('0 @@ INDI' . $gedcom);

        $url = Validator::parsedBody($request)->isLocalUrl()->string('url', $individual->url());

        return redirect($url);
    }
}
