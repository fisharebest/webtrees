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

namespace Fisharebest\Webtrees\Elements;

use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\LocalizationService;
use Fisharebest\Webtrees\Tree;

use function app;
use function e;
use function preg_replace_callback;
use function view;

/**
 * DATE_VALUE := {Size=1:35}
 * [ <DATE> | <DATE_PERIOD> | <DATE_RANGE>| <DATE_APPROXIMATED> | INT <DATE> (<DATE_PHRASE>) | (<DATE_PHRASE>) ]
 * The DATE_VALUE represents the date of an activity, attribute, or event where:
 * INT = Interpreted from knowledge about the associated date phrase included in parentheses.
 * An acceptable alternative to the date phrase choice is to use one of the other choices such as
 * <DATE_APPROXIMATED> choice as the DATE line value and then include the date phrase value as a
 * NOTE value subordinate to the DATE line tag.
 * The date value can take on the date form of just a date, an approximated date, between a date
 * and another date, and from one date to another date. The preferred form of showing date
 * imprecision, is to show, for example, MAY 1890 rather than ABT 12 MAY 1890. This is because
 * limits have not been assigned to the precision of the prefixes such as ABT or EST.
 */
class DateValue extends AbstractElement
{
    /**
     * An edit control for this data.
     *
     * @param string $id
     * @param string $name
     * @param string $value
     * @param Tree   $tree
     *
     * @return string
     */
    public function edit(string $id, string $name, string $value, Tree $tree): string
    {
        // Need to know if the user prefers DMY/MDY/YMD so we can validate dates properly.
        $dmy = app(LocalizationService::class)->dateFormatToOrder(I18N::dateFormat());

        return
            '<div class="input-group">' .
            '<input class="form-control" type="text" id="' . $id . '" name="' . $name . '" value="' . e($value) . '" onchange="webtrees.reformatDate(this, \'' . e($dmy) . '\')" dir="ltr" />' .
            view('edit/input-addon-calendar', ['id' => $id]) .
            view('edit/input-addon-help', ['fact' => 'DATE']) .
            '</div>' .
            '<div id="caldiv' . $id . '" style="position:absolute;visibility:hidden;background-color:white;z-index:1000"></div>' .
            '<div class="form-text">' . (new Date($value))->display() . '</div>';
    }

    /**
     * Escape @ signs in a GEDCOM export.
     *
     * @param string $value
     *
     * @return string
     */
    public function escape(string $value): string
    {
        // Only escape @ signs in an INT phrase
        return preg_replace_callback('/\(.*@.*\)/', static function (array $matches): string {
            return strtr($matches[0], ['@' => '@@']);
        }, $value);
    }

    /**
     * Display the value of this type of element.
     *
     * @param string $value
     * @param Tree   $tree
     *
     * @return string
     */
    public function value(string $value, Tree $tree): string
    {
        $canonical = $this->canonical($value);

        $date = new Date($canonical);

        return $date->display();
    }
}
