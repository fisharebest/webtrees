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

use Exception;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_unique;
use function assert;
use function e;
use function implode;
use function preg_replace;
use function redirect;
use function route;
use function trim;

/**
 * Edit the tree preferences.
 */
class TreePreferencesAction implements RequestHandlerInterface
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

        $tree->setPreference('ADVANCED_NAME_FACTS', implode(',', $params['ADVANCED_NAME_FACTS'] ?? []));
        $tree->setPreference('ADVANCED_PLAC_FACTS', implode(',', $params['ADVANCED_PLAC_FACTS'] ?? []));
        // For backwards compatibility with webtrees 1.x we store the two calendar formats in one variable
        // e.g. "gregorian_and_jewish"
        $tree->setPreference('CALENDAR_FORMAT', implode('_and_', array_unique([
            $params['CALENDAR_FORMAT0'] ?? 'none',
            $params['CALENDAR_FORMAT1'] ?? 'none',
        ])));
        $tree->setPreference('CHART_BOX_TAGS', implode(',', $params['CHART_BOX_TAGS'] ?? []));
        $tree->setPreference('CONTACT_USER_ID', $params['CONTACT_USER_ID'] ?? '');
        $tree->setPreference('EXPAND_NOTES', $params['EXPAND_NOTES'] ?? '');
        $tree->setPreference('EXPAND_SOURCES', $params['EXPAND_SOURCES'] ?? '');
        $tree->setPreference('FAM_FACTS_ADD', implode(',', $params['FAM_FACTS_ADD'] ?? []));
        $tree->setPreference('FAM_FACTS_QUICK', implode(',', $params['FAM_FACTS_QUICK'] ?? []));
        $tree->setPreference('FAM_FACTS_UNIQUE', implode(',', $params['FAM_FACTS_UNIQUE'] ?? []));
        $tree->setPreference('FULL_SOURCES', $params['FULL_SOURCES'] ?? '');
        $tree->setPreference('FORMAT_TEXT', $params['FORMAT_TEXT'] ?? '');
        $tree->setPreference('GENERATE_UIDS', $params['GENERATE_UIDS'] ?? '');
        $tree->setPreference('HIDE_GEDCOM_ERRORS', $params['HIDE_GEDCOM_ERRORS'] ?? '');
        $tree->setPreference('INDI_FACTS_ADD', implode(',', $params['INDI_FACTS_ADD'] ?? []));
        $tree->setPreference('INDI_FACTS_QUICK', implode(',', $params['INDI_FACTS_QUICK'] ?? []));
        $tree->setPreference('INDI_FACTS_UNIQUE', implode(',', $params['INDI_FACTS_UNIQUE'] ?? []));
        $tree->setPreference('MEDIA_UPLOAD', $params['MEDIA_UPLOAD'] ?? '');
        $tree->setPreference('META_DESCRIPTION', $params['META_DESCRIPTION'] ?? '');
        $tree->setPreference('META_TITLE', $params['META_TITLE'] ?? '');
        $tree->setPreference('NO_UPDATE_CHAN', $params['NO_UPDATE_CHAN'] ?? '');
        $tree->setPreference('PEDIGREE_ROOT_ID', $params['PEDIGREE_ROOT_ID'] ?? '');
        $tree->setPreference('PREFER_LEVEL2_SOURCES', $params['PREFER_LEVEL2_SOURCES'] ?? '');
        $tree->setPreference('QUICK_REQUIRED_FACTS', implode(',', $params['QUICK_REQUIRED_FACTS'] ?? []));
        $tree->setPreference('QUICK_REQUIRED_FAMFACTS', implode(',', $params['QUICK_REQUIRED_FAMFACTS'] ?? []));
        $tree->setPreference('SHOW_COUNTER', $params['SHOW_COUNTER'] ?? '');
        $tree->setPreference('SHOW_EST_LIST_DATES', $params['SHOW_EST_LIST_DATES'] ?? '');
        $tree->setPreference('SHOW_FACT_ICONS', $params['SHOW_FACT_ICONS'] ?? '');
        $tree->setPreference('SHOW_GEDCOM_RECORD', $params['SHOW_GEDCOM_RECORD'] ?? '');
        $tree->setPreference('SHOW_HIGHLIGHT_IMAGES', $params['SHOW_HIGHLIGHT_IMAGES'] ?? '');
        $tree->setPreference('SHOW_LAST_CHANGE', $params['SHOW_LAST_CHANGE'] ?? '');
        $tree->setPreference('SHOW_MEDIA_DOWNLOAD', $params['SHOW_MEDIA_DOWNLOAD'] ?? '');
        $tree->setPreference('SHOW_NO_WATERMARK', $params['SHOW_NO_WATERMARK'] ?? '');
        $tree->setPreference('SHOW_PARENTS_AGE', $params['SHOW_PARENTS_AGE'] ?? '');
        $tree->setPreference('SHOW_PEDIGREE_PLACES', $params['SHOW_PEDIGREE_PLACES'] ?? '');
        $tree->setPreference('SHOW_PEDIGREE_PLACES_SUFFIX', $params['SHOW_PEDIGREE_PLACES_SUFFIX'] ?? '');
        $tree->setPreference('SHOW_RELATIVES_EVENTS', implode(',', $params['SHOW_RELATIVES_EVENTS'] ?? []));
        $tree->setPreference('SUBLIST_TRIGGER_I', $params['SUBLIST_TRIGGER_I'] ?? '200');
        $tree->setPreference('SURNAME_LIST_STYLE', $params['SURNAME_LIST_STYLE'] ?? '');
        $tree->setPreference('SURNAME_TRADITION', $params['SURNAME_TRADITION'] ?? '');
        $tree->setPreference('USE_SILHOUETTE', $params['USE_SILHOUETTE'] ?? '');
        $tree->setPreference('WEBMASTER_USER_ID', $params['WEBMASTER_USER_ID'] ?? '');
        $tree->setPreference('title', $params['title'] ?? '');

        if (Auth::isAdmin()) {
            // Only accept valid folders for MEDIA_DIRECTORY
            $MEDIA_DIRECTORY = $params['MEDIA_DIRECTORY'] ?? '';
            $MEDIA_DIRECTORY = preg_replace('/[:\/\\\\]+/', '/', $MEDIA_DIRECTORY);
            $MEDIA_DIRECTORY = trim($MEDIA_DIRECTORY, '/') . '/';

            $tree->setPreference('MEDIA_DIRECTORY', $MEDIA_DIRECTORY);
        }

        $gedcom = $params['gedcom'] ?? '';
        $url    = route(ManageTrees::class, ['tree' => $tree->name()]);

        if (Auth::isAdmin() && $gedcom !== '' && $gedcom !== $tree->name()) {
            try {
                DB::table('gedcom')
                    ->where('gedcom_id', '=', $tree->id())
                    ->update(['gedcom_name' => $gedcom]);

                // Did we rename the default tree?
                DB::table('site_setting')
                    ->where('setting_name', '=', 'DEFAULT_GEDCOM')
                    ->where('setting_value', '=', $tree->name())
                    ->update(['setting_value' => $gedcom]);

                $url = route(ManageTrees::class, ['tree' => $gedcom]);
            } catch (Exception $ex) {
                // Probably a duplicate name.
            }
        }

        FlashMessages::addMessage(I18N::translate('The preferences for the family tree “%s” have been updated.', e($tree->title())), 'success');

        // Coming soon...
        $all_trees = $params['all_trees'] ?? '';
        $new_trees = $params['new_trees'] ?? '';

        if ($all_trees === 'on') {
            FlashMessages::addMessage(I18N::translate('The preferences for all family trees have been updated.'), 'success');
        }

        if ($new_trees === 'on') {
            FlashMessages::addMessage(I18N::translate('The preferences for new family trees have been updated.'), 'success');
        }

        return redirect($url);
    }
}
