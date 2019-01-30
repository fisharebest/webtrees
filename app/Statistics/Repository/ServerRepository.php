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
 * A repository providing methods for server related statistics.
 */
class ServerRepository implements ServerRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function serverDate(): string
    {
        // TODO: Duplicates BrowserRepository::browserDate
        return FunctionsDate::timestampToGedcomDate(WT_TIMESTAMP)->display();
    }

    /**
     * @inheritDoc
     */
    public function serverTime(): string
    {
        return date('g:i a');
    }

    /**
     * @inheritDoc
     */
    public function serverTime24(): string
    {
        return date('G:i');
    }

    /**
     * @inheritDoc
     */
    public function serverTimezone(): string
    {
        return date('T');
    }
}
