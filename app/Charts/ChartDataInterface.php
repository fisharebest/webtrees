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

namespace Fisharebest\Webtrees\Charts;

use JsonSerializable;

interface ChartDataInterface extends JsonSerializable
{
    public const string COLOR_WHITE = '#ffffff';

    public const string COLOR_DEFAULT = '#84beff';

    public const string COLOR_MALE = '#84beff';

    public const string COLOR_FEMALE = '#ffd1dc';

    public const string COLOR_UNKNOWN_SEX = '#777777';
    public const string COLOR_OTHER_SEX = '#ffb347';

    public const string COLOR_CHART_RED = '#ff0000';

    public const string COLOR_LIVING = self::COLOR_DEFAULT;
    public const string COLOR_DEAD = self::COLOR_EMPTY;

    public const string COLOR_EMPTY = '#777777';

    public const array COLOR_PALETTE = [
        '#8FA8C9', '#D7B892', '#CC9A96', '#9CBFC0',
        '#9BBE95', '#D8C489', '#BBA3C7', '#D6AAB2',
        '#B7A091', '#B8B8B1', '#95ADD9', '#9ECFBE',
        '#9BA8BC', '#D9CC97', '#A8A1D6', '#9EC6DA',
    ];

    public function hasData(): bool;
}
