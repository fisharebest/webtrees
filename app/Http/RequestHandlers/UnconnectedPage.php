<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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

use Fisharebest\Algorithm\ConnectedComponent;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Validator;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function count;
use function in_array;
use function strtolower;

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
        $tree       = Validator::attributes($request)->tree();
        $user       = Validator::attributes($request)->user();
        $aliases    = Validator::queryParams($request)->boolean('aliases', false);
        $associates = Validator::queryParams($request)->boolean('associates', false);

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
            // Allow for upper/lower-case mismatches, and all-numeric XREFs
            $component = array_map(static fn ($x): string => strtolower((string) $x), $component);

            if (!in_array(strtolower($xref), $component, true)) {
                $individual_groups[] = DB::table('individuals')
                    ->where('i_file', '=', $tree->id())
                    ->whereIn('i_id', $component)
                    ->get()
                    ->map(Registry::individualFactory()->mapper($tree))
                    ->filter();
            }
        }

        usort($individual_groups, static fn (Collection $x, Collection $y): int => count($x) <=> count($y));

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
