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

/**
 * Exception thrown when a media cannot be accessed due to privacy rules.
 */
class MediaAccessDeniedException extends HttpAccessDeniedException
{
    /**
     * MediaNotFoundException constructor.
     */
    public function __construct()
    {
        parent::__construct(I18N::translate(
            'This media object does not exist or you do not have permission to view it.'
        ));
    }
}
