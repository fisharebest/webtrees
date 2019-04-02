<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace Fisharebest\Webtrees\Exceptions;

use Fig\Http\Message\StatusCodeInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Exception thrown when we can't easily display an error, such as AJAX requests.
 */
class InternalServerErrorException extends HttpException
{
    /**
     * InternalServerErrorException constructor.
     *
     * @param string $message
     */
    public function __construct(string $message)
    {
        parent::__construct(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR, $message);
    }
}
