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
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Statistics\Repository\Interfaces\BrowserRepositoryInterface;

/**
 * Statistics submodule providing all BROWSER related methods.
 */
class BrowserRepository implements BrowserRepositoryInterface
{
    /**
     * What is the client's date.
     *
     * @return string
     */
    public function browserDate(): string
    {
        return FunctionsDate::timestampToGedcomDate(WT_TIMESTAMP)->display();
    }

    /**
     * What is the client's timestamp.
     *
     * @return string
     */
    public function browserTime(): string
    {
        return date(
            str_replace('%', '', I18N::timeFormat()),
            WT_TIMESTAMP + WT_TIMESTAMP_OFFSET
        );
    }

    /**
     * What is the browser's tiemzone.
     *
     * @return string
     */
    public function browserTimezone(): string
    {
        return date('T', WT_TIMESTAMP + WT_TIMESTAMP_OFFSET);
    }
}
