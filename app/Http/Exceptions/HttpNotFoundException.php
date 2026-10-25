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

namespace Fisharebest\Webtrees\Http\Exceptions;

use Fisharebest\Webtrees\Enums\HttpStatusCode;
use Fisharebest\Webtrees\Http\Controllers\HomePage;
use Fisharebest\Webtrees\I18N;

use function e;
use function route;

class HttpNotFoundException extends HttpException
{
    public function __construct(string|null $message = null)
    {
        $message ??=
            I18N::translate('You do not have permission to view this page.') .
            '<br><br>' .
            '<a href="' . e(route(HomePage::class)) . '" class="alert-link">' .
            I18N::translate('Home page') .
            '</a>';

        parent::__construct($message, HttpStatusCode::NotFound);
    }
}
