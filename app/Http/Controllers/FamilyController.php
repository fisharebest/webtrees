<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Services\ClipboardService;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;

use function redirect;

/**
 * Controller for the family page.
 */
class FamilyController extends AbstractBaseController
{
    /** @var ClipboardService */
    private $clipboard_service;

    /**
     * FamilyController constructor.
     *
     * @param ClipboardService $clipboard_service
     */
    public function __construct(ClipboardService $clipboard_service)
    {
        $this->clipboard_service = $clipboard_service;
    }

    /**
     * Show a family's page.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function show(ServerRequestInterface $request): ResponseInterface
    {
        $slug   = $request->getAttribute('slug');
        $tree   = $request->getAttribute('tree');
        $xref   = $request->getAttribute('xref');
        $family = Family::getInstance($xref, $tree);

        Auth::checkFamilyAccess($family, false);

        if ($slug !== $family->slug()) {
            return redirect($family->url());
        }

        $clipboard_facts = $this->clipboard_service->pastableFacts($family, new Collection());

        return $this->viewResponse('family-page', [
            'facts'           => $family->facts([], true),
            'meta_robots'     => 'index,follow',
            'clipboard_facts' => $clipboard_facts,
            'record'          => $family,
            'significant'     => $this->significant($family),
            'title'           => $family->fullName(),
        ]);
    }

    /**
     * What are the significant elements of this page?
     * The layout will need them to generate URLs for charts and reports.
     *
     * @param Family $family
     *
     * @return stdClass
     */
    private function significant(Family $family): stdClass
    {
        $significant = (object) [
            'family'     => $family,
            'individual' => null,
            'surname'    => '',
        ];

        foreach ($family->spouses()->merge($family->children()) as $individual) {
            $significant->individual = $individual;
            [$significant->surname] = explode(',', $individual->sortName());
            break;
        }

        return $significant;
    }
}
