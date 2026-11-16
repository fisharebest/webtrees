<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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
use function preg_match;

/**
 * UID fields
 */
class Uid extends AbstractElement
{
    protected const int MAXIMUM_LENGTH = 36;

    public function canonical(string $value): string
    {
        // The GEDCOM spec says:
        // "it should be used without modification, not even whitespace or letter case normalization."
        return $value;
    }

    public function edit(string $id, string $name, string $value, Tree $tree): string
    {
        return
            '<div class="input-group mb-3">' .
            parent::edit($id, $name, $value, $tree) .
            '<button type="button" class="input-group-text btn btn-primary" data-wt-generated-value-target="' . e($id) . '" data-wt-generated-value="' . e(Registry::idFactory()->uuid()) . '">' .
            I18N::translate('create') .
            '</button>' .
            '</div>';
    }
}
