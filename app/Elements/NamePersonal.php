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

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\SurnameTradition;
use Fisharebest\Webtrees\Tree;

use function e;
use function view;

/**
 * NAME_PERSONAL := {Size=1:120}
 * [
 * <NAME_TEXT> | /<NAME_TEXT>/ |
 * <NAME_TEXT> /<NAME_TEXT>/ | /<NAME_TEXT>/ <NAME_TEXT> |
 * <NAME_TEXT> /<NAME_TEXT>/ <NAME_TEXT> ]
 * The surname of an individual, if known, is enclosed between two slash (/)
 * characters. The order of the name parts should be the order that the person
 * would, by custom of their culture, have used when giving it to a recorder.
 * Early versions of Personal Ancestral File ® and other products did not use
 * the trailing slash when the surname was the last element of the name. If
 * part of name is illegible, that part is indicated by an ellipsis (...).
 * Capitalize the name of a person or place in the conventional manner—
 * capitalize the first letter of each part and lowercase the other letters,
 * unless conventional usage is otherwise. For example: McMurray.
 * Examples:
 * William Lee (given name only or surname not known)
 * /Parry/ (surname only)
 * William Lee /Parry/
 * William Lee /Mac Parry/ (both parts (Mac and Parry) are surname parts
 * William /Lee/ Parry (surname imbedded in the name string)
 * William Lee /Pa.../
 */
class NamePersonal extends AbstractElement
{
    protected const MAXIMUM_LENGTH = 120;

    protected const SUBTAGS = [
        'TYPE' => '0:1',
        'NPFX' => '0:1',
        'GIVN' => '0:1',
        'SPFX' => '0:1',
        'SURN' => '0:1',
        'NSFX' => '0:1',
        'NICK' => '0:1',
    ];

    /**
     * Create a default value for this element.
     *
     * @param Tree $tree
     *
     * @return string
     */
    public function default(Tree $tree): string
    {
        $surname_tradition = SurnameTradition::create($tree->getPreference('SURNAME_TRADITION'));

        if ($surname_tradition->hasSurnames()) {
            return '//';
        }

        return '';
    }

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
        return
            '<div class="input-group">' .
            view('edit/input-addon-edit-name', ['id' => $id]) .
            '<input class="form-control" type="text" id="' . e($id) . '" name="' . e($name) . '" value="' . e($value) . '" readonly="readonly" />' .
            view('edit/input-addon-keyboard', ['id' => $id]) .
            view('edit/input-addon-help', ['fact' => 'NAME']) .
            '</div>';
    }

    /**
     * @return array<string,string>
     */
    public function subtags(): array
    {
        $language = I18N::languageTag();

        switch ($language) {
            case 'hu':
            case 'jp':
            case 'ko':
            case 'zh-Hans':
            case 'zh-Hant':
                $subtags = [
                    'TYPE' => '0:1',
                    'NPFX' => '0:1',
                    'SPFX' => '0:1',
                    'SURN' => '0:1',
                    'GIVN' => '0:1',
                    'NSFX' => '0:1',
                    'NICK' => '0:1',
                ];
                break;
            default:
                $subtags = [
                    'TYPE' => '0:1',
                    'NPFX' => '0:1',
                    'GIVN' => '0:1',
                    'SPFX' => '0:1',
                    'SURN' => '0:1',
                    'NSFX' => '0:1',
                    'NICK' => '0:1',
                ];
                break;
        }

        return $subtags;
    }
}
