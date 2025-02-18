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

use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Http\Exceptions\HttpBadRequestException;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

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
        $tree = Validator::attributes($request)->tree();

        $delete_default_resn_id = Validator::parsedBody($request)->array('delete');

        DB::table('default_resn')
            ->whereIn('default_resn_id', $delete_default_resn_id)
            ->delete();

        $xrefs     = Validator::parsedBody($request)->array('xref');
        $tag_types = Validator::parsedBody($request)->array('tag_type');
        $resns     = Validator::parsedBody($request)->array('resn');

        $count_xrefs     = count($xrefs);
        $count_tag_types = count($tag_types);
        $count_resns     = count($resns);

        if ($count_xrefs !== $count_tag_types || $count_xrefs !== $count_resns) {
            $message = 'Bad parameter count: ' . $count_xrefs . '/' . $count_tag_types . '/' . $count_resns;
            throw new HttpBadRequestException($message);
        }

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

        $hide_live_people           = Validator::parsedBody($request)->string('HIDE_LIVE_PEOPLE');
        $keep_alive_years_birth     = Validator::parsedBody($request)->integer('KEEP_ALIVE_YEARS_BIRTH', 0);
        $keep_alive_years_death     = Validator::parsedBody($request)->integer('KEEP_ALIVE_YEARS_DEATH', 0);
        $max_alive_age              = Validator::parsedBody($request)->integer('MAX_ALIVE_AGE');
        $require_authentication     = Validator::parsedBody($request)->string('REQUIRE_AUTHENTICATION');
        $show_dead_people           = Validator::parsedBody($request)->string('SHOW_DEAD_PEOPLE');
        $show_living_names          = Validator::parsedBody($request)->string('SHOW_LIVING_NAMES');
        $show_private_relationships = Validator::parsedBody($request)->string('SHOW_PRIVATE_RELATIONSHIPS');

        $tree->setPreference('HIDE_LIVE_PEOPLE', $hide_live_people);
        $tree->setPreference('KEEP_ALIVE_YEARS_BIRTH', (string) $keep_alive_years_birth);
        $tree->setPreference('KEEP_ALIVE_YEARS_DEATH', (string) $keep_alive_years_death);
        $tree->setPreference('MAX_ALIVE_AGE', (string) $max_alive_age);
        $tree->setPreference('REQUIRE_AUTHENTICATION', $require_authentication);
        $tree->setPreference('SHOW_DEAD_PEOPLE', $show_dead_people);
        $tree->setPreference('SHOW_LIVING_NAMES', $show_living_names);
        $tree->setPreference('SHOW_PRIVATE_RELATIONSHIPS', $show_private_relationships);

        FlashMessages::addMessage(I18N::translate('The preferences for the family tree “%s” have been updated.', e($tree->title())), 'success');

        // Coming soon...
        $all_trees = Validator::parsedBody($request)->boolean('all_trees', false);
        $new_trees = Validator::parsedBody($request)->boolean('new_trees', false);

        if ($all_trees) {
            FlashMessages::addMessage(I18N::translate('The preferences for all family trees have been updated.', e($tree->title())), 'success');
        }
        if ($new_trees) {
            FlashMessages::addMessage(I18N::translate('The preferences for new family trees have been updated.', e($tree->title())), 'success');
        }

        return redirect(route(ManageTrees::class, ['tree' => $tree->name()]));
    }
}
