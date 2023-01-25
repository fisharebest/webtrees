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

namespace Fisharebest\Webtrees\CustomTags;

use Fisharebest\Webtrees\Contracts\CustomTagInterface;
use Fisharebest\Webtrees\Contracts\ElementInterface;
use Fisharebest\Webtrees\Elements\TimeValue;
use Fisharebest\Webtrees\Elements\UserReferenceNumber;
use Fisharebest\Webtrees\I18N;

/**
 * GEDCOM files created by Pro-Gen
 *
 * @see https://www.pro-gen.nl
 */
class ProGen implements CustomTagInterface
{
    /**
     * The name of the application.
     *
     * @return string
     */
    public function name(): string
    {
        return 'Pro-Gen';
    }

    /**
     * Tags created by this application.
     *
     * @return array<string,ElementInterface>
     */
    public function tags(): array
    {
        return [
            'FAM:*:SOUR:REFN'  => new UserReferenceNumber(I18N::translate('Reference number')),
            'FAM:RFN'          => new UserReferenceNumber(I18N::translate('Reference number')),
            'INDI:*:SOUR:REFN' => new UserReferenceNumber(I18N::translate('Reference number')),
            'INDI:BIRT:TIME'   => new TimeValue(I18N::translate('Time of birth')),
        ];
    }
}
