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

namespace Fisharebest\Webtrees\CustomTags;

use Fisharebest\Webtrees\Contracts\CustomTagInterface;
use Fisharebest\Webtrees\Contracts\ElementInterface;
use Fisharebest\Webtrees\Elements\AddressCity;
use Fisharebest\Webtrees\Elements\AddressCountry;
use Fisharebest\Webtrees\Elements\AddressEmail;
use Fisharebest\Webtrees\Elements\AddressFax;
use Fisharebest\Webtrees\Elements\AddressLine;
use Fisharebest\Webtrees\Elements\AddressLine1;
use Fisharebest\Webtrees\Elements\AddressLine2;
use Fisharebest\Webtrees\Elements\AddressPostalCode;
use Fisharebest\Webtrees\Elements\AddressState;
use Fisharebest\Webtrees\Elements\CustomElement;
use Fisharebest\Webtrees\Elements\NamePersonal;
use Fisharebest\Webtrees\Elements\PafUid;
use Fisharebest\Webtrees\Elements\PhoneNumber;
use Fisharebest\Webtrees\I18N;

/**
 * GEDCOM files created by Personal Ancestral File
 */
class PersonalAncestralFile implements CustomTagInterface
{
    /**
     * The name of the application.
     *
     * @return string
     */
    public function name(): string
    {
        return 'Personal Ancestral File';
    }

    /**
     * Tags created by this application.
     *
     * @return array<string,ElementInterface>
     */
    public function tags(): array
    {
        return [
            'FAM:_UID'        => new PafUid(I18N::translate('Unique identifier')),
            'INDI:NAME:_ADPN' => new NamePersonal(I18N::translate('Adopted name'), []),
            'INDI:NAME:_AKA'  => new NamePersonal(I18N::translate('Also known as'), []),
            'INDI:NAME:_AKAN' => new NamePersonal(I18N::translate('Also known as'), []),
            'INDI:ADDR'       => new AddressLine(I18N::translate('Address')),
            'INDI:ADDR:ADR1'  => new AddressLine1(I18N::translate('Address line 1')),
            'INDI:ADDR:ADR2'  => new AddressLine2(I18N::translate('Address line 2')),
            'INDI:ADDR:CITY'  => new AddressCity(I18N::translate('City')),
            'INDI:ADDR:CTRY'  => new AddressCountry(I18N::translate('Country')),
            'INDI:ADDR:POST'  => new AddressPostalCode(I18N::translate('Postal code')),
            'INDI:ADDR:STAE'  => new AddressState(I18N::translate('State')),
            'INDI:ADDR:_NAME' => new CustomElement(I18N::translate('Name of addressee')),
            'INDI:EMAIL'      => new AddressEmail(I18N::translate('Email address')),
            'INDI:FAX'        => new AddressFax(I18N::translate('Fax')),
            'INDI:PHON'       => new PhoneNumber(I18N::translate('Phone')),
            'INDI:URL'        => new CustomElement(I18N::translate('URL')),
            'INDI:_UID'       => new PafUid(I18N::translate('Unique identifier')),
            'OBJE:_UID'       => new PafUid(I18N::translate('Unique identifier')),
            'REPO:_UID'       => new PafUid(I18N::translate('Unique identifier')),
            'SOUR:_UID'       => new PafUid(I18N::translate('Unique identifier')),
        ];
    }
}
