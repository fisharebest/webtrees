<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Contracts\ElementInterface;
use Fisharebest\Webtrees\Elements\AddressPostalCode;
use Fisharebest\Webtrees\Elements\CustomElement;
use Fisharebest\Webtrees\Elements\DateValue;
use Fisharebest\Webtrees\Elements\GovIdentifier;
use Fisharebest\Webtrees\Elements\MaidenheadLocator;
use Fisharebest\Webtrees\Elements\PafUid;
use Fisharebest\Webtrees\Elements\SourceFiledByEntry;
use Fisharebest\Webtrees\Elements\TextFromSource;
use Fisharebest\Webtrees\Elements\XrefAssociate;
use Fisharebest\Webtrees\I18N;

/**
 * Class CustomTagsGenPluswin - Support for GEDCOM files created by https://www.genpluswin.de
 */
class CustomTagsGenPluswin extends AbstractModule implements ModuleConfigInterface, ModuleCustomTagsInterface
{
    use ModuleConfigTrait;
    use ModuleCustomTagsTrait;

    /**
     * Should this module be enabled when it is first installed?
     *
     * @return bool
     */
    public function isEnabledByDefault(): bool
    {
        return false;
    }

    /**
     * @return array<string,ElementInterface>
     */
    public function customTags(): array
    {
        return [
            'FAM:*:ADDR:_NUM'         => new CustomElement(I18N::translate('House number')),
            'FAM:*:ADDR:_STRASSE'     => new CustomElement(I18N::translate('Street name')),
            'FAM:*:DATE:_ZUS'         => new CustomElement(I18N::translate('Additional information')),
            'FAM:*:OBJE:_PRIM'        => new CustomElement(I18N::translate('Highlighted image')),
            'FAM:*:PLAC:_AON'         => new CustomElement(I18N::translate('Alternative place name')),
            // I18N: https://foko.genealogy.net
            'FAM:*:PLAC:_FCTRY'       => new CustomElement(I18N::translate('FOKO country')),
            // I18N: https://foko.genealogy.net
            'FAM:*:PLAC:_FSTAE'       => new CustomElement(I18N::translate('FOKO country')),
            // I18N: https://gov.genealogy.net
            'FAM:*:PLAC:_GOV'         => new GovIdentifier(I18N::translate('GOV identifier')),
            // I18N: https://en.wikipedia.org/wiki/Maidenhead_Locator_System
            'FAM:*:PLAC:_MAIDENHEAD'  => new MaidenheadLocator(I18N::translate('Maidenhead location code')),
            'FAM:*:PLAC:_POST'        => new AddressPostalCode(I18N::translate('Postal code')),
            'FAM:*:PLAC:_SIC'         => new CustomElement(I18N::translate('Reliability of the information')),
            'FAM:*:PLAC:_ZUS'         => new CustomElement(I18N::translate('Additional information')),
            'FAM:*:SOUR:_ORI'         => new TextFromSource(I18N::translate('Original text')),
            'FAM:*:SOUR:_ZUS'         => new CustomElement(I18N::translate('Additional information')),
            'FAM:*:SOUR:PAGE:_ZUS'    => new CustomElement(I18N::translate('Additional information')),
            'FAM:*:_ASSO'             => new XrefAssociate(I18N::translate('Associate')),
            'FAM:*:_CERT'             => new CustomElement(I18N::translate('Certificate number')),
            'FAM:*:_COM'              => new CustomElement(I18N::translate('Comment')),
            'FAM:*:_SITE'             => new CustomElement(I18N::translate('Extra information')),
            'FAM:*:_WITN'             => new CustomElement(I18N::translate('Witness')),
            'FAM:OBJE:_PRIM'          => new CustomElement(I18N::translate('Highlighted image')),
            'FAM:SOUR:_ORI'           => new TextFromSource(I18N::translate('Original text')),
            'FAM:SOUR:_ZUS'           => new CustomElement(I18N::translate('Additional information')),
            'FAM:SOUR:PAGE:_ZUS'      => new CustomElement(I18N::translate('Additional information')),
            'FAM:_CREAT'              => new DateValue(I18N::translate('Creation date')),
            'FAM:_LIV'                => new CustomElement(I18N::translate('Cohabitation')),
            'FAM:_NAME'               => new CustomElement(I18N::translate('Joint family name')),
            'FAM:_UID'                => new PafUid(I18N::translate('Unique identifier')),
            'INDI:*:ADDR:_NUM'        => new CustomElement(I18N::translate('House number')),
            'INDI:*:ADDR:_STRASSE'    => new CustomElement(I18N::translate('Street name')),
            'INDI:*:DATE:_ZUS'        => new CustomElement(I18N::translate('Additional information')),
            'INDI:*:OBJE:_PRIM'       => new CustomElement(I18N::translate('Highlighted image')),
            'INDI:*:PLAC:_AON'        => new CustomElement(I18N::translate('Alternative place name')),
            // I18N: https://foko.genealogy.net
            'INDI:*:PLAC:_FCTRY'      => new CustomElement(I18N::translate('FOKO country')),
            // I18N: https://foko.genealogy.net
            'INDI:*:PLAC:_FSTAE'      => new CustomElement(I18N::translate('FOKO country')),
            // I18N: https://gov.genealogy.net
            'INDI:*:PLAC:_GOV'        => new GovIdentifier(I18N::translate('GOV identifier')),
            // I18N: https://en.wikipedia.org/wiki/Maidenhead_Locator_System
            'INDI:*:PLAC:_MAIDENHEAD' => new MaidenheadLocator(I18N::translate('Maidenhead location code')),
            'INDI:*:PLAC:_POST'       => new AddressPostalCode(I18N::translate('Postal code')),
            'INDI:*:PLAC:_SIC'        => new CustomElement(I18N::translate('Reliability of the information')),
            'INDI:*:PLAC:_ZUS'        => new CustomElement(I18N::translate('Additional information')),
            'INDI:*:SOUR:_ORI'        => new TextFromSource(I18N::translate('Original text')),
            'INDI:*:SOUR:_ZUS'        => new CustomElement(I18N::translate('Additional information')),
            'INDI:*:SOUR:PAGE:_ZUS'   => new CustomElement(I18N::translate('Additional information')),
            'INDI:*:_ASSO'            => new XrefAssociate(I18N::translate('Associate')),
            'INDI:*:_CERT'            => new CustomElement(I18N::translate('Certificate number')),
            'INDI:*:_COM'             => new CustomElement(I18N::translate('Comment')),
            'INDI:*:_SITE'            => new CustomElement(I18N::translate('Extra information')),
            'INDI:*:_WITN'            => new CustomElement(I18N::translate('Witness')),
            'INDI:BAPM:_GODP'         => new CustomElement(I18N::translate('Also known as')),
            'INDI:CHR:_GODP'          => new CustomElement(I18N::translate('Godparent')),
            'INDI:OBJE:_PRIM'         => new CustomElement(I18N::translate('Highlighted image')),
            'INDI:SOUR:_ORI'          => new TextFromSource(I18N::translate('Original text')),
            'INDI:SOUR:_ZUS'          => new CustomElement(I18N::translate('Additional information')),
            'INDI:SOUR:PAGE:_ZUS'     => new CustomElement(I18N::translate('Additional information')),
            'INDI:NAME:_AKA'          => new CustomElement(I18N::translate('Also known as')),
            // https://en.wikipedia.org/wiki/Rufname
            'INDI:NAME:RUFN'          => new CustomElement(I18N::translate('Rufname')),
            'INDI:_CREAT'             => new CustomElement(I18N::translate('Creation date')),
            'INDI:_HEIM'              => new CustomElement(/* I18N: German Bürgerort */ I18N::translate('Place of citizenship')),
            'INDI:_UID'               => new PafUid(I18N::translate('Unique identifier')),
            'NOTE:_CREAT'             => new DateValue(I18N::translate('Creation date')),
            'NOTE:_UID'               => new PafUid(I18N::translate('Unique identifier')),
            'OBJE:_CREAT'             => new DateValue(I18N::translate('Creation date')),
            'OBJE:_UID'               => new PafUid(I18N::translate('Unique identifier')),
            'REPO:_CREAT'             => new DateValue(I18N::translate('Creation date')),
            'REPO:_UID'               => new PafUid(I18N::translate('Unique identifier')),
            'SOUR:_CREAT'             => new DateValue(I18N::translate('Creation date')),
            'SOUR:_KTIT'              => new SourceFiledByEntry(I18N::translate('Abbreviation')),
            'SOUR:_UID'               => new PafUid(I18N::translate('Unique identifier')),
        ];
    }

    /**
     * The application for which we are supporting custom tags.
     *
     * @return string
     */
    public function customTagApplication(): string
    {
        return 'Gen_Pluswin™';
    }
}
