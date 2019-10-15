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
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Services\ClipboardService;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function redirect;

/**
 * Controller for the repository page.
 */
class RepositoryController extends AbstractBaseController
{
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
     * MediaController constructor.
     *
     * @param ClipboardService $clipboard_service
     */
    public function __construct(ClipboardService $clipboard_service)
    {
        $this->clipboard_service = $clipboard_service;
    }

    /**
     * Show a repository's page.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function show(ServerRequestInterface $request): ResponseInterface
    {
        $slug       = $request->getAttribute('slug') ?? '';
        $tree       = $request->getAttribute('tree');
        $xref       = $request->getAttribute('xref');
        $repository = Repository::getInstance($xref, $tree);

        Auth::checkRepositoryAccess($repository, false);

        if ($slug !== $repository->slug()) {
            return redirect($repository->url());
        }

        return $this->viewResponse('repository-page', [
            'clipboard_facts' => $this->clipboard_service->pastableFacts($repository, new Collection()),
            'facts'           => $this->facts($repository),
            'meta_robots'     => 'index,follow',
            'repository'      => $repository,
            'sources'         => $repository->linkedSources('REPO'),
            'title'           => $repository->fullName(),
        ]);
    }

    /**
     * @param Repository $record
     *
     * @return Collection
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
