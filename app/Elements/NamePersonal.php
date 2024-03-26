<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;

use function e;
use function in_array;
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
 * William /Lee/ Parry (surname embedded in the name string)
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
        'NOTE' => '0:M',
        'SOUR' => '0:M',
        'FONE' => '0:M',
        'ROMN' => '0:M',
    ];

    // For some languages, we want to show the surname field first.
    protected const SURNAME_FIRST_LANGUAGES = ['hu', 'jp', 'ko', 'zh-Hans', 'zh-Hant'];

    protected const SUBTAGS_SURNAME_FIRST = [
        'TYPE' => '0:1',
        'NPFX' => '0:1',
        'SPFX' => '0:1',
        'SURN' => '0:1',
        'GIVN' => '0:1',
        'NSFX' => '0:1',
        'NICK' => '0:1',
        'NOTE' => '0:M',
        'SOUR' => '0:M',
        'FONE' => '0:M',
        'ROMN' => '0:M',
    ];

    /**
     * @param string             $label
     * @param array<string>|null $subtags
     */
    public function __construct(string $label, ?array $subtags = null)
    {
        if ($subtags === null && in_array(I18N::languageTag(), static::SURNAME_FIRST_LANGUAGES, true)) {
            $subtags = static::SUBTAGS_SURNAME_FIRST;
        }
        parent::__construct($label, $subtags);
    }

    /**
     * Convert a value to a canonical form.
     *
     * @param string $value
     *
     * @return string
     */
    public function canonical(string $value): string
    {
        $value = parent::canonical($value);

        if ($value === '//') {
            return '';
        }

        return $value;
    }


    /**
     * Create a default value for this element.
     *
     * @param Tree $tree
     *
     * @return string
     */
    public function default(Tree $tree): string
    {
        return Registry::surnameTraditionFactory()
            ->make($tree->getPreference('SURNAME_TRADITION'))
            ->defaultName();
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
            '<input class="form-control" type="text" id="' . e($id) . '-disabled" name="' . e($name) . '" value="' . e($value) . '" readonly="readonly" disabled="disabled" />' .
            '<input class="form-control d-none" type="text" id="' . e($id) . '" name="' . e($name) . '" value="' . e($value) . '" />' .
            view('edit/input-addon-keyboard', ['id' => $id]) .
            view('edit/input-addon-help', ['topic' => 'NAME']) .
            '</div>';
    }
}
