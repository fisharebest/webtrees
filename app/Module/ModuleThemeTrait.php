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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;

/**
 * Trait ModuleThemeTrait - default implementation of ModuleThemeInterface
 */
trait ModuleThemeTrait
{
    /** @var  Request */
    protected $request;

    /** @var Tree|null */
    protected $tree;

    /**
     * @param Request   $request
     * @param Tree|null $tree The current tree (if there is one).
     */
    public function __construct(Request $request, ?Tree $tree)
    {
        $this->request = $request;
        $this->tree    = $tree;
    }

    /**
     * Add markup to the secondary menu.
     *
     * @return string
     */
    public function formatSecondaryMenu(): string
    {
        return
            '<ul class="nav wt-secondary-menu">' .
            implode('', array_map(function (Menu $menu): string {
                return $this->formatSecondaryMenuItem($menu);
            }, $this->secondaryMenu())) .
            '</ul>';
    }

    /**
     * Add markup to an item in the secondary menu.
     *
     * @param Menu $menu
     *
     * @return string
     */
    public function formatSecondaryMenuItem(Menu $menu): string
    {
        return $menu->bootstrap4();
    }

    /**
     * Display an icon for this fact.
     *
     * @TODO use CSS for this
     *
     * @param Fact $fact
     *
     * @return string
     */
    public function icon(Fact $fact): string
    {
        $asset = 'public/css/' . $this->name() . '/images/facts/' . $fact->getTag() . '.png';
        if (file_exists(WT_ROOT . 'public' . $asset)) {
            return '<img src="' . e(asset($asset)) . '" title="' . GedcomTag::getLabel($fact->getTag()) . '">';
        }

        // Spacer image - for alignment - until we move to a sprite.
        $asset = 'public/css/' . $this->name() . '/images/facts/NULL.png';
        if (file_exists(WT_ROOT . 'public' . $asset)) {
            return '<img src="' . e(asset($asset)) . '">';
        }

        return '';
    }

    /**
     * Display an individual in a box - for charts, etc.
     *
     * @param Individual $individual
     *
     * @return string
     */
    public function individualBox(Individual $individual): string
    {
        return view('chart-box', ['individual' => $individual]);
    }

    /**
     * Display an empty box - for a missing individual in a chart.
     *
     * @return string
     */
    public function individualBoxEmpty(): string
    {
        return '<div class="wt-chart-box"></div>';
    }

    /**
     * Display an individual in a box - for charts, etc.
     *
     * @param Individual $individual
     *
     * @return string
     */
    public function individualBoxLarge(Individual $individual): string
    {
        return $this->individualBox($individual);
    }

    /**
     * Display an individual in a box - for charts, etc.
     *
     * @param Individual $individual
     *
     * @return string
     */
    public function individualBoxSmall(Individual $individual): string
    {
        return $this->individualBox($individual);
    }

    /**
     * Display an individual in a box - for charts, etc.
     *
     * @return string
     */
    public function individualBoxSmallEmpty(): string
    {
        return '<div class="wt-chart-box"></div>';
    }

    /**
     * Generate the facts, for display in charts.
     *
     * @param Individual $individual
     *
     * @return string
     */
    public function individualBoxFacts(Individual $individual): string
    {
        $html = '';

        $opt_tags = preg_split('/\W/', $individual->tree()->getPreference('CHART_BOX_TAGS'), 0, PREG_SPLIT_NO_EMPTY);
        // Show BIRT or equivalent event
        foreach (Gedcom::BIRTH_EVENTS as $birttag) {
            if (!in_array($birttag, $opt_tags)) {
                $event = $individual->firstFact($birttag);
                if ($event) {
                    $html .= $event->summary();
                    break;
                }
            }
        }
        // Show optional events (before death)
        foreach ($opt_tags as $key => $tag) {
            if (!in_array($tag, Gedcom::DEATH_EVENTS)) {
                $event = $individual->firstFact($tag);
                if ($event !== null) {
                    $html .= $event->summary();
                    unset($opt_tags[$key]);
                }
            }
        }
        // Show DEAT or equivalent event
        foreach (Gedcom::DEATH_EVENTS as $deattag) {
            $event = $individual->firstFact($deattag);
            if ($event) {
                $html .= $event->summary();
                if (in_array($deattag, $opt_tags)) {
                    unset($opt_tags[array_search($deattag, $opt_tags)]);
                }
                break;
            }
        }
        // Show remaining optional events (after death)
        foreach ($opt_tags as $tag) {
            $event = $individual->firstFact($tag);
            if ($event) {
                $html .= $event->summary();
            }
        }

        return $html;
    }

