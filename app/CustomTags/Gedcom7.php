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
use Fisharebest\Webtrees\Elements\CountOfChildren;
use Fisharebest\Webtrees\Elements\Creation;
use Fisharebest\Webtrees\Elements\CustomElement;
use Fisharebest\Webtrees\Elements\DateValueExact;
use Fisharebest\Webtrees\Elements\DateValueToday;
use Fisharebest\Webtrees\Elements\EventOrFactClassification;
use Fisharebest\Webtrees\Elements\ExternalIdentifier;
use Fisharebest\Webtrees\Elements\ExternalIdentifierType;
use Fisharebest\Webtrees\Elements\FamilyFact;
use Fisharebest\Webtrees\Elements\LdsInitiatory;
use Fisharebest\Webtrees\Elements\LdsOrdinanceStatus;
use Fisharebest\Webtrees\Elements\NonEvent;
use Fisharebest\Webtrees\Elements\RoleInEvent;
use Fisharebest\Webtrees\Elements\TempleCode;
use Fisharebest\Webtrees\Elements\TimeValue;
use Fisharebest\Webtrees\Elements\TimeValueNow;
use Fisharebest\Webtrees\Elements\Uid;
use Fisharebest\Webtrees\Elements\UserReferenceNumber;
use Fisharebest\Webtrees\Elements\UserReferenceType;
use Fisharebest\Webtrees\Elements\XrefAssociate;
use Fisharebest\Webtrees\Elements\XrefSharedNote;
use Fisharebest\Webtrees\I18N;

/**
 * GEDCOM files created by Gedcom7
 *
 * @see https://gedcom.io
 */
class Gedcom7 implements CustomTagInterface
{
    /**
     * The name of the application.
     *
     * @return string
     */
    public function name(): string
    {
        return 'Gedcom 7';
    }

