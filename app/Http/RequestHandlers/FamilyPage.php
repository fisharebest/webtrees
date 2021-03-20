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

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ClipboardService;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use stdClass;

use function assert;
use function explode;
use function in_array;
use function is_string;
use function redirect;

/**
 * Show a family's page.
 */
class FamilyPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    /** @var ClipboardService */
    private $clipboard_service;

    /**
     * FamilyPage constructor.
     *
     * @param ClipboardService $clipboard_service
     */
    public function __construct(ClipboardService $clipboard_service)
    {
        $this->clipboard_service = $clipboard_service;
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

        $xref = $request->getAttribute('xref');
        assert(is_string($xref));

        $family = Registry::familyFactory()->make($xref, $tree);
        $family = Auth::checkFamilyAccess($family, false);

        // Redirect to correct xref/slug
        if ($family->xref() !== $xref || $request->getAttribute('slug') !== $family->slug()) {
            return redirect($family->url(), StatusCodeInterface::STATUS_MOVED_PERMANENTLY);
        }

        $clipboard_facts = $this->clipboard_service->pastableFacts($family);

        $facts = $family->facts([], true)
            ->filter(static function (Fact $fact): bool {
                return !in_array($fact->tag(), ['FAM:HUSB', 'FAM:WIFE', 'FAM:CHIL'], true);
            });

        return $this->viewResponse('family-page', [
            'clipboard_facts'  => $clipboard_facts,
            'facts'            => $facts,
            'meta_description' => '',
            'meta_robots'      => 'index,follow',
            'record'           => $family,
            'significant'      => $this->significant($family),
            'title'            => $family->fullName(),
            'tree'             => $tree,
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

        $individual = $family->spouses()->merge($family->children())->first();

        if ($individual instanceof Individual) {
            $significant->individual = $individual;
            [$significant->surname] = explode(',', $individual->sortName());
        }

        return $significant;
    }
}
