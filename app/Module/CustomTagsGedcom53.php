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
use Fisharebest\Webtrees\Elements\CertaintyAssessment;
use Fisharebest\Webtrees\Elements\CustomElement;
use Fisharebest\Webtrees\Elements\DateValue;
use Fisharebest\Webtrees\Elements\NoteStructure;
use Fisharebest\Webtrees\Elements\PlaceName;
use Fisharebest\Webtrees\Elements\SourceMediaType;
use Fisharebest\Webtrees\Elements\TextFromSource;
use Fisharebest\Webtrees\Elements\XrefSource;
use Fisharebest\Webtrees\I18N;

/**
 * Class CustomTagsGedcom53
 */
class CustomTagsGedcom53 extends AbstractModule implements ModuleConfigInterface, ModuleCustomTagsInterface
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
            'EVEN'                       => new CustomElement('Event'),
            'EVEN:*:*:NAME'              => new CustomElement(I18N::translate('Name')),
            'EVEN:*:AUDIO'               => new CustomElement(I18N::translate('Audio')),
            'EVEN:*:BROT'                => new PlaceName('Brother'),
            'EVEN:*:BUYR'                => new PlaceName('Buyer'),
            'EVEN:*:CHIL'                => new PlaceName('Child'),
            'EVEN:*:DATE'                => new DateValue('Date'),
            'EVEN:*:FATH'                => new PlaceName('Father'),
            'EVEN:*:GODP'                => new PlaceName('Godparent'),
            'EVEN:*:HDOH'                => new PlaceName('Head of household'),
            'EVEN:*:HEIR'                => new PlaceName('Heir'),
            'EVEN:*:HFAT'                => new PlaceName('Husband’s father'),
            'EVEN:*:HMOT'                => new PlaceName('Husband’s mother'),
            'EVEN:*:HUSB'                => new PlaceName('Husband'),
            'EVEN:*:IMAGE'               => new CustomElement('Image'),
            'EVEN:*:INDI'                => new PlaceName('Individual'),
            'EVEN:*:INFT'                => new PlaceName('Informant'),
            'EVEN:*:LEGA'                => new PlaceName('Legatee'),
            'EVEN:*:MBR'                 => new PlaceName('Member'),
            'EVEN:*:MOTH'                => new PlaceName('Mother'),
            'EVEN:*:OFFI'                => new PlaceName('Official'),
            'EVEN:*:PARE'                => new PlaceName('Parent'),
            'EVEN:*:PHOTO'               => new CustomElement(I18N::translate('Photo')),
            'EVEN:*:PHUS'                => new PlaceName('Previous husband'),
            'EVEN:*:PLAC'                => new PlaceName('Place'),
            'EVEN:*:PWIF'                => new PlaceName('Previous wife'),
            'EVEN:*:RECO'                => new PlaceName('Recorder'),
            'EVEN:*:REL'                 => new PlaceName('Relative'),
            'EVEN:*:SELR'                => new PlaceName('Seller'),
            'EVEN:*:SIBL'                => new PlaceName('Sibling'),
            'EVEN:*:SIST'                => new PlaceName('Sister'),
            'EVEN:*:SPOU'                => new PlaceName('Spouse'),
            'EVEN:*:TXPY'                => new PlaceName('Taxpayer'),
            'EVEN:*:VIDEO'               => new CustomElement(I18N::translate('Video')),
            'EVEN:*:WFAT'                => new PlaceName('Wife’s father'),
            'EVEN:*:WIFE'                => new PlaceName('Wife'),
            'EVEN:*:WITN'                => new PlaceName('Witness'),
            'EVEN:*:WMOT'                => new PlaceName('Wife’s mother'),
            'EVEN:TYPE'                  => new CustomElement('Type of event'),
            'FAM:*:*:QUAY'               => new CertaintyAssessment(I18N::translate('Quality of data')),
            'FAM:*:PLAC:SITE'            => new CustomElement('Site'),
            'FAM:*:QUAY'                 => new CertaintyAssessment(I18N::translate('Quality of data')),
            'FAM:AUDIO'                  => new CustomElement(I18N::translate('Audio')),
            'FAM:IMAGE'                  => new CustomElement('Image'),
            'FAM:PHOTO'                  => new CustomElement(I18N::translate('Photo')),
            'FAM:VIDEO'                  => new CustomElement(I18N::translate('Video')),
            'HEAD:SCHEMA'                => new CustomElement(I18N::translate('Unknown')),
            'HEAD:SCHEMA:FAM'            => new CustomElement(I18N::translate('Family')),
            'HEAD:SCHEMA:FAM:*:_*'       => new CustomElement('Custom event'),
            'HEAD:SCHEMA:FAM:*:_*:DEFN'  => new CustomElement('Definition'),
            'HEAD:SCHEMA:FAM:*:_*:ISA'   => new CustomElement('Type of event'),
            'HEAD:SCHEMA:FAM:*:_*:LABL'  => new CustomElement('Label'),
            'HEAD:SCHEMA:FAM:_*'         => new CustomElement('Custom event'),
            'HEAD:SCHEMA:FAM:_*:DEFN'    => new CustomElement('Definition'),
            'HEAD:SCHEMA:FAM:_*:ISA'     => new CustomElement('Type of event'),
            'HEAD:SCHEMA:FAM:_*:LABL'    => new CustomElement('Label'),
            'HEAD:SCHEMA:INDI'           => new CustomElement(I18N::translate('Individual')),
            'HEAD:SCHEMA:INDI:*:_*'      => new CustomElement('Custom event'),
            'HEAD:SCHEMA:INDI:*:_*:DEFN' => new CustomElement('Definition'),
            'HEAD:SCHEMA:INDI:*:_*:ISA'  => new CustomElement('Type of event'),
            'HEAD:SCHEMA:INDI:*:_*:LABL' => new CustomElement('Label'),
            'HEAD:SCHEMA:INDI:_*'        => new CustomElement('Custom event'),
            'HEAD:SCHEMA:INDI:_*:DEFN'   => new CustomElement('Definition'),
            'HEAD:SCHEMA:INDI:_*:ISA'    => new CustomElement('Type of event'),
            'HEAD:SCHEMA:INDI:_*:LABL'   => new CustomElement('Label'),
            'INDI:*:*:QUAY'              => new CertaintyAssessment(I18N::translate('Quality of data')),
            'INDI:*:PLAC:SITE'           => new CustomElement('Site'),
            'INDI:*:QUAY'                => new CertaintyAssessment(I18N::translate('Quality of data')),
            'INDI:AUDIO'                 => new CustomElement(I18N::translate('Audio')),
            'INDI:BURI:PLAC:CEME'        => new CustomElement(I18N::translate('Cemetery')),
            'INDI:BURI:PLAC:CEME:PLOT'   => new CustomElement('Burial plot'),
            'INDI:IMAGE'                 => new CustomElement('Image'),
            'INDI:NAMR'                  => new CustomElement(I18N::translate('Religious name')),
            'INDI:NAMS'                  => new CustomElement(I18N::translate('Namesake')),
            'INDI:PHOTO'                 => new CustomElement(I18N::translate('Photo')),
            'INDI:SIGN'                  => new CustomElement('Signature'),
            'INDI:VIDEO'                 => new CustomElement(I18N::translate('Video')),
            'REPO:CALN:ITEM'             => new CustomElement('Item'),
            'REPO:CALN:PAGE'             => new CustomElement('Page'),
            'REPO:CALN:SHEE'             => new CustomElement('Sheet'),
            'REPO:CNTC'                  => new CustomElement('Contact person'),
            'REPO:MEDI'                  => new SourceMediaType(I18N::translate('Media type')),
            'REPO:REFN'                  => new CustomElement('Reference number'),
            'SOUR:AUDIO'                 => new CustomElement(I18N::translate('Audio')),
            'SOUR:CENS'                  => new CustomElement('Census'),
            'SOUR:CENS:DATE'             => new CustomElement('Census'),
            'SOUR:CENS:DWEL'             => new CustomElement('Dwelling number'),
            'SOUR:CENS:FAMN'             => new CustomElement('Family number'),
            'SOUR:CENS:LINE'             => new CustomElement('Line number'),
            'SOUR:CLAS'                  => new CustomElement('Source classification'),
            'SOUR:CPLR'                  => new CustomElement('Compiler'),
            'SOUR:EDTR'                  => new CustomElement('Editor'),
            'SOUR:EVEN'                  => new CustomElement('Source events'),
            'SOUR:FIDE'                  => new CustomElement('Fidelity'),
            'SOUR:FILM'                  => new CustomElement(I18N::translate('Microfilm')),
            'SOUR:IMAGE'                 => new CustomElement('Image'),
            'SOUR:INDX'                  => new CustomElement('Indexed'),
            'SOUR:INTV'                  => new CustomElement('Interviewer'),
            'SOUR:ORIG'                  => new CustomElement('Originator'),
            'SOUR:ORIG:NAME'             => new CustomElement('Name'),
            'SOUR:ORIG:NOTE'             => new CustomElement('Note'),
            'SOUR:ORIG:TYPE'             => new CustomElement('Type'),
            'SOUR:PERI'                  => new CustomElement('Date period'),
            'SOUR:PHOTO'                 => new CustomElement(I18N::translate('Photo')),
            'SOUR:PUBL:DATE'             => new CustomElement('Date'),
            'SOUR:PUBL:EDTN'             => new CustomElement('Edition'),
            'SOUR:PUBL:ISSU'             => new CustomElement('Issue'),
            'SOUR:PUBL:LCCN'             => new CustomElement('Library of Congress call number'),
            'SOUR:PUBL:NAME'             => new CustomElement('Name'),
            'SOUR:PUBL:PUBR'             => new CustomElement('Publisher'),
            'SOUR:PUBL:SERS'             => new CustomElement('Series'),
            'SOUR:PUBL:TYPE'             => new CustomElement('Type'),
            'SOUR:QUAY'                  => new CertaintyAssessment(I18N::translate('Quality of data')),
            'SOUR:RECO'                  => new CustomElement('Recording agency?'),
            'SOUR:REFS'                  => new XrefSource('Referenced source'),
            'SOUR:REPO:DPRT:ARVL'        => new CustomElement('Departure'),
            'SOUR:REPO:DPRT:ARVL:DATE'   => new DateValue('Date'),
            'SOUR:REPO:DPRT:ARVL:PLAC'   => new PlaceName('Place'),
            'SOUR:REPO:NAME'             => new CustomElement('Name of vessel'),
            'SOUR:REPO:NOTE'             => new NoteStructure(I18N::translate('Note')),
            'SOUR:REPO:PORT'             => new CustomElement('Port'),
            'SOUR:REPO:PORT:ARVL'        => new CustomElement('Arrival'),
            'SOUR:REPO:PORT:ARVL:DATE'   => new DateValue('Date'),
            'SOUR:REPO:PORT:ARVL:PLAC'   => new PlaceName('Place'),
            'SOUR:REPO:TEXT'             => new TextFromSource(I18N::translate('Text')),
            'SOUR:SEQU'                  => new CustomElement('Sequence'),
            'SOUR:STAT'                  => new CustomElement('Search status'),
            'SOUR:STAT:DATE'             => new DateValue('Date'),
            'SOUR:TEXT'                  => new TextFromSource(I18N::translate('Text')),
            'SOUR:TYPE'                  => new CustomElement('Type of source'),
            'SOUR:VIDEO'                 => new CustomElement(I18N::translate('Video')),
            'SOUR:XLTR'                  => new CustomElement('Translator'),
        ];
    }

    /**
     * The application for which we are supporting custom tags.
     *
     * @return string
     */
    public function customTagApplication(): string
    {
        return 'GEDCOM 5.3';
    }
}
