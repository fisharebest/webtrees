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

namespace Fisharebest\Webtrees\Elements;

use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Services\LocalizationService;
use Fisharebest\Webtrees\Tree;

use function app;
use function e;
use function preg_replace_callback;
use function view;

/**
 * DATE_VALUE := {Size=1:35}
 */
class DateValueExact extends DateValue
{
    protected const PATTERN = '(0[1-9]|[12][0-9]|3[0-1]) (JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC) [0-9][0-9][0-9][0-9]';
}
