<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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

use Fisharebest\Webtrees\Functions\FunctionsDate;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\ServerRepositoryInterface;

/**
 * Statistics submodule providing all SERVER related methods.
 */
class ServerRepository implements ServerRepositoryInterface
{
    /**
     * What is the current date on the server?
     *
     * @return string
     */
    public function serverDate(): string
    {
        return FunctionsDate::timestampToGedcomDate(WT_TIMESTAMP)->display();
    }

    /**
     * What is the current time on the server (in 12 hour clock)?
     *
     * @return string
     */
    public function serverTime(): string
    {
        return date('g:i a');
    }

    /**
     * What is the current time on the server (in 24 hour clock)?
     *
     * @return string
     */
    public function serverTime24(): string
    {
        return date('G:i');
    }

    /**
     * What is the timezone of the server.
     *
     * @return string
     */
    public function serverTimezone(): string
    {
        return date('T');
    }
}
