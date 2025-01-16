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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Http\RequestHandlers\CalendarPage;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Tree;

/**
 * Class CalendarMenuModule - provide a menu option for the calendar
 */
class CalendarMenuModule extends AbstractModule implements ModuleMenuInterface
{
    use ModuleMenuTrait;

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Calendar');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “Calendar” module */
        return I18N::translate('The calendar menu.');
    }

    /**
     * The default position for this menu.  It can be changed in the control panel.
     *
     * @return int
     */
    public function defaultMenuOrder(): int
    {
        return 4;
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
            $this->calendarDayMenu($tree),
            $this->calendarMonthMenu($tree),
            $this->calendarYearMenu($tree),
        ];

        return new Menu(I18N::translate('Calendar'), '#', 'menu-calendar', ['rel' => 'nofollow'], $submenu);
    }

    /**
     * @param Tree $tree
     *
     * @return Menu
     */
    protected function calendarDayMenu(Tree $tree): Menu
    {
        return new Menu(I18N::translate('Day'), route(CalendarPage::class, [
            'view' => 'day',
            'tree' => $tree->name(),
        ]), 'menu-calendar-day', ['rel' => 'nofollow']);
    }

    /**
     * @param Tree $tree
     *
     * @return Menu
     */
    protected function calendarMonthMenu(Tree $tree): Menu
    {
        return new Menu(I18N::translate('Month'), route(CalendarPage::class, [
            'view' => 'month',
            'tree' => $tree->name(),
        ]), 'menu-calendar-month', ['rel' => 'nofollow']);
    }

    /**
     * @param Tree $tree
     *
     * @return Menu
     */
    protected function calendarYearMenu(Tree $tree): Menu
    {
        return new Menu(I18N::translate('Year'), route(CalendarPage::class, [
            'view' => 'year',
            'tree' => $tree->name(),
        ]), 'menu-calendar-year', ['rel' => 'nofollow']);
    }
}
