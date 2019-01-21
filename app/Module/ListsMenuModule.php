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

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Tree;
use Illuminate\Database\Capsule\Manager as DB;

/**
 * Class ListsMenuModule - provide a menu option for the lists
 */
class ListsMenuModule extends AbstractModule implements ModuleInterface, ModuleMenuInterface
{
    use ModuleMenuTrait;

    /** {@inheritdoc} */
    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Lists');
    }

    /** {@inheritdoc} */
    public function description(): string
    {
        /* I18N: Description of the “Reports” module */
        return I18N::translate('The lists menu.');
    }

    /**
     * The default position for this menu.  It can be changed in the control panel.
     *
     * @return int
     */
    public function defaultMenuOrder(): int
    {
        return 30;
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
        // Do not show empty lists
        $sources_exist = DB::table('sources')
            ->where('s_file', '=', $tree->id())
            ->exists();

        $repositories_exist = DB::table('other')
            ->where('o_file', '=', $tree->id())
            ->where('o_type', '=', 'REPO')
            ->exists();

        $notes_exist = DB::table('other')
            ->where('o_file', '=', $tree->id())
            ->where('o_type', '=', 'NOTE')
            ->exists();

        $media_exist = DB::table('media')
            ->where('m_file', '=', $tree->id())
            ->exists();

        $submenus = [
            new Menu(I18N::translate('Individuals'), route('individual-list', ['ged' => $tree->name()]), 'menu-list-indi'),
            new Menu(I18N::translate('Families'), route('family-list', ['ged' => $tree->name()]), 'menu-list-fam'),
            new Menu(I18N::translate('Branches'), route('branches', ['ged' => $tree->name()]), 'menu-branches', ['rel' => 'nofollow']),
            new Menu(I18N::translate('Place hierarchy'), route('place-hierarchy', ['ged' => $tree->name()]), 'menu-list-plac', ['rel' => 'nofollow']),
        ];
        
        if ($media_exist) {
            $submenus[] = new Menu(I18N::translate('Media objects'), route('media-list', ['ged' => $tree->name()]), 'menu-list-obje', ['rel' => 'nofollow']);
        }
        if ($repositories_exist) {
            $submenus[] = new Menu(I18N::translate('Repositories'), route('repository-list', ['ged' => $tree->name()]), 'menu-list-repo', ['rel' => 'nofollow']);
        }
        if ($sources_exist) {
            $submenus[] = new Menu(I18N::translate('Sources'), route('source-list', ['ged' => $tree->name()]), 'menu-list-sour', ['rel' => 'nofollow']);
        }
        if ($notes_exist) {
            $submenus[] = new Menu(I18N::translate('Shared notes'), route('note-list', ['ged' => $tree->name()]), 'menu-list-note', ['rel' => 'nofollow']);
        }

        uasort($submenus, function (Menu $x, Menu $y) {
            return I18N::strcasecmp($x->getLabel(), $y->getLabel());
        });

        return new Menu(I18N::translate('Lists'), '#', 'menu-list', [], $submenus);
    }
}
