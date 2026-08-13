<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\ElementInterface;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Elements\UnknownElement;
use Fisharebest\Webtrees\Enums\AccessLevel;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\Validator;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function array_unique;
use function e;
use function explode;
use function implode;
use function in_array;
use function preg_replace;
use function redirect;
use function route;
use function trim;

final class TreePreferences
{
    use ViewResponseTrait;

    public function __construct(
        private ModuleService $module_service,
        private TreeService $tree_service,
        private UserService $user_service,
    ) {
    }

    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        $tree        = Validator::attributes($request)->tree();
        $data_folder = Registry::filesystem()->dataName();

        $french_calendar_start    = new Date('22 SEP 1792');
        $french_calendar_end      = new Date('31 DEC 1805');
        $gregorian_calendar_start = new Date('15 OCT 1582');

        $surname_list_styles = [
            /* I18N: Layout option for lists of names */
            'style1' => I18N::translate('list'),
            /* I18N: Layout option for lists of names */
            'style2' => I18N::translate('table'),
            /* I18N: Layout option for lists of names */
            'style3' => I18N::translate('tag cloud'),
        ];

        $page_layouts = [
            /* I18N: page orientation */
            0 => I18N::translate('Portrait'),
            /* I18N: page orientation */
            1 => I18N::translate('Landscape'),
        ];

        $formats = [
            /* I18N: https://en.wikipedia.org/wiki/Plain_text */
            ''         => I18N::translate('plain text'),
            /* I18N: https://en.wikipedia.org/wiki/Markdown */
            'markdown' => I18N::translate('markdown'),
        ];

        $source_types = [
            0 => I18N::translate('none'),
            1 => I18N::translate('facts'),
            2 => I18N::translate('records'),
        ];

        $theme_options = $this->module_service
            ->findByInterface(ModuleThemeInterface::class)
            ->map($this->module_service->titleMapper())
            ->prepend(I18N::translate('<default theme>'), '');

        $privacy_options = [
            AccessLevel::Member->value  => AccessLevel::Member->label(),
            AccessLevel::Manager->value => AccessLevel::Manager->label(),
            AccessLevel::Hidden->value  => AccessLevel::Hidden->label(),
        ];

        // For historical reasons, we have two fields in one
        $calendar_formats = explode('_and_', $tree->getPreference('CALENDAR_FORMAT') . '_and_');

        // Split into separate fields
        $relatives_events = explode(',', $tree->getPreference('SHOW_RELATIVES_EVENTS'));

        $pedigree_individual = Registry::individualFactory()->make($tree->getPreference('PEDIGREE_ROOT_ID'), $tree);

        $members = $this->user_service->all()->filter(static fn (UserInterface $user): bool => Auth::isMember($tree, $user));

        $ignore_facts = ['CHAN', 'CHIL', 'FAMC', 'FAMS', 'HUSB', 'SUBM', 'WIFE', 'NAME', 'SEX'];

        $all_family_facts = Collection::make(Registry::elementFactory()->make('FAM')->subtags())
            ->filter(static fn (string $value, string $key): bool => !in_array($key, $ignore_facts, true))
            ->mapWithKeys(static fn (string $value, string $key): array => [$key => 'FAM:' . $key])
            ->map(static fn (string $tag): ElementInterface => Registry::elementFactory()->make($tag))
            ->filter(static fn (ElementInterface $element): bool => !$element instanceof UnknownElement)
            ->map(static fn (ElementInterface $element): string => $element->label())
            ->sort(I18N::compare(...));

        $all_individual_facts = Collection::make(Registry::elementFactory()->make('INDI')->subtags())
            ->filter(static fn (string $value, string $key): bool => !in_array($key, $ignore_facts, true))
            ->mapWithKeys(static fn (string $value, string $key): array => [$key => 'INDI:' . $key])
            ->map(static fn (string $tag): ElementInterface => Registry::elementFactory()->make($tag))
            ->filter(static fn (ElementInterface $element): bool => !$element instanceof UnknownElement)
            ->map(static fn (ElementInterface $element): string => $element->label())
            ->sort(I18N::compare(...));

        $all_surname_traditions = Registry::surnameTraditionFactory()->list();

        $tree_count = $this->tree_service->all()->count();

        $title = I18N::translate('Preferences') . ' — ' . e($tree->title());

        $base_url = Validator::attributes($request)->string('base_url');

