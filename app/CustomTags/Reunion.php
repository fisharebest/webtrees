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
use Fisharebest\Webtrees\Elements\AddressEmail;
use Fisharebest\Webtrees\Elements\CustomElement;
use Fisharebest\Webtrees\Elements\PafUid;
use Fisharebest\Webtrees\I18N;

/**
 * GEDCOM files created by Reunion
 *
 * @see https://www.leisterpro.com
 */
class Reunion implements CustomTagInterface
{
    /**
     * The name of the application.
     *
     * @return string
     */
    public function name(): string
    {
        return 'Reunion';
    }

    /**
     * Tags created by this application.
     *
     * @return array<string,ElementInterface>
     */
    public function tags(): array
    {
        return [
            'FAM:_UID'   => new PafUid(I18N::translate('Unique identifier')),
            'INDI:CITN'  => new CustomElement(I18N::translate('Citizenship')),
            'INDI:EMAL'  => new AddressEmail(I18N::translate('Email address')),
            'INDI:_LEGA' => new CustomElement(I18N::translate('Legatee')),
            'INDI:_MDCL' => new CustomElement(I18N::translate('Medical')),
            'INDI:_PURC' => /* I18N: GEDCOM tag _PURC */ new CustomElement(I18N::translate('Land purchase')),
            'INDI:_SALE' => /* I18N: GEDCOM tag _SALE */ new CustomElement(I18N::translate('Land sale')),
            'INDI:_UID'  => new PafUid(I18N::translate('Unique identifier')),
            'OBJE:_UID'  => new PafUid(I18N::translate('Unique identifier')),
            'REPO:_UID'  => new PafUid(I18N::translate('Unique identifier')),
            'SOUR:_UID'  => new PafUid(I18N::translate('Unique identifier')),
        ];
    }
}
