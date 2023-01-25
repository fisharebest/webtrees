<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees\Http\Exceptions;

use Fig\Http\Message\StatusCodeInterface;

/**
 * Exception thrown when we have some sort of fatal error.
 */
class HttpServerErrorException extends HttpException
{
    /**
     * @param string $message
     */
    public function __construct(string $message)
    {
        parent::__construct($message, StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
    }
}
