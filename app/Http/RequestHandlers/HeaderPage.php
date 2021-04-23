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
use Fisharebest\Webtrees\Header;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\Registry;
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
 * Show a header's page.
 */
class HeaderPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    // Show the header's facts in this order:
    private const FACT_ORDER = [
        1 => 'HEAD:SOUR',
        'HEAD:DEST',
        'HEAD:DATE',
        'HEAD:SUBM',
        'HEAD:SUBN',
        'HEAD:FILE',
        'HEAD:COPR',
        'HEAD:GEDC',
        'HEAD:CHAR',
        'HEAD:LANG',
        'HEAD:PLAC',
        'HEAD:NOTE',
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

        $header = Registry::headerFactory()->make($xref, $tree);
        $header = Auth::checkHeaderAccess($header, false);

        // Redirect to correct xref/slug
        $slug = Registry::slugFactory()->make($header);

        if ($header->xref() !== $xref || $request->getAttribute('slug') !== $slug) {
            return redirect($header->url(), StatusCodeInterface::STATUS_MOVED_PERMANENTLY);
        }

        return $this->viewResponse('gedcom-record-page', [
            'facts'            => $this->facts($header),
            'record'           => $header,
            'families'         => new Collection(),
            'individuals'      => new Collection(),
            'media_objects'    => new Collection(),
            'meta_description' => '',
            'meta_robots'      => 'index,follow',
            'notes'            => new Collection(),
            'sources'          => new Collection(),
            'title'            => $header->fullName(),
            'tree'             => $tree,
        ]);
    }

    /**
     * @param Header $record
     *
     * @return Collection<Fact>
     */
    private function facts(Header $record): Collection
    {
        return $record->facts()
            ->sort(static function (Fact $x, Fact $y): int {
                $sort_x = array_search($x->tag(), self::FACT_ORDER, true) ?: PHP_INT_MAX;
                $sort_y = array_search($y->tag(), self::FACT_ORDER, true) ?: PHP_INT_MAX;

                return $sort_x <=> $sort_y;
            });
    }
}
