<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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

namespace Fisharebest\Webtrees\Statistics\Repository;

use Fisharebest\Webtrees\Carbon;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\ServerRepositoryInterface;

/**
 * A repository providing methods for server related statistics.
 */
class ServerRepository implements ServerRepositoryInterface
{
    /**
     * @return string
     */
    public function serverDate(): string
    {
        $format   = strtr(I18N::dateFormat(), ['%' => '']);
        $timezone = Site::getPreference('TIMEZONE', 'UTC');

        return Carbon::now()->local()->timezone($timezone)->format($format);
    }

    /**
     * @return string
     */
    public function serverTime(): string
    {
        $format   = strtr(I18N::timeFormat(), ['%' => '']);
        $timezone = Site::getPreference('TIMEZONE', 'UTC');

        return Carbon::now()->local()->timezone($timezone)->format($format);
    }

    /**
     * @return string
     */
    public function serverTime24(): string
    {
        $timezone = Site::getPreference('TIMEZONE', 'UTC');

        return Carbon::now()->local()->timezone($timezone)->format('G:i');
    }

    /**
     * @return string
     */
    public function serverTimezone(): string
    {
        $timezone = Site::getPreference('TIMEZONE', 'UTC');

        return Carbon::now()->local()->timezone($timezone)->format('T');
    }
}
