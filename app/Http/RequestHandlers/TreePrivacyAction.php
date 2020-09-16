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

use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;
use function e;
use function redirect;
use function route;

/**
 * Edit the tree privacy.
 */
class TreePrivacyAction implements RequestHandlerInterface
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

        $params = (array) $request->getParsedBody();

        $delete_default_resn_id = $params['delete'] ?? [];

        DB::table('default_resn')
            ->whereIn('default_resn_id', $delete_default_resn_id)
            ->delete();

        $xrefs     = $params['xref'] ?? [];
        $tag_types = $params['tag_type'] ?? [];
        $resns     = $params['resn'] ?? [];

        foreach ($xrefs as $n => $xref) {
            $tag_type = $tag_types[$n];
            $resn     = $resns[$n];

            // Delete any existing data
            if ($tag_type !== '' && $xref !== '') {
                DB::table('default_resn')
                    ->where('gedcom_id', '=', $tree->id())
                    ->where('tag_type', '=', $tag_type)
                    ->where('xref', '=', $xref)
                    ->delete();
            }

            if ($tag_type !== '' && $xref === '') {
                DB::table('default_resn')
                    ->where('gedcom_id', '=', $tree->id())
                    ->where('tag_type', '=', $tag_type)
                    ->whereNull('xref')
                    ->delete();
            }

            if ($tag_type === '' && $xref !== '') {
                DB::table('default_resn')
                    ->where('gedcom_id', '=', $tree->id())
                    ->whereNull('tag_type')
                    ->where('xref', '=', $xref)
                    ->delete();
            }

            // Add (or update) the new data
            if ($tag_type !== '' || $xref !== '') {
                DB::table('default_resn')->insert([
                    'gedcom_id' => $tree->id(),
                    'xref'      => $xref === '' ? null : $xref,
                    'tag_type'  => $tag_type === '' ? null : $tag_type,
                    'resn'      => $resn,
                ]);
            }
        }

        $tree->setPreference('HIDE_LIVE_PEOPLE', $params['HIDE_LIVE_PEOPLE']);
        $tree->setPreference('KEEP_ALIVE_YEARS_BIRTH', $params['KEEP_ALIVE_YEARS_BIRTH']);
        $tree->setPreference('KEEP_ALIVE_YEARS_DEATH', $params['KEEP_ALIVE_YEARS_DEATH']);
        $tree->setPreference('MAX_ALIVE_AGE', $params['MAX_ALIVE_AGE']);
        $tree->setPreference('REQUIRE_AUTHENTICATION', $params['REQUIRE_AUTHENTICATION']);
        $tree->setPreference('SHOW_DEAD_PEOPLE', $params['SHOW_DEAD_PEOPLE']);
        $tree->setPreference('SHOW_LIVING_NAMES', $params['SHOW_LIVING_NAMES']);
        $tree->setPreference('SHOW_PRIVATE_RELATIONSHIPS', $params['SHOW_PRIVATE_RELATIONSHIPS']);

        FlashMessages::addMessage(I18N::translate('The preferences for the family tree “%s” have been updated.', e($tree->title())), 'success');

        // Coming soon...
        $all_trees = $params['all_trees'] ?? '';
        $new_trees = $params['new_trees'] ?? '';

        if ($all_trees === 'on') {
            FlashMessages::addMessage(I18N::translate('The preferences for all family trees have been updated.', e($tree->title())), 'success');
        }
        if ($new_trees === 'on') {
            FlashMessages::addMessage(I18N::translate('The preferences for new family trees have been updated.', e($tree->title())), 'success');
        }

        return redirect(route(ManageTrees::class, ['tree' => $tree->name()]));
    }
}
