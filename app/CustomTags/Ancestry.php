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
use Fisharebest\Webtrees\Elements\AddressWebPage;
use Fisharebest\Webtrees\Elements\AutomatedRecordId;
use Fisharebest\Webtrees\Elements\CustomElement;
use Fisharebest\Webtrees\Elements\CustomEvent;
use Fisharebest\Webtrees\Elements\CustomFact;
use Fisharebest\Webtrees\Elements\DateValue;
use Fisharebest\Webtrees\Elements\LdsInitiatory;
use Fisharebest\Webtrees\Elements\PlaceName;
use Fisharebest\Webtrees\Elements\SubmitterText;
use Fisharebest\Webtrees\I18N;

/**
 * GEDCOM files created by Ancestry
 *
 * @see https://www.ancestry.com/
 * @see https://www.webtrees.net/index.php/en/forum/help-for-release-2-1-x/36664-2-1-beta-support-for-indi-even-sour-data-note-and-the-like
 */
class Ancestry implements CustomTagInterface
{
    /**
     * The name of the application.
     *
     * @return string
     */
    public function name(): string
    {
        return 'Ancestry';
    }

    /**
     * Tags created by this application.
     *
     * @return array<string,ElementInterface>
     */
    public function tags(): array
    {
        return [
            'HEAD:SOUR:_TREE'       => new CustomElement(I18N::translate('Family tree')),
            'HEAD:SOUR:_TREE:NOTE'  => new SubmitterText(I18N::translate('Note')),
            'HEAD:SOUR:_TREE:RIN'   => new AutomatedRecordId(I18N::translate('Record ID number')),
            'INDI:*:SOUR:_APID'     => /* I18N: GEDCOM tag _APID */ new CustomElement(I18N::translate('Ancestry PID')),
            'INDI:*:SOUR:DATA:NOTE' => new SubmitterText(I18N::translate('Note')),
            'INDI:_EMPLOY'          => new CustomFact(I18N::translate('Occupation')),
            'INDI:_FUN'             => new CustomEvent(I18N::translate('Funeral')),
            'INDI:_INIT'            => new LdsInitiatory(I18N::translate('LDS initiatory')),
            'INDI:_ORDI'            => new CustomEvent(I18N::translate('Ordination')),
            'INDI:_ORIG'            => new CustomFact(I18N::translate('Origin')),
            'INDI:_DEST'            => new CustomFact(I18N::translate('Destination')),
            'OBJE:DATE'             => new DateValue(I18N::translate('Date')),
            'OBJE:PLAC'             => new PlaceName(I18N::translate('Place')),
            'OBJE:_CREA'            => /* I18N: GEDCOM tag _CREA */ new CustomElement(I18N::translate('Created at')),
            'OBJE:_ORIG'            => /* I18N: GEDCOM tag _ORIG */ new CustomElement(I18N::translate('Original text')),
            'OBJE:_ORIG:_URL'       => new AddressWebPage(I18N::translate('URL')),
        ];
    }
}
