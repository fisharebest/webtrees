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

namespace Fisharebest\Webtrees\Elements;

use Fisharebest\Webtrees\Tree;

use function strtoupper;

/**
 * CHARACTER_SET := {Size=1:8}
 * [ ANSEL |UTF-8 | UNICODE | ASCII ]
 * A code value that represents the character set to be used to interpret this
 * data. Currently, the preferred character set is ANSEL, which includes ASCII
 * as a subset. UNICODE is not widely supported by most operating systems;
 * therefore, GEDCOM produced using the UNICODE character set will be limited
 * in its interchangeability for a while but should eventually provide the
 * international flexibility that is desired. See Chapter 3, starting on page
 * 77.
 * Note:The IBMPC character set is not allowed. This character set cannot be
 * interpreted properly without knowing which code page the sender was using.
 */
class CharacterSet extends AbstractElement
{
    /**
     * Convert a value to a canonical form.
     *
     * @param string $value
     *
     * @return string
     */
    public function canonical(string $value): string
    {
        return strtoupper(parent::canonical($value));
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
        return 'UTF-8';
    }

    /**
     * A list of controlled values for this element
     *
     * @return array<int|string,string>
     */
    public function values(): array
    {
        return [
            'UTF-8'   => 'UTF-8',
            'UNICODE' => 'UNICODE',
            'ANSEL'   => 'ANSEL',
            'ASCII'   => 'ASCII',
        ];
    }
}
