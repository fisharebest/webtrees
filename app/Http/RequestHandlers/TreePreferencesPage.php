<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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
use Fisharebest\Webtrees\Contracts\ElementInterface;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Elements\UnknownElement;
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
use Psr\Http\Server\RequestHandlerInterface;

use function e;
use function explode;
use function in_array;

/**
 * Edit the tree preferences.
 */
class TreePreferencesPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    private ModuleService $module_service;

    private TreeService $tree_service;

    private UserService $user_service;

    /**
     * @param ModuleService $module_service
     * @param TreeService   $tree_service
     * @param UserService   $user_service
     */
    public function __construct(
        ModuleService $module_service,
        TreeService $tree_service,
        UserService $user_service
    ) {
        $this->module_service = $module_service;
        $this->tree_service   = $tree_service;
        $this->user_service   = $user_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
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
            Auth::PRIV_USER => I18N::translate('Show to members'),
            Auth::PRIV_NONE => I18N::translate('Show to managers'),
            Auth::PRIV_HIDE => I18N::translate('Hide from everyone'),
        ];

        // For historical reasons, we have two fields in one
        $calendar_formats = explode('_and_', $tree->getPreference('CALENDAR_FORMAT') . '_and_');

        // Split into separate fields
        $relatives_events = explode(',', $tree->getPreference('SHOW_RELATIVES_EVENTS'));

        $pedigree_individual = Registry::individualFactory()->make($tree->getPreference('PEDIGREE_ROOT_ID'), $tree);

        $members = $this->user_service->all()->filter(static function (UserInterface $user) use ($tree): bool {
            return Auth::isMember($tree, $user);
        });

        $ignore_facts = ['CHAN', 'CHIL', 'FAMC', 'FAMS', 'HUSB', 'SUBM', 'WIFE', 'NAME', 'SEX'];

        $all_family_facts = Collection::make(Registry::elementFactory()->make('FAM')->subtags())
            ->filter(static fn (string $value, string $key): bool => !in_array($key, $ignore_facts, true))
            ->mapWithKeys(static fn (string $value, string $key): array => [$key => 'FAM:' . $key])
            ->map(static fn (string $tag): ElementInterface => Registry::elementFactory()->make($tag))
            ->filter(static fn (ElementInterface $element): bool => !$element instanceof UnknownElement)
            ->map(static fn (ElementInterface $element): string => $element->label())
            ->sort(I18N::comparator());

        $all_individual_facts = Collection::make(Registry::elementFactory()->make('INDI')->subtags())
            ->filter(static fn (string $value, string $key): bool => !in_array($key, $ignore_facts, true))
            ->mapWithKeys(static fn (string $value, string $key): array => [$key => 'INDI:' . $key])
            ->map(static fn (string $tag): ElementInterface => Registry::elementFactory()->make($tag))
            ->filter(static fn (ElementInterface $element): bool => !$element instanceof UnknownElement)
            ->map(static fn (ElementInterface $element): string => $element->label())
            ->sort(I18N::comparator());

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
}
