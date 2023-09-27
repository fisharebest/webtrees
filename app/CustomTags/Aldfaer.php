<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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
use Fisharebest\Webtrees\Elements\AddressFax;
use Fisharebest\Webtrees\Elements\AddressLine;
use Fisharebest\Webtrees\Elements\AddressWebPage;
use Fisharebest\Webtrees\Elements\CustomElement;
use Fisharebest\Webtrees\Elements\DateValue;
use Fisharebest\Webtrees\Elements\EmptyElement;
use Fisharebest\Webtrees\Elements\Marriage;
use Fisharebest\Webtrees\Elements\PhoneNumber;
use Fisharebest\Webtrees\Elements\TimeValue;
use Fisharebest\Webtrees\I18N;

/**
 * GEDCOM files created by Aldfaer
 *
 * @see http://aldfaer.net
 */
class Aldfaer implements CustomTagInterface
{
    /**
     * The name of the application.
     *
     * @return string
     */
    public function name(): string
    {
        return 'Aldfaer';
    }

    /**
     * Tags created by this application.
     *
     * @return array<string,ElementInterface>
     */
    public function tags(): array
    {
        return [
            'FAM:MARR_CIVIL'           => new Marriage(I18N::translate('Civil marriage')),
            'FAM:MARR_PARTNERS'        => new Marriage(I18N::translate('Registered partnership')),
            'FAM:MARR_RELIGIOUS'       => new Marriage(I18N::translate('Religious marriage')),
            'FAM:MARR_UNKNOWN'         => new Marriage(I18N::translate('Marriage')),
            'FAM:_ALDFAER_NOREL'       => new EmptyElement('No relation'), // What is this?
            'HEAD:SUBM:ADDR'           => new AddressLine(I18N::translate('Address')),
            'HEAD:SUBM:PHON'           => new PhoneNumber(I18N::translate('Phone')),
            'HEAD:SUBM:_EMAI'          => new AddressEmail(I18N::translate('Email')),
            'HEAD:SUBM:_FAX'           => new AddressFax(I18N::translate('Fax')),
            'HEAD:SUBM:_WWW'           => new AddressWebPage(I18N::translate('URL')),
            'INDI:BIRT:_ALDFAER_TIME'  => new TimeValue(I18N::translate('Time of birth')),
            'INDI:BIRT:_LENGTH'        => new CustomElement(I18N::translate('Length')),
            'INDI:BIRT:_WEIGHT'        => new CustomElement(I18N::translate('Weight')),
            'INDI:DEAT:_ALDFAER_TIME'  => new TimeValue(I18N::translate('Time of death')),
            'INDI:_REFERENCE'          => new CustomElement(''),
            'INDI:_PRIVACY'            => new CustomElement(''),
            'INDI:_PRIVACY:_OBJECTION' => new CustomElement(''),
            'INDI:_PRIVACY:_PUBLISH'   => new CustomElement(''),
            'INDI:NAME:_SURNAS'        => new CustomElement(I18N::translate('Alternative spelling of surname')),
            'INDI:DEAT:_DATE'          => new DateValue(I18N::translate('Date')),
            'INDI:_INQUBIRT'           => new CustomElement(''),
            'INDI:_INQUCHIL'           => new CustomElement(''),
            'INDI:_INQURELA'           => new CustomElement(''),
            'INDI:_INQUDEAT'           => new CustomElement(''),
            'INDI:_INQUVAR1'           => new CustomElement(''),
            'INDI:_INQUVAR1CAT'        => new CustomElement(''),
            'INDI:_INQUVAR2'           => new CustomElement(''),
            'INDI:_INQUVAR2CAT'        => new CustomElement(''),
            'INDI:_INQUVAR3'           => new CustomElement(''),
            'INDI:_INQUVAR3CAT'        => new CustomElement(''),
            'INDI:_NOPARTNER'          => new CustomElement(''),
            'INDI:_NEW'                => new CustomElement(''),
            'INDI:_BOLD'               => new CustomElement(''),
            'INDI:_ITALIC'             => new CustomElement(''),
            'INDI:_UNDERLINE'          => new CustomElement(''),
            'INDI:_COLOR'              => new CustomElement(''),
        ];
    }
}
