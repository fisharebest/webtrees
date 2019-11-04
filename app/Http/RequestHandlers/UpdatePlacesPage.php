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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;
use function e;
use function preg_match_all;
use function preg_quote;

/**
 * Update place names.
 */
class UpdatePlacesPage implements RequestHandlerInterface
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

        $search  = $request->getQueryParams()['search'] ?? '';
        $replace = $request->getQueryParams()['replace'] ?? '';

        if ($search !== '' && $replace !== '') {
            // This does not take into account pending changes.
            $union = DB::table('families')
                ->where('f_file', '=', $tree->id())
                ->whereContains('f_gedcom', $search)
                ->select(['f_gedcom AS gedcom']);

            $changes = DB::table('individuals')
                ->where('i_file', '=', $tree->id())
                ->whereContains('i_gedcom', $search)
                ->select(['i_gedcom AS gedcom'])
                ->unionAll($union)
                ->pluck('gedcom')
                ->mapWithKeys(static function (string $gedcom) use ($search, $replace): array {
                    preg_match_all('/\n2 PLAC ((?:.*, )*)' . preg_quote($search, '/') . '(\n|$)/i', $gedcom, $matches);

                    $changes = [];
                    foreach ($matches[1] as $prefix) {
                        $changes[$prefix . $search] = $prefix . $replace;
                    }

                    return $changes;
                })
                ->sort()
                ->all();
        } else {
            $changes = new Collection();
        }

        /* I18N: Renumber the records in a family tree */
        $title = I18N::translate('Update place names') . ' — ' . e($tree->title());

        $this->layout = 'layouts/administration';

        return $this->viewResponse('admin/trees-places', [
            'changes' => $changes,
            'replace' => $replace,
            'search'  => $search,
            'title'   => $title,
            'tree'    => $tree,
        ]);
    }
}
