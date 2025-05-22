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

/**
 * NATIONAL_ID_NUMBER := {Size=1:30}
 * A nationally-controlled number assigned to an individual. Commonly known
 * national numbers should be assigned their own tag, such as SSN for U.S.
 * Social Security Number. The use of the IDNO tag requires a subordinate TYPE
 * tag to identify what kind of number is being stored.
 * For example:
 * n IDNO 43-456-1899
 * +1 TYPE Canadian Health Registration
 */
class NationalIdNumber extends AbstractElement
{
    protected const int MAXIMUM_LENGTH = 30;

    protected const array SUBTAGS = [
        'TYPE'  => '0:1',
        'DATE'  => '0:1',
        'PLAC'  => '0:1',
        'ADDR'  => '0:1:?',
        'EMAIL' => '0:1:?',
        'WWW'   => '0:1:?',
        'PHON'  => '0:1:?',
        'FAX'   => '0:1:?',
        'CAUS'  => '0:1:?',
        'AGNC'  => '0:1',
        'RELI'  => '0:1:?',
        'NOTE'  => '0:M',
        'OBJE'  => '0:M',
        'SOUR'  => '0:M',
        'RESN'  => '0:1',
    ];
}