        return $this->viewResponse('admin/trees-preferences', [
            'all_family_facts'         => $all_family_facts,
            'all_individual_facts'     => $all_individual_facts,
            'all_surname_traditions'   => $all_surname_traditions,
            'base_url'                 => $base_url,
            'calendar_formats'         => $calendar_formats,
            'data_folder'              => $data_folder,
            'formats'                  => $formats,
            'french_calendar_end'      => $french_calendar_end,
            'french_calendar_start'    => $french_calendar_start,
            'gregorian_calendar_start' => $gregorian_calendar_start,
            'members'                  => $members,
            'page_layouts'             => $page_layouts,
            'pedigree_individual'      => $pedigree_individual,
            'privacy_options'          => $privacy_options,
            'relatives_events'         => $relatives_events,
            'source_types'             => $source_types,
            'surname_list_styles'      => $surname_list_styles,
            'theme_options'            => $theme_options,
            'title'                    => $title,
            'tree'                     => $tree,
            'tree_count'               => $tree_count,
        ]);
    }

    public function post(ServerRequestInterface $request): ResponseInterface
    {
        $tree = Validator::attributes($request)->tree();

        // For backwards compatibility with webtrees 1.x we store the two calendar formats in one variable
        // e.g. "gregorian_and_jewish"
        $calendar_format_0           = Validator::parsedBody($request)->string('CALENDAR_FORMAT0');
        $calendar_format_1           = Validator::parsedBody($request)->string('CALENDAR_FORMAT1');
        $calendar_format             = implode('_and_', array_unique([$calendar_format_0, $calendar_format_1]));
        $chart_box_tags              = Validator::parsedBody($request)->list('CHART_BOX_TAGS');
        $contact_user_id             = Validator::parsedBody($request)->integer('CONTACT_USER_ID', 0);
        $expand_notes                = Validator::parsedBody($request)->boolean('EXPAND_NOTES');
        $expand_sources              = Validator::parsedBody($request)->boolean('EXPAND_SOURCES');
        $fam_facts_quick             = Validator::parsedBody($request)->list('FAM_FACTS_QUICK');
        $format_text                 = Validator::parsedBody($request)->string('FORMAT_TEXT');
        $gedcom                      = Validator::parsedBody($request)->isNotEmpty()->string('gedcom');
        $generate_uuids              = Validator::parsedBody($request)->boolean('GENERATE_UIDS');
        $hide_gedcom_errors          = Validator::parsedBody($request)->boolean('HIDE_GEDCOM_ERRORS');
        $indi_facts_quick            = Validator::parsedBody($request)->list('INDI_FACTS_QUICK');
        $MEDIA_DIRECTORY             = Validator::parsedBody($request)->string('MEDIA_DIRECTORY');
        $media_upload                = Validator::parsedBody($request)->integer('MEDIA_UPLOAD');
        $meta_description            = Validator::parsedBody($request)->string('META_DESCRIPTION');
        $meta_title                  = Validator::parsedBody($request)->string('META_TITLE');
        $no_update_chan              = Validator::parsedBody($request)->boolean('NO_UPDATE_CHAN');
        $pedigree_root_id            = Validator::parsedBody($request)->string('PEDIGREE_ROOT_ID');
        $quick_required_facts        = Validator::parsedBody($request)->list('QUICK_REQUIRED_FACTS');
        $quick_required_famfacts     = Validator::parsedBody($request)->list('QUICK_REQUIRED_FAMFACTS');
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
        $show_relatives_events       = Validator::parsedBody($request)->list('SHOW_RELATIVES_EVENTS');
        $sublist_trigger_i           = Validator::parsedBody($request)->integer('SUBLIST_TRIGGER_I');
        $surname_list_style          = Validator::parsedBody($request)->string('SURNAME_LIST_STYLE');
        $surname_tradition           = Validator::parsedBody($request)->string('SURNAME_TRADITION');
        $use_silhouette              = Validator::parsedBody($request)->boolean('USE_SILHOUETTE');
        $webmaster_user_id           = Validator::parsedBody($request)->integer('WEBMASTER_USER_ID', 0);
        $title                       = Validator::parsedBody($request)->string('title');

        $tree->setPreference('CALENDAR_FORMAT', $calendar_format);
        $tree->setPreference('CHART_BOX_TAGS', implode(',', $chart_box_tags));
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

        if (Auth::isAdmin()) {
            // Only accept valid folders for MEDIA_DIRECTORY
            $MEDIA_DIRECTORY = preg_replace('/[:\/\\\\]+/', '/', $MEDIA_DIRECTORY);
            $MEDIA_DIRECTORY = trim($MEDIA_DIRECTORY, '/') . '/';

            // Tree name needs to be unique
            $duplicate = DB::table('gedcom')
                ->where('gedcom_name', '=', $gedcom)
                ->where('gedcom_id', '<>', $tree->id())
                ->exists();

            if ($duplicate) {
                FlashMessages::addMessage(I18N::translate('The family tree “%s” already exists.', e($gedcom)), 'danger');
                $gedcom = $tree->name();
            }
        } else {
            $MEDIA_DIRECTORY = $tree->mediaFolder();
            $gedcom          = $tree->name();
        }

        DB::table('gedcom')
            ->where('gedcom_id', '=', $tree->id())
            ->update([
                'contact_user_id' => $contact_user_id === 0 ? null : $contact_user_id,
                'media_folder'    => $MEDIA_DIRECTORY,
                'gedcom_name'     => $gedcom,
                'support_user_id' => $webmaster_user_id === 0 ? null : $webmaster_user_id,
                'title'           => $title,
            ]);

        if ($tree->name() !== $gedcom) {
            DB::table('site_setting')
                ->where('setting_name', '=', 'DEFAULT_GEDCOM')
                ->where('setting_value', '=', $tree->name())
                ->update(['setting_value' => $gedcom]);
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

        $url = route(ManageTrees::class, ['tree' => $gedcom]);

        return redirect($url);
    }
}
