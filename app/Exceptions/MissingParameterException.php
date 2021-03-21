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

namespace Fisharebest\Webtrees\Exceptions;

use Fisharebest\Webtrees\I18N;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Application level exceptions.
 */
class MissingParameterException extends HttpBadRequestException
{
    /**
     * @param ServerRequestInterface $request
     * @param string                 $parameter
     */
    public function __construct(ServerRequestInterface $request, string $parameter)
    {
        $message = I18N::translate('The parameter “%s” is missing.', $parameter);

        $referer = $request->getHeaderLine('Referer');

        if ($referer !== '') {
            $message .= ' ';
            /* I18N: %s is a URL */
            $message .= I18N::translate('This could be caused by an error at %s', $referer);
        }

        parent::__construct($message);
    }
}
