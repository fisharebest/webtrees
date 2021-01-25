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

use Fisharebest\Webtrees\Services\GedcomEditService;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;
use function preg_match_all;
use function redirect;
use function route;

/**
 * Create a new unlinked individual.
 */
class AddUnlinkedAction implements RequestHandlerInterface
{
    /** @var GedcomEditService */
    private $gedcom_edit_service;

    /**
     * AddChildToFamilyAction constructor.
     *
     * @param GedcomEditService $gedcom_edit_service
     */
    public function __construct(GedcomEditService $gedcom_edit_service)
    {
        $this->gedcom_edit_service = $gedcom_edit_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $params = (array) $request->getParsedBody();

        $this->gedcom_edit_service->glevels = $params['glevels'] ?? [];
        $this->gedcom_edit_service->tag     = $params['tag'] ?? [];
        $this->gedcom_edit_service->text    = $params['text'] ?? [];
        $this->gedcom_edit_service->islink  = $params['islink'] ?? [];

        $this->gedcom_edit_service->splitSource();
        $gedrec = '0 @@ INDI';
        $gedrec .= $this->gedcom_edit_service->addNewName($request, $tree);
        $gedrec .= $this->gedcom_edit_service->addNewSex($request);
        if (preg_match_all('/([A-Z0-9_]+)/', $tree->getPreference('QUICK_REQUIRED_FACTS'), $matches)) {
            foreach ($matches[1] as $match) {
                $gedrec .= $this->gedcom_edit_service->addNewFact($request, $tree, $match);
            }
        }
        if ($params['SOUR_INDI'] ?? false) {
            $gedrec = $this->gedcom_edit_service->handleUpdates($gedrec);
        } else {
            $gedrec = $this->gedcom_edit_service->updateRest($gedrec);
        }

        $new_indi = $tree->createIndividual($gedrec);

        if (($params['goto'] ?? '') === 'new') {
            return redirect($new_indi->url());
        }

        return redirect(route(ManageTrees::class, ['tree' => $tree->name()]));
    }
}