    /**
     * Links, to show in chart boxes;
     *
     * @param Individual $individual
     *
     * @return Menu[]
     */
    public function individualBoxMenu(Individual $individual): array
    {
        $menus = array_merge(
            $this->individualBoxMenuCharts($individual),
            $this->individualBoxMenuFamilyLinks($individual)
        );

        return $menus;
    }

    /**
     * Chart links, to show in chart boxes;
     *
     * @param Individual $individual
     *
     * @return Menu[]
     */
    public function individualBoxMenuCharts(Individual $individual): array
    {
        $menus = [];
        foreach (app(ModuleService::class)->findByComponent('chart', $this->tree, Auth::user()) as $chart) {
            $menu = $chart->chartBoxMenu($individual);
            if ($menu) {
                $menus[] = $menu;
            }
        }

        usort($menus, function (Menu $x, Menu $y) {
            return I18N::strcasecmp($x->getLabel(), $y->getLabel());
        });

        return $menus;
    }

    /**
     * Family links, to show in chart boxes.
     *
     * @param Individual $individual
     *
     * @return Menu[]
     */
    public function individualBoxMenuFamilyLinks(Individual $individual): array
    {
        $menus = [];

        foreach ($individual->spouseFamilies() as $family) {
            $menus[] = new Menu('<strong>' . I18N::translate('Family with spouse') . '</strong>', $family->url());
            $spouse  = $family->spouse($individual);
            if ($spouse && $spouse->canShowName()) {
                $menus[] = new Menu($spouse->fullName(), $spouse->url());
            }
            foreach ($family->children() as $child) {
                if ($child->canShowName()) {
                    $menus[] = new Menu($child->fullName(), $child->url());
                }
            }
        }

        return $menus;
    }

    /**
     * Generate a menu item to change the blocks on the current (index.php) page.
     *
     * @return Menu|null
     */
    public function menuChangeBlocks()
    {
        if (Auth::check() && $this->request->get('route') === 'user-page') {
            return new Menu(I18N::translate('Customize this page'), route('user-page-edit', ['ged' => $this->tree->name()]), 'menu-change-blocks');
        }

        if (Auth::isManager($this->tree) && $this->request->get('route') === 'tree-page') {
            return new Menu(I18N::translate('Customize this page'), route('tree-page-edit', ['ged' => $this->tree->name()]), 'menu-change-blocks');
        }

        return null;
    }

    /**
     * Generate a menu item for the control panel.
     *
     * @return Menu|null
     */
    public function menuControlPanel()
    {
        if (Auth::isAdmin()) {
            return new Menu(I18N::translate('Control panel'), route('admin-control-panel'), 'menu-admin');
        }

        if (Auth::isManager($this->tree)) {
            return new Menu(I18N::translate('Control panel'), route('admin-control-panel-manager'), 'menu-admin');
        }

        return null;
    }

    /**
     * A menu to show a list of available languages.
     *
     * @return Menu|null
     */
    public function menuLanguages()
    {
        $menu = new Menu(I18N::translate('Language'), '#', 'menu-language');

        foreach (I18N::activeLocales() as $locale) {
            $language_tag = $locale->languageTag();
            $class        = 'menu-language-' . $language_tag . (WT_LOCALE === $language_tag ? ' active' : '');
            $menu->addSubmenu(new Menu($locale->endonym(), '#', $class, [
                'onclick'       => 'return false;',
                'data-language' => $language_tag,
            ]));
        }

        if (count($menu->getSubmenus()) > 1) {
            return $menu;
        }

        return null;
    }

    /**
     * A login menu option (or null if we are already logged in).
     *
     * @return Menu|null
     */
    public function menuLogin()
    {
        if (Auth::check()) {
            return null;
        }

        // Return to this page after login...
        $url = $this->request->getRequestUri();

        // ...but switch from the tree-page to the user-page
        $url = str_replace('route=tree-page', 'route=user-page', $url);

        return new Menu(I18N::translate('Sign in'), route('login', ['url' => $url]), 'menu-login', ['rel' => 'nofollow']);
    }

    /**
     * A logout menu option (or null if we are already logged out).
     *
     * @return Menu|null
     */
    public function menuLogout()
    {
        if (Auth::check()) {
            return new Menu(I18N::translate('Sign out'), route('logout'), 'menu-logout');
        }

        return null;
    }

    /**
     * A link to allow users to edit their account settings.
     *
     * @return Menu|null
     */
    public function menuMyAccount()
    {
        if (Auth::check()) {
            return new Menu(I18N::translate('My account'), route('my-account'));
        }

        return null;
    }

