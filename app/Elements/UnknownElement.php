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

/**
 * An unrecognised GEDCOM element.
 */
class UnknownElement extends AbstractElement
{
    /**
     * Name for this GEDCOM primitive.
     *
     * @return string
     */
    public function label(): string
    {
        $title = I18N::translate('Unrecognized GEDCOM code');

        return '<span class="error" title="' . $title . '">' . parent::label() . '</span>';
    }
}
