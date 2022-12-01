<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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

namespace Fisharebest\Webtrees\Exceptions;

use Exception;
use Fisharebest\Webtrees\I18N;

use function e;

/**
 * Exception thrown when importing invalid GEDCOM data.
 */
class InvalidGedcomEncodingException extends Exception
{
    /**
     * InvalidGedcomEncodingException constructor.
     *
     * @param string $charset
     */
    public function __construct(string $charset)
    {
        $message = I18N::translate('Error: converting GEDCOM files from %s encoding to UTF-8 encoding not currently supported.', e($charset));

        parent::__construct($message);
    }
}