    /**
     * Tags created by this application.
     *
     * @return array<string,ElementInterface>
     */
    public function tags(): array
    {
        return [
            'FAM:NO' => new NonEvent('Event did not happen'),
            'INDI:NO' => new NonEvent('Event did not happen'),
            'FAM:*:ASSO'                 => new XrefAssociate(I18N::translate('Associate')),
            'FAM:*:ASSO:PHRASE'          => new CustomElement(I18N::translate('Phrase')),
            'FAM:*:ASSO:ROLE'            => new RoleInEvent(I18N::translate('Role')),
            'FAM:*:ASSO:ROLE:PHRASE'     => new CustomElement(I18N::translate('Phrase')),
            'FAM:*:DATE:TIME'            => new TimeValue(I18N::translate('Time')),
            'FAM:*:PLAC:EXID'            => new ExternalIdentifier(I18N::translate('External identifier')),
            'FAM:*:PLAC:EXID:TYPE'       => new ExternalIdentifierType(I18N::translate('Type')),
            'FAM:*:SDATE'                => new CustomElement(I18N::translate('Sort date')),
            'FAM:*:SDATE:PHRASE'         => new CustomElement(I18N::translate('Phrase')),
            'FAM:*:SDATE:TIME'           => new CustomElement(I18N::translate('Sort time')),
            'FAM:*:SNOTE'                => new XrefSharedNote(I18N::translate('Shared note')),
            'FAM:*:UID'                  => new Uid(I18N::translate('Unique identifier')),
            'FAM:ASSO'                   => new XrefAssociate(I18N::translate('Associate')),
            'FAM:ASSO:PHRASE'            => new CustomElement(I18N::translate('Phrase')),
            'FAM:ASSO:ROLE'              => new RoleInEvent(I18N::translate('Role')),
            'FAM:ASSO:ROLE:PHRASE'       => new CustomElement(I18N::translate('Phrase')),
            'FAM:CREA'                   => new Creation(I18N::translate('Created at')),
            'FAM:CREA:DATE'              => new DateValueToday(I18N::translate('Creation date')),
            'FAM:CREA:DATE:TIME'         => new TimeValueNow(I18N::translate('Creation time')),
            'FAM:EXID'                   => new ExternalIdentifier(I18N::translate('External identifier')),
            'FAM:EXID:TYPE'              => new ExternalIdentifierType(I18N::translate('Type')),
            'FAM:FACT'                   => new FamilyFact(I18N::translate('Fact')),
            'FAM:FACT:TYPE'               => new EventOrFactClassification(I18N::translate('Type of fact')),
            'FAM:REFN'                   => new UserReferenceNumber(I18N::translate('Reference number')),
            'FAM:REFN:TYPE'              => new UserReferenceType(I18N::translate('Type')),
            'FAM:SNOTE'                  => new XrefSharedNote(I18N::translate('Shared note')),
            'FAM:UID'                    => new Uid(I18N::translate('Unique identifier')),
            'INDI:*:ASSO'                => new XrefAssociate(I18N::translate('Associate')),
            'INDI:*:ASSO:PHRASE'         => new CustomElement(I18N::translate('Phrase')),
            'INDI:*:ASSO:ROLE'           => new RoleInEvent(I18N::translate('Role')),
            'INDI:*:ASSO:ROLE:PHRASE'    => new CustomElement(I18N::translate('Phrase')),
            'INDI:*:DATE:TIME'           => new TimeValue(I18N::translate('Time')),
            'INDI:*:PLAC:EXID'           => new ExternalIdentifier(I18N::translate('External identifier')),
            'INDI:*:PLAC:EXID:TYPE'      => new ExternalIdentifierType(I18N::translate('Type')),
            'INDI:*:SDATE'               => new CustomElement(I18N::translate('Sort date')),
            'INDI:*:SDATE:PHRASE'        => new CustomElement(I18N::translate('Phrase')),
            'INDI:*:SDATE:TIME'          => new CustomElement(I18N::translate('Sort time')),
            'INDI:*:SNOTE'               => new XrefSharedNote(I18N::translate('Shared note')),
            'INDI:*:UID'                 => new Uid(I18N::translate('Unique identifier')),
            'INDI:ADOP:FAMC:ADOP:PHRASE' => new CustomElement(I18N::translate('Phrase')),
            'INDI:ALIA:PHRASE'           => new CustomElement(I18N::translate('Phrase')),
            'INDI:ASSO'                  => new XrefAssociate(I18N::translate('Associate')),
            'INDI:ASSO:PHRASE'           => new CustomElement(I18N::translate('Phrase')),
            'INDI:ASSO:ROLE'             => new RoleInEvent(I18N::translate('Role')),
            'INDI:ASSO:ROLE:PHRASE'      => new CustomElement(I18N::translate('Phrase')),
            'INDI:BIRT:DATE:TIME'        => new TimeValue(I18N::translate('Time of birth')),
            'INDI:CREA'                  => new Creation(I18N::translate('Created at')),
            'INDI:CREA:DATE'             => new DateValueToday(I18N::translate('Creation date')),
            'INDI:CREA:DATE:TIME'        => new TimeValueNow(I18N::translate('Creation time')),
            'INDI:DEAT:DATE:TIME'        => new TimeValue(I18N::translate('Time of death')),
            'INDI:EXID'                  => new ExternalIdentifier(I18N::translate('External identifier')),
            'INDI:EXID:TYPE'             => new ExternalIdentifierType(I18N::translate('Type')),
            'INDI:INIL'                  => /* I18N: GEDCOM tag INIL - an LDS ceremony */ new LdsInitiatory(I18N::translate('LDS initiatory')),
            'INDI:INIL:STAT'             => new LdsOrdinanceStatus(I18N::translate('Status')),
            'INDI:INIL:STAT:DATE'        => new DateValueExact(I18N::translate('Date of status change')),
            'INDI:INIL:STAT:DATE:TIME'   => new TimeValue(I18N::translate('Time of status change')),
            'INDI:INIL:TEMP'             => new TempleCode(I18N::translate('Temple')),
            'INDI:REFN'                  => new UserReferenceNumber(I18N::translate('Reference number')),
            'INDI:REFN:TYPE'             => new UserReferenceType(I18N::translate('Type')),
            'INDI:SNOTE'                 => new XrefSharedNote(I18N::translate('Shared note')),
            'INDI:UID'                   => new Uid(I18N::translate('Unique identifier')),
            'OBJE:CREA'                  => new Creation(I18N::translate('Created at')),
            'OBJE:CREA:DATE'             => new DateValueToday(I18N::translate('Creation date')),
            'OBJE:CREA:DATE:TIME'        => new TimeValueNow(I18N::translate('Creation time')),
            'OBJE:SNOTE'                 => new XrefSharedNote(I18N::translate('Shared note')),
            'REPO:CREA'                  => new Creation(I18N::translate('Created at')),
            'REPO:CREA:DATE'             => new DateValueToday(I18N::translate('Creation date')),
            'REPO:CREA:DATE:TIME'        => new TimeValueNow(I18N::translate('Creation time')),
            'REPO:EXID'                  => new ExternalIdentifier(I18N::translate('External identifier')),
            'REPO:EXID:TYPE'             => new ExternalIdentifierType(I18N::translate('Type')),
            'REPO:REFN'                  => new UserReferenceNumber(I18N::translate('Reference number')),
            'REPO:REFN:TYPE'             => new UserReferenceType(I18N::translate('Type')),
            'REPO:SNOTE'                 => new XrefSharedNote(I18N::translate('Shared note')),
            'REPO:UID'                   => new Uid(I18N::translate('Unique identifier')),
            'SNOTE:CREA'                 => new Creation(I18N::translate('Created at')),
            'SNOTE:CREA:DATE'            => new DateValueToday(I18N::translate('Creation date')),
            'SNOTE:CREA:DATE:TIME'       => new TimeValueNow(I18N::translate('Creation time')),
            'SNOTE:EXID'                 => new ExternalIdentifier(I18N::translate('External identifier')),
            'SNOTE:EXID:TYPE'            => new ExternalIdentifierType(I18N::translate('Type')),
            'SNOTE:REFN'                 => new UserReferenceNumber(I18N::translate('Reference number')),
            'SNOTE:REFN:TYPE'            => new UserReferenceType(I18N::translate('Type')),
            'SNOTE:UID'                  => new Uid(I18N::translate('Unique identifier')),
            'SOUR:CREA'                  => new Creation(I18N::translate('Created at')),
            'SOUR:CREA:DATE'             => new DateValueToday(I18N::translate('Creation date')),
            'SOUR:CREA:DATE:TIME'        => new TimeValueNow(I18N::translate('Creation time')),
            'SOUR:EXID'                  => new ExternalIdentifier(I18N::translate('External identifier')),
            'SOUR:EXID:TYPE'             => new ExternalIdentifierType(I18N::translate('Type')),
            'SOUR:REFN'                  => new UserReferenceNumber(I18N::translate('Reference number')),
            'SOUR:REFN:TYPE'             => new UserReferenceType(I18N::translate('Type')),
            'SOUR:SNOTE'                 => new XrefSharedNote(I18N::translate('Shared note')),
            'SOUR:UID'                   => new Uid(I18N::translate('Unique identifier')),
            'SUBM:CREA'                  => new Creation(I18N::translate('Created at')),
            'SUBM:CREA:DATE'             => new DateValueToday(I18N::translate('Creation date')),
            'SUBM:CREA:DATE:TIME'        => new TimeValueNow(I18N::translate('Creation time')),
            'SUBM:EXID'                  => new ExternalIdentifier(I18N::translate('External identifier')),
            'SUBM:EXID:TYPE'             => new ExternalIdentifierType(I18N::translate('Type')),
            'SUBM:REFN'                  => new UserReferenceNumber(I18N::translate('Reference number')),
            'SUBM:REFN:TYPE'             => new UserReferenceType(I18N::translate('Type')),
            'SUBM:SNOTE'                 => new XrefSharedNote(I18N::translate('Shared note')),
            'SUBM:UID'                   => new Uid(I18N::translate('Unique identifier')),
        ];
    }
}
