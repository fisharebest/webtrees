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

namespace Fisharebest\Webtrees\Enums;

/**
 * HTTP status codes.
 *
 * @see https://www.iana.org/assignments/http-status-codes
 */
enum HttpStatusCode: int
{
    case OK                  = 200;
    case NoContent           = 204;
    case MovedPermanently    = 301;
    case Found               = 302;
    case TemporaryRedirect   = 307;
    case PermanentRedirect   = 308;
    case BadRequest          = 400;
    case Forbidden           = 403;
    case NotFound            = 404;
    case MethodNotAllowed    = 405;
    case NotAcceptable       = 406;
    case Gone                = 410;
    case TooManyRequests     = 429;
    case InternalServerError = 500;
    case ServiceUnavailable  = 503;
}
