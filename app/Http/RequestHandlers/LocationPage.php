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
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\Location;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ClipboardService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_search;
use function assert;
use function is_string;
use function redirect;

use const PHP_INT_MAX;

/**
 * Show a location's page.
 */
class LocationPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    // Show the repository's facts in this order:
    private const FACT_ORDER = [
        1 => '_LOC:NAME',
        '_LOC:TYPE',
        '_LOC:_POST',
        '_LOC:_GOV',
        '_LOC:MAP',
        '_LOC:_MAIDENHEAD',
        '_LOC:RELI',
        '_LOC:EVEN',
        '_LOC:_LOC',
        '_LOC:_DMGD',
        '_LOC:_AIDN',
    ];

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

        $location = Registry::locationFactory()->make($xref, $tree);
        $location = Auth::checkLocationAccess($location, false);

        // Redirect to correct xref/slug
        if ($location->xref() !== $xref || $request->getAttribute('slug') !== $location->slug()) {
            return redirect($location->url(), StatusCodeInterface::STATUS_MOVED_PERMANENTLY);
        }

        return $this->viewResponse('gedcom-record-page', [
            'facts'         => $this->facts($location),
            'families'      => $location->linkedFamilies('_LOC'),
            'individuals'   => $location->linkedIndividuals('_LOC'),
            'notes'         => new Collection(),
            'media_objects' => new Collection(),
            'record'        => $location,
            'sources'       => new Collection(),
            'title'         => $location->fullName(),
            'tree'          => $tree,
        ]);
    }

    /**
     * @param Location $location
     *
     * @return Collection<Fact>
     */
    private function facts(Location $location): Collection
    {
        return $location->facts()
            ->sort(static function (Fact $x, Fact $y): int {
                $sort_x = array_search($x->tag(), self::FACT_ORDER, true) ?: PHP_INT_MAX;
                $sort_y = array_search($y->tag(), self::FACT_ORDER, true) ?: PHP_INT_MAX;

                return $sort_x <=> $sort_y;
            });
    }
}
