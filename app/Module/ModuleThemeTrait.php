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

namespace Fisharebest\Webtrees\Module;

use Aura\Router\Route;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\Http\RequestHandlers\AccountEdit;
use Fisharebest\Webtrees\Http\RequestHandlers\ControlPanel;
use Fisharebest\Webtrees\Http\RequestHandlers\HomePage;
use Fisharebest\Webtrees\Http\RequestHandlers\LoginPage;
use Fisharebest\Webtrees\Http\RequestHandlers\Logout;
use Fisharebest\Webtrees\Http\RequestHandlers\ManageTrees;
use Fisharebest\Webtrees\Http\RequestHandlers\PendingChanges;
use Fisharebest\Webtrees\Http\RequestHandlers\SelectLanguage;
use Fisharebest\Webtrees\Http\RequestHandlers\SelectTheme;
use Fisharebest\Webtrees\Http\RequestHandlers\TreePage;
use Fisharebest\Webtrees\Http\RequestHandlers\TreePageEdit;
use Fisharebest\Webtrees\Http\RequestHandlers\UserPage;
use Fisharebest\Webtrees\Http\RequestHandlers\UserPageEdit;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ServerRequestInterface;

use function app;
use function assert;
use function route;
use function view;

/**
 * Trait ModuleThemeTrait - default implementation of ModuleThemeInterface
 */
