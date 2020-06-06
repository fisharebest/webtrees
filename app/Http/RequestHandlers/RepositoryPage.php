<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Factory;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\Repository;
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
 * Show a repository's page.
 */
class RepositoryPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    // Show the repository's facts in this order:
    private const FACT_ORDER = [
        1 => 'NAME',
        'ADDR',
        'NOTE',
        'WWW',
        'REFN',
        'RIN',
        '_UID',
        'CHAN',
        'RESN',
    ];

    /** @var ClipboardService */
    private $clipboard_service;

    /**
     * RepositoryPage constructor.
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

        $repository = Factory::repository()->make($xref, $tree);
        $repository = Auth::checkRepositoryAccess($repository, false);

        // Redirect to correct xref/slug
        if ($repository->xref() !== $xref || $request->getAttribute('slug') !== $repository->slug()) {
            return redirect($repository->url(), StatusCodeInterface::STATUS_MOVED_PERMANENTLY);
        }

        return $this->viewResponse('repository-page', [
            'clipboard_facts'  => $this->clipboard_service->pastableFacts($repository, new Collection()),
            'facts'            => $this->facts($repository),
            'meta_description' => '',
            'meta_robots'      => 'index,follow',
            'repository'       => $repository,
            'sources'          => $repository->linkedSources('REPO'),
            'title'            => $repository->fullName(),
            'tree'             => $tree,
        ]);
    }

    /**
     * @param Repository $record
     *
     * @return Collection<Fact>
     */
    private function facts(Repository $record): Collection
    {
        return $record->facts()
            ->sort(static function (Fact $x, Fact $y): int {
                $sort_x = array_search($x->getTag(), self::FACT_ORDER, true) ?: PHP_INT_MAX;
                $sort_y = array_search($y->getTag(), self::FACT_ORDER, true) ?: PHP_INT_MAX;

                return $sort_x <=> $sort_y;
            });
    }
}
