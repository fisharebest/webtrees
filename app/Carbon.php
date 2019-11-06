<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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

namespace Fisharebest\Webtrees;

use Carbon\CarbonImmutable;
use Fisharebest\Localization\Locale\LocaleInterface;
use Psr\Http\Message\ServerRequestInterface;

use function app;
use function gregoriantojd;
use function unixtojd;

/**
 * A wrapper around CarbonImmutable dates.
 *
 * The future of nesbot/carbon seems uncertain.  We can always swap it out
 * for carbondate/carbon, cake-php/chronos or some other datetime class.
 */
class Carbon extends CarbonImmutable
{
    /**
     * We use julian days extensively, so a helper function to convert.
     */
    public function julianDay(): int
    {
        return gregoriantojd($this->month, $this->day, $this->year);
    }

    /**
     * Create a local timestamp for the current user.
     *
     * @return Carbon
     */
    public function local(): Carbon
    {
        $locale = app(ServerRequestInterface::class)->getAttribute('locale');
        assert($locale instanceof LocaleInterface);

        $timezone = Auth::user()->getPreference('TIMEZONE', Site::getPreference('TIMEZONE', 'UTC'));

        return $this->locale($locale->code())->setTimezone($timezone);
    }
}
