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

use Fisharebest\Algorithm\ConnectedComponent;
use Fisharebest\Webtrees\Factory;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\User;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;

/**
 * Find groups of unrelated individuals.
 */
class UnconnectedPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $user = $request->getAttribute('user');
        assert($user instanceof User);

        $aliases    = (bool) ($request->getQueryParams()['aliases'] ?? false);
        $associates = (bool) ($request->getQueryParams()['associates'] ?? false);

        // Connect individuals using these links.
        $links = ['FAMS', 'FAMC'];

        if ($aliases) {
            $links[] = 'ALIA';
        }

        if ($associates) {
            $links[] = 'ASSO';
            $links[] = '_ASSO';
        }

        $rows = DB::table('link')
            ->where('l_file', '=', $tree->id())
            ->whereIn('l_type', $links)
            ->select(['l_from', 'l_to'])
            ->get();

        $graph = DB::table('individuals')
            ->where('i_file', '=', $tree->id())
            ->pluck('i_id')
            ->mapWithKeys(static function (string $xref): array {
                return [$xref => []];
            })
            ->all();

        foreach ($rows as $row) {
            $graph[$row->l_from][$row->l_to] = 1;
            $graph[$row->l_to][$row->l_from] = 1;
        }

        $algorithm  = new ConnectedComponent($graph);
        $components = $algorithm->findConnectedComponents();
        $root       = $tree->significantIndividual($user);
        $xref       = $root->xref();

        /** @var Individual[][] */
        $individual_groups = [];

        foreach ($components as $component) {
            if (!in_array($xref, $component, true)) {
                $individual_groups[] = DB::table('individuals')
                    ->where('i_file', '=', $tree->id())
                    ->whereIn('i_id', $component)
                    ->get()
                    ->map(Factory::individual()->mapper($tree))
                    ->filter();
            }
        }

        $title = I18N::translate('Find unrelated individuals') . ' — ' . e($tree->title());

        $this->layout = 'layouts/administration';

        return $this->viewResponse('admin/trees-unconnected', [
            'aliases'           => $aliases,
            'associates'        => $associates,
            'root'              => $root,
            'individual_groups' => $individual_groups,
            'title'             => $title,
            'tree'              => $tree,
        ]);
    }
}
