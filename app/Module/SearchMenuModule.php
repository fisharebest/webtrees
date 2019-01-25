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
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Tree;

/**
 * Class SearchMenuModule - provide a menu option for the search options
 */
class SearchMenuModule extends AbstractModule implements ModuleMenuInterface
{
    use ModuleMenuTrait;

    /**
     * How should this module be labelled on tabs, menus, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Search');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “Reports” module */
        return I18N::translate('The search menu.');
    }

    /**
     * The default position for this menu.  It can be changed in the control panel.
     *
     * @return int
     */
    public function defaultMenuOrder(): int
    {
        return 6;
    }

    /**
     * A menu, to be added to the main application menu.
     *
     * @param Tree $tree
     *
     * @return Menu|null
     */
    public function getMenu(Tree $tree): ?Menu
    {
        $submenu = [
            $this->menuSearchGeneral($tree),
            $this->menuSearchPhonetic($tree),
            $this->menuSearchAdvanced($tree),
            $this->menuSearchAndReplace($tree),
        ];

        $submenu = array_filter($submenu);

        return new Menu(I18N::translate('Search'), '#', 'menu-search', ['rel' => 'nofollow'], $submenu);
    }

    /**
     * @param Tree $tree
     *
     * @return Menu
     */
    protected function menuSearchGeneral(Tree $tree): Menu
    {
        return new Menu(I18N::translate('General search'), route('search-general', ['ged' => $tree->name()]), 'menu-search-general', ['rel' => 'nofollow']);
    }

    /**
     * @param Tree $tree
     *
     * @return Menu
     */
    protected function menuSearchPhonetic(Tree $tree): Menu
    {
        /* I18N: search using “sounds like”, rather than exact spelling */
        return new Menu(I18N::translate('Phonetic search'), route('search-phonetic', ['ged' => $tree->name(), 'action' => 'soundex']), 'menu-search-soundex', ['rel' => 'nofollow']);
    }

    /**
     * @param Tree $tree
     *
     * @return Menu
     */
    protected function menuSearchAdvanced(Tree $tree): Menu
    {
        return new Menu(I18N::translate('Advanced search'), route('search-advanced', ['ged' => $tree->name()]), 'menu-search-advanced', ['rel' => 'nofollow']);
    }

    /**
     * @param Tree $tree
     *
     * @return Menu|null
     */
    protected function menuSearchAndReplace(Tree $tree): ?Menu
    {
        if (Auth::isEditor($tree)) {
            return new Menu(I18N::translate('Search and replace'), route('search-replace', [
                'ged'    => $tree->name(),
                'action' => 'replace',
            ]), 'menu-search-replace');
        }

        return null;
    }
}