    /**
     * A link to the user's individual record (individual.php).
     *
     * @return Menu|null
     */
    public function menuMyIndividualRecord()
    {
        $record = Individual::getInstance($this->tree->getUserPreference(Auth::user(), 'gedcomid'), $this->tree);

        if ($record) {
            return new Menu(I18N::translate('My individual record'), $record->url(), 'menu-myrecord');
        }

        return null;
    }

    /**
     * A link to the user's personal home page.
     *
     * @return Menu
     */
    public function menuMyPage(): Menu
    {
        return new Menu(I18N::translate('My page'), route('user-page', ['ged' => $this->tree->name()]), 'menu-mypage');
    }

    /**
     * A menu for the user's personal pages.
     *
     * @return Menu|null
     */
    public function menuMyPages()
    {
        if (Auth::id() && $this->tree !== null) {
            return new Menu(I18N::translate('My pages'), '#', 'menu-mymenu', [], array_filter([
                $this->menuMyPage(),
                $this->menuMyIndividualRecord(),
                $this->menuMyPedigree(),
                $this->menuMyAccount(),
                $this->menuControlPanel(),
                $this->menuChangeBlocks(),
            ]));
        }

        return null;
    }

    /**
     * A link to the user's individual record.
     *
     * @return Menu|null
     */
    public function menuMyPedigree()
    {
        $gedcomid = $this->tree->getUserPreference(Auth::user(), 'gedcomid');

        $pedigree_chart = app(ModuleService::class)->findByComponent('chart', $this->tree, Auth::user())
            ->filter(function (ModuleInterface $module): bool {
                return $module instanceof PedigreeChartModule;
            });

        if ($gedcomid !== '' && $pedigree_chart instanceof PedigreeChartModule) {
            return new Menu(
                I18N::translate('My pedigree'),
                route('pedigree', [
                    'xref' => $gedcomid,
                    'ged'  => $this->tree->name(),
                ]),
                'menu-mypedigree'
            );
        }

        return null;
    }

    /**
     * Create a pending changes menu.
     *
     * @return Menu|null
     */
    public function menuPendingChanges()
    {
        if ($this->pendingChangesExist()) {
            $url = route('show-pending', [
                'ged' => $this->tree ? $this->tree->name() : '',
                'url' => $this->request->getRequestUri(),
            ]);

            return new Menu(I18N::translate('Pending changes'), $url, 'menu-pending');
        }

        return null;
    }

    /**
     * Themes menu.
     *
     * @return Menu|null
     */
    public function menuThemes()
    {
        $themes = app(ModuleService::class)->findByInterface(ModuleThemeInterface::class);

        $current_theme = app()->make(ModuleThemeInterface::class);

        if ($themes->count() > 1) {
            $submenus = $themes->map(function (ModuleThemeInterface $theme) use ($current_theme): Menu {
                $active     = $theme->name() === $current_theme->name();
                $class      = 'menu-theme-' . $theme->name() . ($active ? ' active' : '');

                return new Menu($theme->title(), '#', $class, [
                    'onclick'    => 'return false;',
                    'data-theme' => $theme->name(),
                ]);
            });

            return  new Menu(I18N::translate('Theme'), '#', 'menu-theme', [], $submenus->all());
        }

        return null;
    }

    /**
     * Misecellaneous dimensions, fonts, styles, etc.
     *
     * @param string $parameter_name
     *
     * @return string|int|float
     */
    public function parameter($parameter_name)
    {
        return '';
    }

    /**
     * Are there any pending changes for us to approve?
     *
     * @return bool
     */
    public function pendingChangesExist(): bool
    {
        return $this->tree && $this->tree->hasPendingEdit() && Auth::isModerator($this->tree);
    }

    /**
     * Generate a list of items for the main menu.
     *
     * @return Menu[]
     */
    public function primaryMenu(): array
    {
        return app(ModuleService::class)->findByComponent('menu', $this->tree, Auth::user())
            ->map(function (ModuleMenuInterface $menu): ?Menu {
                return $menu->getMenu($this->tree);
            })
            ->filter()
            ->all();
    }

    /**
     * Create the primary menu.
     *
     * @param Menu[] $menus
     *
     * @return string
     */
    public function primaryMenuContent(array $menus): string
    {
        return implode('', array_map(function (Menu $menu): string {
            return $menu->bootstrap4();
        }, $menus));
    }

    /**
     * Generate a list of items for the user menu.
     *
     * @return Menu[]
     */
    public function secondaryMenu(): array
    {
        return array_filter([
            $this->menuPendingChanges(),
            $this->menuMyPages(),
            $this->menuThemes(),
            $this->menuLanguages(),
            $this->menuLogin(),
            $this->menuLogout(),
        ]);
    }

    /**
     * A list of CSS files to include for this page.
     *
     * @return string[]
     */
    public function stylesheets(): array
    {
        return [];
    }
}
