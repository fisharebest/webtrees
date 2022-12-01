<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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
use Fisharebest\Webtrees\Elements\AddressWebPage;
use Fisharebest\Webtrees\Elements\CustomElement;
use Fisharebest\Webtrees\Elements\EmptyElement;
use Fisharebest\Webtrees\Elements\PafUid;
use Fisharebest\Webtrees\I18N;

/**
 * GEDCOM files created by Roots Magic
 *
 * @see https://www.rootsmagic.com/
 */
class RootsMagic implements CustomTagInterface
{
    /**
     * The name of the application.
     *
     * @return string
     */
    public function name(): string
    {
        return 'RootsMagic';
    }

    /**
     * Tags created by this application.
     *
     * @return array<string,ElementInterface>
     */
    public function tags(): array
    {
        return [
            'FAM:*:SOUR:_TMPLT'              => new EmptyElement(I18N::translate('Template')),
            'FAM:*:SOUR:_TMPLT:FIELD'        => /* I18N: Data entry field */ new EmptyElement(I18N::translate('Field')),
            'FAM:*:SOUR:_TMPLT:FIELD:NAME'   => /* I18N: Data entry field */ new CustomElement(I18N::translate('Field name')),
            'FAM:*:SOUR:_TMPLT:FIELD:VALUE'  => /* I18N: Data entry field */ new CustomElement(I18N::translate('Field value')),
            'FAM:SOUR:_TMPLT'                => new EmptyElement(''),
            'FAM:SOUR:_TMPLT:FIELD'          => new EmptyElement(''),
            'FAM:SOUR:_TMPLT:FIELD:NAME'     => new CustomElement(I18N::translate('Field name')),
            'FAM:SOUR:_TMPLT:FIELD:VALUE'    => new CustomElement(I18N::translate('Field value')),
            'FAM:_UID'                       => new PafUid(I18N::translate('Unique identifier')),
            'INDI:*:SOUR:_TMPLT'             => new EmptyElement(''),
            'INDI:*:SOUR:_TMPLT:FIELD'       => new EmptyElement(''),
            'INDI:*:SOUR:_TMPLT:FIELD:NAME'  => new CustomElement(I18N::translate('Field name')),
            'INDI:*:SOUR:_TMPLT:FIELD:VALUE' => new CustomElement(I18N::translate('Field value')),
            'INDI:SOUR:_TMPLT'               => new EmptyElement(''),
            'INDI:SOUR:_TMPLT:FIELD'         => new EmptyElement(''),
            'INDI:SOUR:_TMPLT:FIELD:NAME'    => new CustomElement(I18N::translate('Field name')),
            'INDI:SOUR:_TMPLT:FIELD:VALUE'   => new CustomElement(I18N::translate('Field value')),
            'INDI:_DNA'                      => new CustomElement(I18N::translate('DNA markers')),
            'INDI:_UID'                      => new PafUid(I18N::translate('Unique identifier')),
            'INDI:_WEBTAG'                   => new CustomElement(I18N::translate('External link')),
            'INDI:_WEBTAG:NAME'              => new CustomElement(I18N::translate('Text')),
            'INDI:_WEBTAG:URL'               => new AddressWebPage(I18N::translate('URL')),
            'OBJE:_UID'                      => new PafUid(I18N::translate('Unique identifier')),
            'REPO:_UID'                      => new PafUid(I18N::translate('Unique identifier')),
            'SOUR:_BIBL'                     => new CustomElement(I18N::translate('Bibliography')),
            'SOUR:_SUBQ'                     => new CustomElement(I18N::translate('Abbreviation')),
            'SOUR:_UID'                      => new PafUid(I18N::translate('Unique identifier')),
        ];
    }
}
