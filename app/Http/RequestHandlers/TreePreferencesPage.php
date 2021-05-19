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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Services\UserService;
use Fisharebest\Webtrees\SurnameTradition;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function app;
use function assert;
use function e;
use function explode;

/**
 * Edit the tree preferences.
 */
class TreePreferencesPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    private const ALL_FAM_FACTS = [
        'RESN', 'ANUL', 'CENS', 'DIV', 'DIVF', 'ENGA', 'MARB', 'MARC', 'MARR', 'MARL', 'MARS', 'RESI', 'EVEN',
        'NCHI', 'SUBM', 'SLGS', 'REFN', 'RIN', 'CHAN', 'NOTE', 'SOUR', 'OBJE',
        '_NMR', '_COML', '_MBON', '_MARI', '_SEPR', '_TODO',
    ];

    private const ALL_INDI_FACTS = [
        'RESN', 'NAME', 'SEX', 'BIRT', 'CHR', 'DEAT', 'BURI', 'CREM', 'ADOP', 'BAPM', 'BARM', 'BASM',
        'BLES', 'CHRA', 'CONF', 'FCOM', 'ORDN', 'NATU', 'EMIG', 'IMMI', 'CENS', 'PROB', 'WILL',
        'GRAD', 'RETI', 'EVEN', 'CAST', 'DSCR', 'EDUC', 'IDNO', 'NATI', 'NCHI', 'NMR', 'OCCU', 'PROP',
        'RELI', 'RESI', 'SSN', 'TITL', 'FACT', 'BAPL', 'CONL', 'ENDL', 'SLGC', 'SUBM', 'ASSO',
        'ALIA', 'ANCI', 'DESI', 'RFN', 'AFN', 'REFN', 'RIN', 'CHAN', 'NOTE', 'SOUR', 'OBJE',
        '_BRTM', '_DEG', '_DNA', '_EYEC', '_FNRL', '_HAIR', '_HEIG', '_HNM', '_HOL', '_INTE', '_MDCL',
        '_MEDC', '_MILI', '_MILT', '_NAME', '_NAMS', '_NLIV', '_NMAR', '_PRMN', '_TODO', '_UID', '_WEIG', '_YART',
    ];

    private const ALL_NAME_FACTS = [
        'FONE', 'ROMN', '_HEB', '_AKA', '_MARNM',
    ];

    private const ALL_PLAC_FACTS = [
        'FONE', 'ROMN', '_GOV', '_HEB',
    ];

    private const ALL_REPO_FACTS = [
        'NAME', 'ADDR', 'PHON', 'EMAIL', 'FAX', 'WWW', 'NOTE', 'REFN', 'RIN', 'CHAN', 'RESN',
    ];

    private const ALL_SOUR_FACTS = [
        'DATA', 'AUTH', 'TITL', 'ABBR', 'PUBL', 'TEXT', 'REPO', 'REFN', 'RIN',
        'CHAN', 'NOTE', 'OBJE', 'RESN',
    ];

    private ModuleService $module_service;

    private TreeService $tree_service;

    private UserService $user_service;

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

        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

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
            /* I18N: None of the other options */
            ''         => I18N::translate('none'),
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

        $all_fam_facts = Collection::make(self::ALL_FAM_FACTS)
            ->mapWithKeys(static function (string $tag): array {
                return [$tag => Registry::elementFactory()->make('FAM:' . $tag)->label()];
            })
            ->sort(I18N::comparator());

        $all_indi_facts = Collection::make(self::ALL_INDI_FACTS)
            ->mapWithKeys(static function (string $tag): array {
                return [$tag => Registry::elementFactory()->make('INDI:' . $tag)->label()];
            })
            ->sort(I18N::comparator());

        $all_name_facts = Collection::make(self::ALL_NAME_FACTS)
            ->mapWithKeys(static function (string $tag): array {
                return [$tag => Registry::elementFactory()->make('INDI:NAME:' . $tag)->label()];
            })
            ->sort(I18N::comparator());

        $all_plac_facts = Collection::make(self::ALL_PLAC_FACTS)
            ->mapWithKeys(static function (string $tag): array {
                return [$tag => Registry::elementFactory()->make('INDI:FACT:PLAC:' . $tag)->label()];
            })
            ->sort(I18N::comparator());

        $all_repo_facts = Collection::make(self::ALL_REPO_FACTS)
            ->mapWithKeys(static function (string $tag): array {
                return [$tag => Registry::elementFactory()->make('REPO:' . $tag)->label()];
            })
            ->sort(I18N::comparator());

        $all_sour_facts = Collection::make(self::ALL_SOUR_FACTS)
            ->mapWithKeys(static function (string $tag): array {
                return [$tag => Registry::elementFactory()->make('SOUR:' . $tag)->label()];
            })
            ->sort(I18N::comparator());

        $all_surname_traditions = SurnameTradition::allDescriptions();

        $tree_count = $this->tree_service->all()->count();

        $title = I18N::translate('Preferences') . ' — ' . e($tree->title());

        $base_url = app(ServerRequestInterface::class)->getAttribute('base_url');

        return $this->viewResponse('admin/trees-preferences', [
            'all_fam_facts'            => $all_fam_facts,
            'all_indi_facts'           => $all_indi_facts,
            'all_name_facts'           => $all_name_facts,
            'all_plac_facts'           => $all_plac_facts,
            'all_repo_facts'           => $all_repo_facts,
            'all_sour_facts'           => $all_sour_facts,
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