trait ModuleThemeTrait
{
    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        return I18N::translate('Theme') . ' — ' . $this->title();
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
            if (!in_array($birttag, $opt_tags, true)) {
                $event = $individual->facts([$birttag])->first();
                if ($event instanceof Fact) {
                    $html .= $event->summary();
                    break;
                }
            }
        }
        // Show optional events (before death)
        foreach ($opt_tags as $key => $tag) {
            if (!in_array($tag, Gedcom::DEATH_EVENTS, true)) {
                $event = $individual->facts([$tag])->first();
                if ($event instanceof Fact) {
                    $html .= $event->summary();
                    unset($opt_tags[$key]);
                }
            }
        }
        // Show DEAT or equivalent event
        foreach (Gedcom::DEATH_EVENTS as $deattag) {
            $event = $individual->facts([$deattag])->first();
            if ($event instanceof Fact) {
                $html .= $event->summary();
                if (in_array($deattag, $opt_tags, true)) {
                    unset($opt_tags[array_search($deattag, $opt_tags, true)]);
                }
                break;
            }
        }
        // Show remaining optional events (after death)
        foreach ($opt_tags as $tag) {
            $event = $individual->facts([$tag])->first();
            if ($event instanceof Fact) {
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
        return array_merge(
            $this->individualBoxMenuCharts($individual),
            $this->individualBoxMenuFamilyLinks($individual)
        );
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
        foreach (app(ModuleService::class)->findByComponent(ModuleChartInterface::class, $individual->tree(), Auth::user()) as $chart) {
            $menu = $chart->chartBoxMenu($individual);
            if ($menu) {
                $menus[] = $menu;
            }
        }

        usort($menus, static function (Menu $x, Menu $y): int {
            return I18N::comparator()($x->getLabel(), $y->getLabel());
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
     * Generate a menu item to change the blocks on the current tree/user page.
     *
     * @param Tree $tree
     *
     * @return Menu|null
     */
    public function menuChangeBlocks(Tree $tree): ?Menu
    {
        /** @var ServerRequestInterface $request */
        $request = app(ServerRequestInterface::class);

        $route = $request->getAttribute('route');
        assert($route instanceof Route);

        if (Auth::check() && $route->name === UserPage::class) {
            return new Menu(I18N::translate('Customize this page'), route(UserPageEdit::class, ['tree' => $tree->name()]), 'menu-change-blocks');
        }

        if (Auth::isManager($tree) && $route->name === TreePage::class) {
            return new Menu(I18N::translate('Customize this page'), route(TreePageEdit::class, ['tree' => $tree->name()]), 'menu-change-blocks');
        }

        return null;
    }

    /**
     * Generate a menu item for the control panel.
     *
     * @param Tree $tree
     *
     * @return Menu|null
     */
    public function menuControlPanel(Tree $tree): ?Menu
    {
        if (Auth::isAdmin()) {
            return new Menu(I18N::translate('Control panel'), route(ControlPanel::class), 'menu-admin');
        }

        if (Auth::isManager($tree)) {
            return new Menu(I18N::translate('Control panel'), route(ManageTrees::class, ['tree' => $tree->name()]), 'menu-admin');
        }

        return null;
    }

    /**
     * A menu to show a list of available languages.
     *
     * @return Menu|null
     */
    public function menuLanguages(): ?Menu
    {
        $menu = new Menu(I18N::translate('Language'), '#', 'menu-language');

        foreach (I18N::activeLocales() as $active_locale) {
            $language_tag = $active_locale->languageTag();
            $class        = 'menu-language-' . $language_tag . (I18N::languageTag() === $language_tag ? ' active' : '');
            $menu->addSubmenu(new Menu($active_locale->endonym(), '#', $class, [
                'data-wt-post-url' => route(SelectLanguage::class, ['language' => $language_tag]),
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
    public function menuLogin(): ?Menu
    {
        if (Auth::check()) {
            return null;
        }

        $request = app(ServerRequestInterface::class);

        // Return to this page after login...
        $redirect = $request->getQueryParams()['url'] ?? (string) $request->getUri();

        $tree  = $request->getAttribute('tree');
        $route = $request->getAttribute('route');
        assert($route instanceof Route);

        // ...but switch from the tree-page to the user-page
        if ($route->name === TreePage::class) {
            $redirect = route(UserPage::class, ['tree' => $tree instanceof Tree ? $tree->name() : null]);
        }

        // Stay on the same tree page
        $url  = route(LoginPage::class, ['tree' => $tree instanceof Tree ? $tree->name() : null, 'url' => $redirect]);

        return new Menu(I18N::translate('Sign in'), $url, 'menu-login', ['rel' => 'nofollow']);
    }

    /**
     * A logout menu option (or null if we are already logged out).
     *
     * @return Menu|null
     */
    public function menuLogout(): ?Menu
    {
        if (Auth::check()) {
            $parameters = [
                'data-wt-post-url'   => route(Logout::class),
                'data-wt-reload-url' => route(HomePage::class)
            ];

            return new Menu(I18N::translate('Sign out'), '#', 'menu-logout', $parameters);
        }

        return null;
    }

    /**
     * A link to allow users to edit their account settings.
     *
     * @param Tree|null $tree
     *
     * @return Menu
     */
    public function menuMyAccount(?Tree $tree): Menu
    {
        $url = route(AccountEdit::class, ['tree' => $tree instanceof Tree ? $tree->name() : null]);

        return new Menu(I18N::translate('My account'), $url, 'menu-myaccount');
    }

    /**
     * A link to the user's individual record (individual.php).
     *
     * @param Tree $tree
     *
     * @return Menu|null
     */
    public function menuMyIndividualRecord(Tree $tree): ?Menu
    {
        $record = Registry::individualFactory()->make($tree->getUserPreference(Auth::user(), UserInterface::PREF_TREE_ACCOUNT_XREF), $tree);

        if ($record) {
            return new Menu(I18N::translate('My individual record'), $record->url(), 'menu-myrecord');
        }

        return null;
    }

    /**
     * A link to the user's personal home page.
     *
     * @param Tree $tree
     *
     * @return Menu
     */
    public function menuMyPage(Tree $tree): Menu
    {
        return new Menu(I18N::translate('My page'), route(UserPage::class, ['tree' => $tree->name()]), 'menu-mypage');
    }

    /**
     * A menu for the user's personal pages.
     *
     * @param Tree|null $tree
     *
     * @return Menu|null
     */
    public function menuMyPages(?Tree $tree): ?Menu
    {
        if (Auth::check()) {
            if ($tree instanceof Tree) {
                return new Menu(I18N::translate('My pages'), '#', 'menu-mymenu', [], array_filter([
                    $this->menuMyPage($tree),
                    $this->menuMyIndividualRecord($tree),
                    $this->menuMyPedigree($tree),
                    $this->menuMyAccount($tree),
                    $this->menuControlPanel($tree),
                    $this->menuChangeBlocks($tree),
                ]));
            }

            return $this->menuMyAccount($tree);
        }

        return null;
    }

    /**
     * A link to the user's individual record.
     *
     * @param Tree $tree
     *
     * @return Menu|null
     */
    public function menuMyPedigree(Tree $tree): ?Menu
    {
        $my_xref = $tree->getUserPreference(Auth::user(), UserInterface::PREF_TREE_ACCOUNT_XREF);

        $pedigree_chart = app(ModuleService::class)->findByComponent(ModuleChartInterface::class, $tree, Auth::user())
            ->first(static function (ModuleInterface $module): bool {
                return $module instanceof PedigreeChartModule;
            });

        if ($my_xref !== '' && $pedigree_chart instanceof PedigreeChartModule) {
            $individual = Registry::individualFactory()->make($my_xref, $tree);

            if ($individual instanceof Individual) {
                return new Menu(
                    I18N::translate('My pedigree'),
                    $pedigree_chart->chartUrl($individual),
                    'menu-mypedigree'
                );
            }
        }

        return null;
    }

    /**
     * Create a pending changes menu.
     *
     * @param Tree|null $tree
     *
     * @return Menu|null
     */
    public function menuPendingChanges(?Tree $tree): ?Menu
    {
        if ($tree instanceof Tree && $tree->hasPendingEdit() && Auth::isModerator($tree)) {
            $url = route(PendingChanges::class, [
                'tree' => $tree->name(),
                'url' => (string) app(ServerRequestInterface::class)->getUri(),
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
    public function menuThemes(): ?Menu
    {
        $themes = app(ModuleService::class)->findByInterface(ModuleThemeInterface::class, false, true);

        $current_theme = app(ModuleThemeInterface::class);

        if ($themes->count() > 1) {
            $submenus = $themes->map(static function (ModuleThemeInterface $theme) use ($current_theme): Menu {
                $active     = $theme->name() === $current_theme->name();
                $class      = 'menu-theme-' . $theme->name() . ($active ? ' active' : '');

                return new Menu($theme->title(), '#', $class, [
                    'data-wt-post-url' => route(SelectTheme::class, ['theme' => $theme->name()]),
                ]);
            });

            return new Menu(I18N::translate('Theme'), '#', 'menu-theme', [], $submenus->all());
        }

        return null;
    }

    /**
     * Miscellaneous dimensions, fonts, styles, etc.
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
     * Generate a list of items for the main menu.
     *
     * @param Tree|null $tree
     *
     * @return Menu[]
     */
    public function genealogyMenu(?Tree $tree): array
    {
        if ($tree === null) {
            return [];
        }

        return app(ModuleService::class)->findByComponent(ModuleMenuInterface::class, $tree, Auth::user())
            ->map(static function (ModuleMenuInterface $menu) use ($tree): ?Menu {
                return $menu->getMenu($tree);
            })
            ->filter()
            ->all();
    }

    /**
     * Create the genealogy menu.
     *
     * @param Menu[] $menus
     *
     * @return string
     */
    public function genealogyMenuContent(array $menus): string
    {
        return implode('', array_map(static function (Menu $menu): string {
            return view('components/menu-item', ['menu' => $menu]);
        }, $menus));
    }

    /**
     * Generate a list of items for the user menu.
     *
     * @param Tree|null $tree
     *
     * @return Menu[]
     */
    public function userMenu(?Tree $tree): array
    {
        return array_filter([
            $this->menuPendingChanges($tree),
            $this->menuMyPages($tree),
            $this->menuThemes(),
            $this->menuLanguages(),
            $this->menuLogin(),
            $this->menuLogout(),
        ]);
    }

    /**
     * A list of CSS files to include for this page.
     *
     * @return array<string>
     */
    public function stylesheets(): array
    {
        return [];
    }
}
