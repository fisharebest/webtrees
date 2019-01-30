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

namespace Fisharebest\Webtrees\Statistics\Helper;

use Fisharebest\Webtrees\I18N;
use NumberFormatter;

/**
 *
 */
class Century
{
    /**
     * Century name, English => 21st, Polish => XXI, etc.
     *
     * @param int $century
     *
     * @return string
     */
    public function centuryName(int $century): string
    {
        if ($century < 0) {
            return I18N::translate('%s BCE', $this->centuryName(-$century));
        }

        $nf     = new NumberFormatter('en_US', NumberFormatter::ORDINAL);
        $suffix = $nf->format($century);

        return strip_tags(I18N::translateContext('CENTURY', $suffix));
    }
}
