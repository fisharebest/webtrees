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

use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;
use function e;
use function preg_match;
use function preg_quote;
use function preg_replace;
use function redirect;
use function route;

/**
 * Update place names.
 */
class UpdatePlacesAction implements RequestHandlerInterface
{
    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $replace = $request->getParsedBody()['replace'] ?? '';
        $search  = $request->getParsedBody()['search'] ?? '';
        $submit  = $request->getParsedBody()['submit'] ?? '';

        if ($search !== '' && $replace !== '' && $submit === 'update') {
            $individual_changes = DB::table('individuals')
                ->where('i_file', '=', $tree->id())
                ->whereContains('i_gedcom', $search)
                ->select(['individuals.*'])
                ->get()
                ->map(Individual::rowMapper());

            $family_changes = DB::table('families')
                ->where('f_file', '=', $tree->id())
                ->whereContains('f_gedcom', $search)
                ->select(['families.*'])
                ->get()
                ->map(Family::rowMapper());

            $changes = $individual_changes->merge($family_changes)
                ->mapWithKeys(static function (GedcomRecord $record) use ($search, $replace): array {
                    $changes = [];

                    foreach ($record->facts() as $fact) {
                        $old_place = $fact->attribute('PLAC');
                        if (preg_match('/(^|, )' . preg_quote($search, '/') . '$/i', $old_place)) {
                            $new_place           = preg_replace('/(^|, )' . preg_quote($search, '/') . '$/i', '$1' . $replace, $old_place);
                            $changes[$old_place] = $new_place;
                            $gedcom              = preg_replace('/(\n2 PLAC (?:.*, )*)' . preg_quote($search, '/') . '(\n|$)/i', '$1' . $replace . '$2', $fact->gedcom());
                            $record->updateFact($fact->id(), $gedcom, false);
                        }
                    }

                    return $changes;
                })
                ->sort();

            $feedback = I18N::translate('The following places have been changed:') . '<ul>';

            foreach ($changes as $old_place => $new_place) {
                $feedback .= '<li>' . e($old_place) . ' &rarr; ' . e($new_place) . '</li>';
            }
            $feedback .= '</ul>';

            FlashMessages::addMessage($feedback, 'success');
        }

        return redirect(route(UpdatePlacesPage::class, [
            'tree'    => $tree->name(),
            'replace' => $replace,
            'search'  => $search,
        ]));
    }
}
