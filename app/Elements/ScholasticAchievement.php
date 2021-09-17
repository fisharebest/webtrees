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

namespace Fisharebest\Webtrees\Elements;

/**
 * SCHOLASTIC_ACHIEVEMENT := {Size=1:248}
 * A description of a scholastic or educational achievement or pursuit.
 */
class ScholasticAchievement extends AbstractEventElement
{
    protected const SUBTAGS = [
        'DATE' => '0:1',
        'PLAC' => '0:1',
        'ADDR' => '0:1',
        'AGNC' => '0:1',
        'NOTE' => '0:M',
        'OBJE' => '0:M',
        'SOUR' => '0:M',
        'RESN' => '0:1',
    ];
}
