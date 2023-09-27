<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Validator;
use PDOException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_unique;
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
        $tree = Validator::attributes($request)->tree();

        // For backwards compatibility with webtrees 1.x we store the two calendar formats in one variable
        // e.g. "gregorian_and_jewish"
        $calendar_format_0           = Validator::parsedBody($request)->string('CALENDAR_FORMAT0');
        $calendar_format_1           = Validator::parsedBody($request)->string('CALENDAR_FORMAT1');
        $calendar_format             = implode('_and_', array_unique([$calendar_format_0, $calendar_format_1]));
        $chart_box_tags              = Validator::parsedBody($request)->array('CHART_BOX_TAGS');
        $contact_user_id             = Validator::parsedBody($request)->integer('CONTACT_USER_ID', 0);
        $expand_notes                = Validator::parsedBody($request)->boolean('EXPAND_NOTES');
        $expand_sources              = Validator::parsedBody($request)->boolean('EXPAND_SOURCES');
        $fam_facts_quick             = Validator::parsedBody($request)->array('FAM_FACTS_QUICK');
        $format_text                 = Validator::parsedBody($request)->string('FORMAT_TEXT');
        $generate_uuids              = Validator::parsedBody($request)->boolean('GENERATE_UIDS');
        $hide_gedcom_errors          = Validator::parsedBody($request)->boolean('HIDE_GEDCOM_ERRORS');
        $indi_facts_quick            = Validator::parsedBody($request)->array('INDI_FACTS_QUICK');
        $media_upload                = Validator::parsedBody($request)->integer('MEDIA_UPLOAD');
        $meta_description            = Validator::parsedBody($request)->string('META_DESCRIPTION');
        $meta_title                  = Validator::parsedBody($request)->string('META_TITLE');
        $no_update_chan              = Validator::parsedBody($request)->boolean('NO_UPDATE_CHAN');
        $pedigree_root_id            = Validator::parsedBody($request)->string('PEDIGREE_ROOT_ID');
        $quick_required_facts        = Validator::parsedBody($request)->array('QUICK_REQUIRED_FACTS');
        $quick_required_famfacts     = Validator::parsedBody($request)->array('QUICK_REQUIRED_FAMFACTS');
        $show_counter                = Validator::parsedBody($request)->boolean('SHOW_COUNTER');
        $show_est_list_dates         = Validator::parsedBody($request)->boolean('SHOW_EST_LIST_DATES');
        $show_fact_icons             = Validator::parsedBody($request)->boolean('SHOW_FACT_ICONS');
        $show_gedcom_record          = Validator::parsedBody($request)->boolean('SHOW_GEDCOM_RECORD');
        $show_highlight_images       = Validator::parsedBody($request)->boolean('SHOW_HIGHLIGHT_IMAGES');
        $show_last_change            = Validator::parsedBody($request)->boolean('SHOW_LAST_CHANGE');
        $show_media_download         = Validator::parsedBody($request)->integer('SHOW_MEDIA_DOWNLOAD');
        $show_no_watermark           = Validator::parsedBody($request)->integer('SHOW_NO_WATERMARK');
        $show_parents_age            = Validator::parsedBody($request)->boolean('SHOW_PARENTS_AGE');
        $show_pedigree_places        = Validator::parsedBody($request)->integer('SHOW_PEDIGREE_PLACES');
        $show_pedigree_places_suffix = Validator::parsedBody($request)->integer('SHOW_PEDIGREE_PLACES_SUFFIX');
        $show_relatives_events       = Validator::parsedBody($request)->array('SHOW_RELATIVES_EVENTS');
        $sublist_trigger_i           = Validator::parsedBody($request)->integer('SUBLIST_TRIGGER_I');
        $surname_list_style          = Validator::parsedBody($request)->string('SURNAME_LIST_STYLE');
        $surname_tradition           = Validator::parsedBody($request)->string('SURNAME_TRADITION');
        $use_silhouette              = Validator::parsedBody($request)->boolean('USE_SILHOUETTE');
        $webmaster_user_id           = Validator::parsedBody($request)->integer('WEBMASTER_USER_ID', 0);
        $title                       = Validator::parsedBody($request)->string('title');

        $contact_user_id   = $contact_user_id === 0 ? '' : (string) $contact_user_id;
        $webmaster_user_id = $webmaster_user_id === 0 ? '' : (string) $webmaster_user_id;

        $tree->setPreference('CALENDAR_FORMAT', $calendar_format);
        $tree->setPreference('CHART_BOX_TAGS', implode(',', $chart_box_tags));
        $tree->setPreference('CONTACT_USER_ID', $contact_user_id);
        $tree->setPreference('EXPAND_NOTES', (string) $expand_notes);
        $tree->setPreference('EXPAND_SOURCES', (string) $expand_sources);
        $tree->setPreference('FAM_FACTS_QUICK', implode(',', $fam_facts_quick));
        $tree->setPreference('FORMAT_TEXT', $format_text);
        $tree->setPreference('GENERATE_UIDS', (string) $generate_uuids);
        $tree->setPreference('HIDE_GEDCOM_ERRORS', (string) $hide_gedcom_errors);
        $tree->setPreference('INDI_FACTS_QUICK', implode(',', $indi_facts_quick));
        $tree->setPreference('MEDIA_UPLOAD', (string) $media_upload);
        $tree->setPreference('META_DESCRIPTION', $meta_description);
        $tree->setPreference('META_TITLE', $meta_title);
        $tree->setPreference('NO_UPDATE_CHAN', (string) $no_update_chan);
        $tree->setPreference('PEDIGREE_ROOT_ID', $pedigree_root_id);
        $tree->setPreference('QUICK_REQUIRED_FACTS', implode(',', $quick_required_facts));
        $tree->setPreference('QUICK_REQUIRED_FAMFACTS', implode(',', $quick_required_famfacts));
        $tree->setPreference('SHOW_COUNTER', (string) $show_counter);
        $tree->setPreference('SHOW_EST_LIST_DATES', (string) $show_est_list_dates);
        $tree->setPreference('SHOW_FACT_ICONS', (string) $show_fact_icons);
        $tree->setPreference('SHOW_GEDCOM_RECORD', (string) $show_gedcom_record);
        $tree->setPreference('SHOW_HIGHLIGHT_IMAGES', (string) $show_highlight_images);
        $tree->setPreference('SHOW_LAST_CHANGE', (string) $show_last_change);
        $tree->setPreference('SHOW_MEDIA_DOWNLOAD', (string) $show_media_download);
        $tree->setPreference('SHOW_NO_WATERMARK', (string) $show_no_watermark);
        $tree->setPreference('SHOW_PARENTS_AGE', (string) $show_parents_age);
        $tree->setPreference('SHOW_PEDIGREE_PLACES', (string) $show_pedigree_places);
        $tree->setPreference('SHOW_PEDIGREE_PLACES_SUFFIX', (string) $show_pedigree_places_suffix);
        $tree->setPreference('SHOW_RELATIVES_EVENTS', implode(',', $show_relatives_events));
        $tree->setPreference('SUBLIST_TRIGGER_I', (string) $sublist_trigger_i);
        $tree->setPreference('SURNAME_LIST_STYLE', $surname_list_style);
        $tree->setPreference('SURNAME_TRADITION', $surname_tradition);
        $tree->setPreference('USE_SILHOUETTE', (string) $use_silhouette);
        $tree->setPreference('WEBMASTER_USER_ID', (string) $webmaster_user_id);
        $tree->setPreference('title', $title);

        if (Auth::isAdmin()) {
            // Only accept valid folders for MEDIA_DIRECTORY
            $MEDIA_DIRECTORY = Validator::parsedBody($request)->string('MEDIA_DIRECTORY');
            $MEDIA_DIRECTORY = preg_replace('/[:\/\\\\]+/', '/', $MEDIA_DIRECTORY);
            $MEDIA_DIRECTORY = trim($MEDIA_DIRECTORY, '/') . '/';

            $tree->setPreference('MEDIA_DIRECTORY', $MEDIA_DIRECTORY);
        }

        $gedcom = Validator::parsedBody($request)->string('gedcom');
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
            } catch (PDOException) {
                // Probably a duplicate name.
            }
        }

        FlashMessages::addMessage(I18N::translate('The preferences for the family tree “%s” have been updated.', e($tree->title())), 'success');

        // Coming soon...
        $all_trees = Validator::parsedBody($request)->boolean('all_trees', false);
        $new_trees = Validator::parsedBody($request)->boolean('new_trees', false);

        if ($all_trees) {
            FlashMessages::addMessage(I18N::translate('The preferences for all family trees have been updated.'), 'success');
        }

        if ($new_trees) {
            FlashMessages::addMessage(I18N::translate('The preferences for new family trees have been updated.'), 'success');
        }

        return redirect($url);
    }
}
