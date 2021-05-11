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
use Fisharebest\Webtrees\Elements\CustomElement;

/**
 * Class CustomTagsLegacy
 */
class CustomTagsLegacy extends AbstractModule implements ModuleConfigInterface, ModuleCustomTagsInterface
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
     * @see http://support.legacyfamilytree.com/article/AA-00520/0/GEDCOM-Files-custom-tags-in-Legacy.html
     *
     * @return array<string,ElementInterface>
     */
    public function customTags(): array
    {
        return [
            'FAM:*:ADDR:_PRIV'             => new CustomElement('Indicates that an address or event is marked as Private.'),
            'FAM:*:PLAC:_VERI'             => new CustomElement('Indicates that a source citation or place name has a checkmark in the Verified column.'),
            'FAM:*:SOUR:_VERI'             => new CustomElement('Indicates that a source citation or place name has a checkmark in the Verified column.'),
            'FAM:*:_PRIV'                  => new CustomElement('Indicates that an address or event is marked as Private.'),
            'FAM:CHIL:_FREL'               => new CustomElement('The Relationship of a child to the Father (under a CHIL block under a FAM record).'),
            'FAM:CHIL:_MREL'               => new CustomElement('The Relationship of a child to the Mother (under a CHIL block under a FAM record).'),
            'FAM:CHIL:_STAT'               => new CustomElement('The Status of a marriage (Married, Unmarried, etc.).  Also the Status of a child (Twin, Triplet, etc.).  (The marriage status of Divorced is exported using a DIV tag.)'),
            'FAM:EVEN:_OVER'               => new CustomElement('An event sentence override (under an EVEN block).'),
            'FAM:MARR:_STAT'               => new CustomElement('The Status of a marriage (Married, Unmarried, etc.).  Also the Status of a child (Twin, Triplet, etc.).  (The marriage status of Divorced is exported using a DIV tag.)'),
            'FAM:SOUR:_VERI'               => new CustomElement('Indicates that a source citation or place name has a checkmark in the Verified column.'),
            'FAM:_NONE'                    => new CustomElement('Indicates that a couple had no children (under a FAM record).'),
            'HEAD:_EVENT_DEFN'             => new CustomElement('Indicates the start of an Event Definition record that describes the attributes of an event or fact.'),
            'HEAD:_EVENT_DEFN:_CONF_FLAG'  => new CustomElement('Indicates that an event is Confidential or Private (under an _EVENT_DEFN record).'),
            'HEAD:_EVENT_DEFN:_DATE_TYPE'  => new CustomElement('Indicates whether or not a Date field is shown for a specific event (under an _EVENT_DEFN record).'),
            'HEAD:_EVENT_DEFN:_DESC_FLAG'  => new CustomElement('Indicates whether or not a Description field is shown for a specific event (under an _EVENT_DEFN record).'),
            'HEAD:_EVENT_DEFN:_PLACE_TYPE' => new CustomElement('Indicates whether or not a Place field is shown for a specific event (under an _EVENT_DEFN record).'),
            'HEAD:_EVENT_DEFN:_PP_EXCLUDE' => new CustomElement('Indicates that an event is to be Excluded from the Potential Problems reporting (under an _EVENT_DEFN record).'),
            'HEAD:_EVENT_DEFN:_SEN1'       => new CustomElement('Event sentence definitions (under an _EVENT_DEFN record).'),
            'HEAD:_EVENT_DEFN:_SEN2'       => new CustomElement('Event sentence definitions (under an _EVENT_DEFN record).'),
            'HEAD:_EVENT_DEFN:_SEN3'       => new CustomElement('Event sentence definitions (under an _EVENT_DEFN record).'),
            'HEAD:_EVENT_DEFN:_SEN4'       => new CustomElement('Event sentence definitions (under an _EVENT_DEFN record).'),
            'HEAD:_EVENT_DEFN:_SEN5'       => new CustomElement('Event sentence definitions (under an _EVENT_DEFN record).'),
            'HEAD:_EVENT_DEFN:_SEN6'       => new CustomElement('Event sentence definitions (under an _EVENT_DEFN record).'),
            'HEAD:_EVENT_DEFN:_SEN7'       => new CustomElement('Event sentence definitions (under an _EVENT_DEFN record).'),
            'HEAD:_EVENT_DEFN:_SEN8'       => new CustomElement('Event sentence definitions (under an _EVENT_DEFN record).'),
            'HEAD:_EVENT_DEFN:_SENDOF'     => new CustomElement('Event sentence for PAF5 if only the Date field is filled in for a Male individual (under an _EVENT_DEFN record).'),
            'HEAD:_EVENT_DEFN:_SENDOM'     => new CustomElement('Event sentence for PAF5 if only the Date field is filled in for a Male individual (under an _EVENT_DEFN record).'),
            'HEAD:_EVENT_DEFN:_SENDOU'     => new CustomElement('Event sentence for PAF5 if only the Date field is filled in for a Male individual (under an _EVENT_DEFN record).'),
            'HEAD:_EVENT_DEFN:_SENDPF'     => new CustomElement('Event sentence for PAF5 if only the Date and Place fields are filled in for a Male individual (under an _EVENT_DEFN record).'),
            'HEAD:_EVENT_DEFN:_SENDPM'     => new CustomElement('Event sentence for PAF5 if only the Date and Place fields are filled in for a Male individual (under an _EVENT_DEFN record).'),
            'HEAD:_EVENT_DEFN:_SENDPU'     => new CustomElement('Event sentence for PAF5 if only the Date and Place fields are filled in for a Male individual (under an _EVENT_DEFN record).'),
            'HEAD:_EVENT_DEFN:_SENF'       => new CustomElement('Event sentence for PAF5 if all fields are filled in for a Male individual (under an _EVENT_DEFN record).'),
            'HEAD:_EVENT_DEFN:_SENM'       => new CustomElement('Event sentence for PAF5 if all fields are filled in for a Male individual (under an _EVENT_DEFN record).'),
            'HEAD:_EVENT_DEFN:_SENPOF'     => new CustomElement('Event sentence for PAF5 if only the Place field is filled in for a Male individual (under an _EVENT_DEFN record).'),
            'HEAD:_EVENT_DEFN:_SENPOM'     => new CustomElement('Event sentence for PAF5 if only the Place field is filled in for a Male individual (under an _EVENT_DEFN record).'),
            'HEAD:_EVENT_DEFN:_SENPOU'     => new CustomElement('Event sentence for PAF5 if only the Place field is filled in for a Male individual (under an _EVENT_DEFN record).'),
            'HEAD:_EVENT_DEFN:_SENU'       => new CustomElement('Event sentence for PAF5 if all fields are filled in for a Male individual (under an _EVENT_DEFN record).'),
            'HEAD:_PLAC_DEFN'              => new CustomElement('Indicates the start of a Place Definition record that describes the attribute of a place.'),
            'HEAD:_PLAC_DEFN:_PREP'        => new CustomElement('A location Preposition (under a _PLAC_DEFN record).'),
            'INDI:*:ADDR:_LIST3 YES'       => new CustomElement('Indicates that a person’s address is part of the Birthday grouping (under an ADDR block).'),
            'INDI:*:ADDR:_LIST4 YES'       => new CustomElement('Indicates that a person’s address is part of the Research grouping (under an ADDR block).'),
            'INDI:*:ADDR:_LIST5 YES'       => new CustomElement('Indicates that a person’s address is part of the Christmas grouping (under an ADDR block).'),
            'INDI:*:ADDR:_LIST6 YES'       => new CustomElement('Indicates that a person’s address is part of the Holiday grouping (under an ADDR block).'),
            'INDI:*:ADDR:_NAME'            => new CustomElement('The name of an individual as part of an address (under an ADDR block).'),
            'INDI:*:ADDR:_PRIV'            => new CustomElement('Indicates that an address or event is marked as Private.'),
            'INDI:*:ADDR:_SORT'            => new CustomElement('The spelling of a name to be used when sorting addresses for a report (under an ADDR block).'),
            'INDI:*:ADDR:_TAG'             => new CustomElement('Indicates that an address, or place has been tagged.  Also used for Tag 1 selection for an individual.'),
            'INDI:*:PLAC:_TAG'             => new CustomElement('Indicates that an address, or place has been tagged.  Also used for Tag 1 selection for an individual.'),
            'INDI:*:PLAC:_VERI'            => new CustomElement('Indicates that a source citation or place name has a checkmark in the Verified column.'),
            'INDI:*:SOUR:_VERI'            => new CustomElement('Indicates that a source citation or place name has a checkmark in the Verified column.'),
            'INDI:*:_PRIV'                 => new CustomElement('Indicates that an address or event is marked as Private.'),
            'INDI:ADDR:_EMAIL'             => new CustomElement('An email address (under an ADDR block).'),
            'INDI:ADDR:_LIST1 YES'         => new CustomElement('Indicates that a person’s address is part of the Newsletter grouping (under an ADDR block).'),
            'INDI:ADDR:_LIST2 YES'         => new CustomElement('Indicates that a person’s address is part of the Family Association grouping (under an ADDR block).'),
            'INDI:EVEN:_OVER'              => new CustomElement('An event sentence override (under an EVEN block).'),
            'INDI:SOUR:_VERI'              => new CustomElement('Indicates that a source citation or place name has a checkmark in the Verified column.'),
            'INDI:_TAG'                    => new CustomElement('When under an INDI record, indicates that an individual has been given certain tag marks.'),
            'INDI:_TAG2'                   => new CustomElement('When under an INDI record, indicates that an individual has been given certain tag marks.'),
            'INDI:_TAG3'                   => new CustomElement('When under an INDI record, indicates that an individual has been given certain tag marks.'),
            'INDI:_TAG4'                   => new CustomElement('When under an INDI record, indicates that an individual has been given certain tag marks.'),
            'INDI:_TAG5'                   => new CustomElement('When under an INDI record, indicates that an individual has been given certain tag marks.'),
            'INDI:_TAG6'                   => new CustomElement('When under an INDI record, indicates that an individual has been given certain tag marks.'),
            'INDI:_TAG7'                   => new CustomElement('When under an INDI record, indicates that an individual has been given certain tag marks.'),
            'INDI:_TAG8'                   => new CustomElement('When under an INDI record, indicates that an individual has been given certain tag marks.'),
            'INDI:_TAG9'                   => new CustomElement('When under an INDI record, indicates that an individual has been given certain tag marks.'),
            'INDI:_TODO'                   => new CustomElement('Research task'),
            'INDI:_TODO:_CAT'              => new CustomElement('The Category of a To-Do item (under a _TODO record).'),
            'INDI:_TODO:_CDATE'            => new CustomElement('Closed Date of a To-Do item (under a _TODO record).'),
            'INDI:_TODO:_LOCL'             => new CustomElement('The Locality of a To-Do item (under a _TODO record).'),
            'INDI:_TODO:_RDATE'            => new CustomElement('Reminder date on to-do items. (Under a _TODO record.)'),
            'INDI:_UID'                    => new CustomElement('A Unique Identification Number given to each individual in a family file.'),
            'INDI:_URL'                    => new CustomElement('An Internet address (under an INDI record).'),
            'OBJE:_DATE'                   => new CustomElement('A date associated with a multimedia object, usually a picture or video (under an OBJE block).'),
            'OBJE:_PRIM'                   => new CustomElement('Means a multimedia object, usually a picture, is the Primary object (the one that is shown on a report) (under an OBJE block).'),
            'OBJE:_SCBK'                   => new CustomElement('Indicates that a Picture is tagged to be included in a scrapbook report (under an OBJE block).'),
            'OBJE:_SOUND'                  => new CustomElement('A sound file name that is attached to a picture (under an OBJE block).'),
            'OBJE:_TYPE'                   => new CustomElement('The type of a multimedia object: Photo, Sound, or Video (under an OBJE block).'),
            'SOUR:_ITALIC Y'               => new CustomElement('Indicates that a source title should be printed on a report in italics (under a SOUR record).'),
            'SOUR:_PAREN'                  => new CustomElement('Indicates that the Publication Facts of a source should be printed within parentheses on a report (under a SOUR record).'),
            'SOUR:_QUOTED Y'               => new CustomElement('Indicates that a source title should be printed within quotes on a report (under a SOUR record).'),
            'SOUR:_TAG NO'                 => new CustomElement('When used under a SOUR record, indicates to exclude the source citation detail on reports.'),
            'SOUR:_TAG2 NO'                => new CustomElement('When used under a SOUR record, indicates to exclude the source citation on reports.'),
            'SOUR:_TAG3 YES'               => new CustomElement('When used under a SOUR record, indicates to include the source citation detail text on reports.'),
            'SOUR:_TAG4 YES'               => new CustomElement('When used under a SOUR record, indicates to include the source citation detail notes on reports.'),
            '_PREF'                        => new CustomElement('Indicates a Preferred spouse, child or parents.'), // How is this used?
        ];
    }

    /**
     * The application for which we are supporting custom tags.
     *
     * @return string
     */
    public function customTagApplication(): string
    {
        return 'Legacy™';
    }
}
