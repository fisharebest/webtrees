<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees\Factories;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\TimestampFactoryInterface;
use Fisharebest\Webtrees\Contracts\TimestampInterface;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Timestamp;
use LogicException;

use function date;
use function date_create_from_format;
use function time;

/**
 * Create a timestamp object.
 */
class TimestampFactory implements TimestampFactoryInterface
{
    /**
     * @param int                $timestamp
     * @param UserInterface|null $user
     *
     * @return TimestampInterface
     */
    public function make(int $timestamp, UserInterface $user = null): TimestampInterface
    {
        $user     ??= Auth::user();
        $timezone = $user->getPreference(UserInterface::PREF_TIME_ZONE, Site::getPreference('TIMEZONE'));
        $locale   = I18N::locale()->code();

        return new Timestamp($timestamp, $timezone, $locale);
    }

    /**
     * @param string|null        $string YYYY-MM-DD HH:MM:SS (as provided by SQL).
     * @param string             $format
     * @param UserInterface|null $user
     *
     * @return TimestampInterface
     */
    public function fromString(?string $string, string $format = 'Y-m-d H:i:s', UserInterface $user = null): TimestampInterface
    {
        $string    ??= date($format);
        $timestamp = date_create_from_format($format, $string);

        if ($timestamp === false) {
            throw new LogicException('date/time "' . $string . '" does not match pattern "' . $format . '"');
        }

        return $this->make($timestamp->getTimestamp(), $user);
    }

    /**
     * @param UserInterface|null $user
     *
     * @return TimestampInterface
     */
    public function now(UserInterface $user = null): TimestampInterface
    {
        return $this->make(time(), $user);
    }
}
