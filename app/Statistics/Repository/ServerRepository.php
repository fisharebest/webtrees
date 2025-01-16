<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

namespace Fisharebest\Webtrees\Statistics\Repository;

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\SiteUser;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\ServerRepositoryInterface;

/**
 * A repository providing methods for server related statistics.
 */
class ServerRepository implements ServerRepositoryInterface
{
    public function serverDate(): string
    {
        $format = strtr(I18N::dateFormat(), ['%' => '']);

        return Registry::timestampFactory()->now(new SiteUser())->format($format);
    }

    public function serverTime(): string
    {
        $format = strtr(I18N::timeFormat(), ['%' => '']);

        return Registry::timestampFactory()->now(new SiteUser())->format($format);
    }

    public function serverTime24(): string
    {
        return Registry::timestampFactory()->now(new SiteUser())->format('G:i');
    }

    public function serverTimezone(): string
    {
        return Registry::timestampFactory()->now(new SiteUser())->format('T');
    }
}
