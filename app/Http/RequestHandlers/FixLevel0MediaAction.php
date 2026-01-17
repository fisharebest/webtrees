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

use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function response;

final class FixLevel0MediaAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly TreeService $tree_service,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $fact_id   = Validator::parsedBody($request)->string('fact_id');
        $indi_xref = Validator::parsedBody($request)->isXref()->string('indi_xref');
        $obje_xref = Validator::parsedBody($request)->isXref()->string('obje_xref');
        $tree_id   = Validator::parsedBody($request)->integer('tree_id');

        $tree       = $this->tree_service->find($tree_id);
        $individual = Registry::individualFactory()->make($indi_xref, $tree);
        $media      = Registry::mediaFactory()->make($obje_xref, $tree);

        if ($individual instanceof Individual && $media instanceof Media) {
            foreach ($individual->facts() as $fact1) {
                if ($fact1->id() === $fact_id) {
                    $individual->updateFact($fact_id, $fact1->gedcom() . "\n2 OBJE @" . $obje_xref . '@', false);

                    foreach ($individual->facts(['OBJE']) as $fact2) {
                        if ($fact2->target() === $media) {
                            $individual->deleteFact($fact2->id(), false);
                        }
                    }
                    break;
                }
            }
        }

        return response();
    }
}
