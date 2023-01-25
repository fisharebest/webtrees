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
use Fisharebest\Webtrees\Elements\AddressWebPage;
use Fisharebest\Webtrees\Elements\CustomElement;
use Fisharebest\Webtrees\Elements\DateValue;
use Fisharebest\Webtrees\Elements\PafUid;
use Fisharebest\Webtrees\I18N;

/**
 * GEDCOM files created by Legacy
 *
 * @see https://legacyfamilytree.com
 * @see http://support.legacyfamilytree.com/article/AA-00520/0/GEDCOM-Files-custom-tags-in-Legacy.html
 */
class Legacy implements CustomTagInterface
{
    /**
     * The name of the application.
     *
     * @return string
     */
    public function name(): string
    {
        return 'Legacy';
    }

    /**
     * Tags created by this application.
     *
     * @return array<string,ElementInterface>
     */
    public function tags(): array
    {
        return [
            'FAM:*:ADDR:_PRIV'             => new CustomElement(I18N::translate('Private')),
            'FAM:*:PLAC:_VERI'             => new CustomElement(I18N::translate('Verified')),
            'FAM:*:SOUR:DATE'              => new DateValue(I18N::translate('Date')),
            'FAM:*:SOUR:_VERI'             => new CustomElement(I18N::translate('Verified')),
            'FAM:*:_PRIV'                  => new CustomElement(I18N::translate('Private')),
            'FAM:CHIL:_FREL'               => new CustomElement(I18N::translate('Relationship to father')),
            'FAM:CHIL:_MREL'               => new CustomElement(I18N::translate('Relationship to mother')),
            'FAM:CHIL:_STAT'               => new CustomElement(I18N::translate('Status')),
            'FAM:EVEN:_OVER'               => new CustomElement('Event sentence override'),
            'FAM:MARR:_HTITL'              => new CustomElement(I18N::translate('Label for husband')),
            'FAM:MARR:_RPT_PHRS'           => /* I18N: ''GEDCOM tag _RPT_PHRS */ new CustomElement(I18N::translate('Report phrase')),
            'FAM:MARR:_RPT_PHRS2'          => /* I18N: ''GEDCOM tag _RPT_PHRS */ new CustomElement(I18N::translate('Report phrase')),
            'FAM:MARR:_STAT'               => new CustomElement(I18N::translate('Status')),
            'FAM:MARR:_WTITL'              => new CustomElement(I18N::translate('Label for wife')),
            'FAM:_NONE'                    => new CustomElement(I18N::translate('No children')),
            'FAM:_TAG'                     => new CustomElement('Tag'),
            'FAM:_TAG2'                    => new CustomElement('Tag #2'),
            'FAM:_TAG3'                    => new CustomElement('Tag #3'),
            'FAM:_TAG4'                    => new CustomElement('Tag #4'),
            'FAM:_TAG5'                    => new CustomElement('Tag #5'),
            'FAM:_TAG6'                    => new CustomElement('Tag #6'),
            'FAM:_TAG7'                    => new CustomElement('Tag #7'),
            'FAM:_TAG8'                    => new CustomElement('Tag #8'),
            'FAM:_TAG9'                    => new CustomElement('Tag #9'),
            'FAM:_UID'                     => new PafUid(I18N::translate('Unique identifier')),
            'HEAD:_EVENT_DEFN'             => new CustomElement('Event definition'),
            'HEAD:_EVENT_DEFN:_CONF_FLAG'  => new CustomElement(I18N::translate('Private')),
            'HEAD:_EVENT_DEFN:_DATE_TYPE'  => new CustomElement(I18N::translate('Date')),
            'HEAD:_EVENT_DEFN:_DESC_FLAG'  => new CustomElement(I18N::translate('Description')),
            'HEAD:_EVENT_DEFN:_PLACE_TYPE' => new CustomElement(I18N::translate('Place')),
            'HEAD:_EVENT_DEFN:_PP_EXCLUDE' => new CustomElement('Exclude event from potential problems report'),
            'HEAD:_EVENT_DEFN:_SEN1'       => new CustomElement('Event sentence definition'),
            'HEAD:_EVENT_DEFN:_SEN2'       => new CustomElement('Event sentence definition'),
            'HEAD:_EVENT_DEFN:_SEN3'       => new CustomElement('Event sentence definition'),
            'HEAD:_EVENT_DEFN:_SEN4'       => new CustomElement('Event sentence definition'),
            'HEAD:_EVENT_DEFN:_SEN5'       => new CustomElement('Event sentence definition'),
            'HEAD:_EVENT_DEFN:_SEN6'       => new CustomElement('Event sentence definition'),
            'HEAD:_EVENT_DEFN:_SEN7'       => new CustomElement('Event sentence definition'),
            'HEAD:_EVENT_DEFN:_SEN8'       => new CustomElement('Event sentence definition'),
            'HEAD:_EVENT_DEFN:_SENDOF'     => new CustomElement('Event sentence, female, date only'),
            'HEAD:_EVENT_DEFN:_SENDOM'     => new CustomElement('Event sentence, male, date only'),
            'HEAD:_EVENT_DEFN:_SENDOU'     => new CustomElement('Event sentence, unknown sex, date only'),
            'HEAD:_EVENT_DEFN:_SENDPF'     => new CustomElement('Event sentence, female, date and place'),
            'HEAD:_EVENT_DEFN:_SENDPM'     => new CustomElement('Event sentence, male, date and place'),
            'HEAD:_EVENT_DEFN:_SENDPU'     => new CustomElement('Event sentence, unknown sex, date and place'),
            'HEAD:_EVENT_DEFN:_SENF'       => new CustomElement('Event sentence, female'),
            'HEAD:_EVENT_DEFN:_SENM'       => new CustomElement('Event sentence, male'),
            'HEAD:_EVENT_DEFN:_SENPOF'     => new CustomElement('Event sentence, unknown sex'),
            'HEAD:_EVENT_DEFN:_SENPOM'     => new CustomElement('Event sentence, female, place only'),
            'HEAD:_EVENT_DEFN:_SENPOU'     => new CustomElement('Event sentence, male, place only'),
            'HEAD:_EVENT_DEFN:_SENU'       => new CustomElement('Event sentence, unknown sex, place only'),
            'HEAD:_PLAC_DEFN'              => new CustomElement('Place definition'),
            'HEAD:_PLAC_DEFN:_PREP'        => new CustomElement('Place preposition'),
            'INDI:*:ADDR:_EMAIL'           => new CustomElement(I18N::translate('Email')),
            'INDI:*:ADDR:_LIST1'           => new CustomElement('Include in the “newsletter” group'),
            'INDI:*:ADDR:_LIST2'           => new CustomElement('Include in the “family association” group'),
            'INDI:*:ADDR:_LIST3'           => new CustomElement('Include in the “birthday” group'),
            'INDI:*:ADDR:_LIST4'           => new CustomElement('Include in the “research” group'),
            'INDI:*:ADDR:_LIST5'           => new CustomElement('Include in the “christmas” group'),
            'INDI:*:ADDR:_LIST6'           => new CustomElement('Include in the “holiday” group'),
            'INDI:*:ADDR:_NAME'            => new CustomElement(I18N::translate('Name of addressee')),
            'INDI:*:ADDR:_PRIV'            => new CustomElement(I18N::translate('Private')),
            'INDI:*:ADDR:_SORT'            => new CustomElement('The spelling of a name to be used when sorting addresses for a report'),
            'INDI:*:ADDR:_TAG'             => new CustomElement('Tag'),
            'INDI:*:PLAC:_TAG'             => new CustomElement('Tag'),
            'INDI:*:PLAC:_VERI'            => new CustomElement(I18N::translate('Verified')),
            'INDI:*:SOUR:DATE'             => new DateValue(I18N::translate('Date')),
            'INDI:*:SOUR:_VERI'            => new CustomElement(I18N::translate('Verified')),
            'INDI:*:_PRIV'                 => new CustomElement(I18N::translate('Private')),
            'INDI:EVEN:_OVER'              => new CustomElement('Event sentence override'),
            'INDI:SOUR:_VERI'              => new CustomElement(I18N::translate('Verified')),
            'INDI:_TAG'                    => new CustomElement('Tag'),
            'INDI:_TAG2'                   => new CustomElement('Tag #2'),
            'INDI:_TAG3'                   => new CustomElement('Tag #3'),
            'INDI:_TAG4'                   => new CustomElement('Tag #4'),
            'INDI:_TAG5'                   => new CustomElement('Tag #5'),
            'INDI:_TAG6'                   => new CustomElement('Tag #6'),
            'INDI:_TAG7'                   => new CustomElement('Tag #7'),
            'INDI:_TAG8'                   => new CustomElement('Tag #8'),
            'INDI:_TAG9'                   => new CustomElement('Tag #9'),
            'INDI:_TODO'                   => new CustomElement(I18N::translate('Research task')),
            'INDI:_TODO:PRTY'              => new CustomElement(I18N::translate('Priority')),
            'INDI:_TODO:_CAT'              => new CustomElement(I18N::translate('Category')),
            'INDI:_TODO:_CDATE'            => new CustomElement(I18N::translate('Completion date')),
            'INDI:_TODO:_LOCL'             => new CustomElement(I18N::translate('Location')),
            'INDI:_TODO:_RDATE'            => new CustomElement(I18N::translate('Reminder date')),
            'INDI:_UID'                    => new PafUid(I18N::translate('Unique identifier')),
            'INDI:_URL'                    => new AddressWebPage(I18N::translate('URL')),
            'OBJE:_DATE'                   => new CustomElement(I18N::translate('Date')),
            'OBJE:_PRIM'                   => new CustomElement(I18N::translate('Highlighted image')),
            'OBJE:_SCBK'                   => new CustomElement(I18N::translate('Scrapbook')),
            'OBJE:_SOUND'                  => new CustomElement(I18N::translate('Audio')),
            'OBJE:_TYPE'                   => new CustomElement(I18N::translate('Type')),
            'OBJE:_UID'                    => new PafUid(I18N::translate('Unique identifier')),
            'REPO:_UID'                    => new PafUid(I18N::translate('Unique identifier')),
            'SOUR:_ITALIC'                 => new CustomElement('The source title should be printed in italic on reports'),
            'SOUR:_PAREN'                  => new CustomElement('The source title should be printed within parentheses on reports'),
            'SOUR:_QUOTED'                 => new CustomElement('The source title should be printed within quotes on reports'),
            'SOUR:_TAG'                    => new CustomElement('Exclude the source citation detail on reports'),
            'SOUR:_TAG2'                   => new CustomElement('Exclude the source citation on reports'),
            'SOUR:_TAG3'                   => new CustomElement('Include the source citation detail text on reports'),
            'SOUR:_TAG4'                   => new CustomElement('Include the source citation detail notes on reports'),
            'SOUR:_UID'                    => new PafUid(I18N::translate('Unique identifier')),
        ];
    }
}
