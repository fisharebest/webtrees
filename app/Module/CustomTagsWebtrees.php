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
use Fisharebest\Webtrees\Elements\EntryRecordingDate;
use Fisharebest\Webtrees\Elements\EventTypeCitedFrom;
use Fisharebest\Webtrees\Elements\NoteStructure;
use Fisharebest\Webtrees\Elements\RelationIsDescriptor;
use Fisharebest\Webtrees\Elements\RestrictionNotice;
use Fisharebest\Webtrees\Elements\RoleInEvent;
use Fisharebest\Webtrees\Elements\SourceData;
use Fisharebest\Webtrees\Elements\TextFromSource;
use Fisharebest\Webtrees\Elements\WebtreesUser;
use Fisharebest\Webtrees\Elements\WhereWithinSource;
use Fisharebest\Webtrees\Elements\XrefAssociate;
use Fisharebest\Webtrees\Elements\XrefMedia;
use Fisharebest\Webtrees\Elements\XrefSource;
use Fisharebest\Webtrees\I18N;

/**
 * Class CustomTagsWebtrees
 */
class CustomTagsWebtrees extends AbstractModule implements ModuleConfigInterface, ModuleCustomTagsInterface
{
    use ModuleConfigTrait;
    use ModuleCustomTagsTrait;

    /**
     * @return array<string,ElementInterface>
     */
    public function customTags(): array
    {
        return [
            'FAM:CHAN:_WT_USER'           => new WebtreesUser(I18N::translate('Author of last change')),
            'FAM:_ASSO'                   => new XrefAssociate(I18N::translate('Associate')),
            'FAM:_ASSO:RELA'              => new RelationIsDescriptor(I18N::translate('Relationship')),
            'FAM:*:_ASSO'                 => new XrefAssociate(I18N::translate('Associate')),
            'FAM:*:_ASSO:NOTE'            => new NoteStructure(I18N::translate('Note')),
            'FAM:*:_ASSO:RELA'            => new RelationIsDescriptor(I18N::translate('Relationship')),
            'FAM:*:_ASSO:SOUR'            => new XrefSource(I18N::translate('Source citation')),
            'FAM:*:_ASSO:SOUR:DATA'       => new SourceData(I18N::translate('Data')),
            'FAM:*:_ASSO:SOUR:DATA:DATE'  => new EntryRecordingDate(I18N::translate('Date of entry in original source')),
            'FAM:*:_ASSO:SOUR:DATA:TEXT'  => new TextFromSource(I18N::translate('Text')),
            'FAM:*:_ASSO:SOUR:EVEN'       => new EventTypeCitedFrom(I18N::translate('Event')),
            'FAM:*:_ASSO:SOUR:EVEN:ROLE'  => new RoleInEvent(I18N::translate('Role')),
            'FAM:*:_ASSO:SOUR:NOTE'       => new NoteStructure(I18N::translate('Note')),
            'FAM:*:_ASSO:SOUR:OBJE'       => new XrefMedia(I18N::translate('Media object')),
            'FAM:*:_ASSO:SOUR:PAGE'       => new WhereWithinSource(I18N::translate('Citation details')),
            'FAM:*:_ASSO:SOUR:QUAY'       => new CertaintyAssessment(I18N::translate('Quality of data')),
            'INDI:CHAN:_WT_USER'          => new WebtreesUser(I18N::translate('Author of last change')),
            'INDI:*:_ASSO'                => new XrefAssociate(I18N::translate('Associate')),
            'INDI:*:_ASSO:NOTE'           => new NoteStructure(I18N::translate('Note')),
            'INDI:*:_ASSO:RELA'           => new RelationIsDescriptor(I18N::translate('Relationship')),
            'INDI:*:_ASSO:SOUR'           => new XrefSource(I18N::translate('Source citation')),
            'INDI:*:_ASSO:SOUR:DATA'      => new SourceData(I18N::translate('Data')),
            'INDI:*:_ASSO:SOUR:DATA:DATE' => new EntryRecordingDate(I18N::translate('Date of entry in original source')),
            'INDI:*:_ASSO:SOUR:DATA:TEXT' => new TextFromSource(I18N::translate('Text')),
            'INDI:*:_ASSO:SOUR:EVEN'      => new EventTypeCitedFrom(I18N::translate('Event')),
            'INDI:*:_ASSO:SOUR:EVEN:ROLE' => new RoleInEvent(I18N::translate('Role')),
            'INDI:*:_ASSO:SOUR:NOTE'      => new NoteStructure(I18N::translate('Note')),
            'INDI:*:_ASSO:SOUR:OBJE'      => new XrefMedia(I18N::translate('Media object')),
            'INDI:*:_ASSO:SOUR:PAGE'      => new WhereWithinSource(I18N::translate('Citation details')),
            'INDI:*:_ASSO:SOUR:QUAY'      => new CertaintyAssessment(I18N::translate('Quality of data')),
            'NOTE:CHAN:_WT_USER'          => new WebtreesUser(I18N::translate('Author of last change')),
            'NOTE:RESN'                   => new RestrictionNotice(I18N::translate('Restriction')),
            'OBJE:CHAN:_WT_USER'          => new WebtreesUser(I18N::translate('Author of last change')),
            'OBJE:RESN'                   => new RestrictionNotice(I18N::translate('Restriction')),
            'REPO:CHAN:_WT_USER'          => new WebtreesUser(I18N::translate('Author of last change')),
            'REPO:RESN'                   => new RestrictionNotice(I18N::translate('Restriction')),
            'SOUR:CHAN:_WT_USER'          => new WebtreesUser(I18N::translate('Author of last change')),
            'SOUR:RESN'                   => new RestrictionNotice(I18N::translate('Restriction')),
            'SUBM:CHAN:_WT_USER'          => new WebtreesUser(I18N::translate('Author of last change')),
            'SUBM:RESN'                   => new RestrictionNotice(I18N::translate('Restriction')),
            '_LOC:CHAN:_WT_USER'          => new WebtreesUser(I18N::translate('Author of last change')),
            '_LOC:RESN'                   => new RestrictionNotice(I18N::translate('Restriction')),
        ];
    }

    /**
     * @return array<string,array<int,array<int,string>>>
     */
    public function customSubTags(): array
    {
        return [
            'FAM:*:SOUR'       => [['NOTE', '0:0']],
            'FAM:*:SOUR:DATA'  => [['TEXT']],
            'FAM:ANUL'         => [['_ASSO', '0:M', 'NOTE']],
            'FAM:CENS'         => [['_ASSO', '0:M', 'NOTE']],
            'FAM:CHAN'         => [['_WT_USER']],
            'FAM:DIV'          => [['_ASSO', '0:M', 'NOTE']],
            'FAM:DIVF'         => [['_ASSO', '0:M', 'NOTE']],
            'FAM:ENGA'         => [['_ASSO', '0:M', 'NOTE']],
            'FAM:EVEN'         => [['_ASSO', '0:M', 'NOTE']],
            'FAM:MARB'         => [['_ASSO', '0:M', 'NOTE']],
            'FAM:MARC'         => [['_ASSO', '0:M', 'NOTE']],
            'FAM:MARL'         => [['_ASSO', '0:M', 'NOTE']],
            'FAM:MARR'         => [['_ASSO', '0:M', 'NOTE']],
            'FAM:MARS'         => [['_ASSO', '0:M', 'NOTE']],
            'FAM:SLGS'         => [['_ASSO', '0:M', 'NOTE']],
            'FAM:SOUR:DATA'    => [['TEXT']],
            'INDI:*:SOUR'      => [['NOTE', '0:0']],
            'INDI:*:SOUR:DATA' => [['TEXT']],
            'INDI:ADOP'        => [['_ASSO', '0:M', 'NOTE']],
            'INDI:BAPL'        => [['_ASSO', '0:M', 'NOTE']],
            'INDI:BAPM'        => [['_ASSO', '0:M', 'NOTE']],
            'INDI:BARM'        => [['_ASSO', '0:M', 'NOTE']],
            'INDI:BASM'        => [['_ASSO', '0:M', 'NOTE']],
            'INDI:BIRT'        => [['_ASSO', '0:M', 'NOTE'], ['FAMC', '0:0']],
            'INDI:BURI'        => [['_ASSO', '0:M', 'NOTE']],
            'INDI:CENS'        => [['_ASSO', '0:M', 'NOTE']],
            'INDI:CHAN'        => [['_WT_USER']],
            'INDI:CHR'         => [['_ASSO', '0:M', 'NOTE']],
            'INDI:CHRA'        => [['_ASSO', '0:M', 'NOTE']],
            'INDI:CONF'        => [['_ASSO', '0:M', 'NOTE']],
            'INDI:CONL'        => [['_ASSO', '0:M', 'NOTE']],
            'INDI:CREM'        => [['_ASSO', '0:M', 'NOTE']],
            'INDI:DEAT'        => [['_ASSO', '0:M', 'NOTE']],
            'INDI:EDUC'        => [['_ASSO', '0:M', 'NOTE']],
            'INDI:EMIG'        => [['_ASSO', '0:M', 'NOTE']],
            'INDI:ENDL'        => [['_ASSO', '0:M', 'NOTE']],
            'INDI:EVEN'        => [['_ASSO', '0:M', 'NOTE']],
            'INDI:GRAD'        => [['_ASSO', '0:M', 'NOTE']],
            'INDI:IMMI'        => [['_ASSO', '0:M', 'NOTE']],
            'INDI:NATU'        => [['_ASSO', '0:M', 'NOTE']],
            'INDI:NAME:FONE'   => [['NPFX', '0:0'], ['GIVN', '0:0'], ['SPFX', '0:0'], ['SURN', '0:0'], ['NSFX', '0:0'], ['NICK', '0:0']],
            'INDI:NAME:ROMN'   => [['NPFX', '0:0'], ['GIVN', '0:0'], ['SPFX', '0:0'], ['SURN', '0:0'], ['NSFX', '0:0'], ['NICK', '0:0']],
            'INDI:OCCU'        => [['_ASSO', '0:M', 'NOTE']],
            'INDI:ORDN'        => [['_ASSO', '0:M', 'NOTE']],
            'INDI:PROB'        => [['_ASSO', '0:M', 'NOTE']],
            'INDI:PROP'        => [['_ASSO', '0:M', 'NOTE']],
            'INDI:RESI'        => [['_ASSO', '0:M', 'NOTE']],
            'INDI:RETI'        => [['_ASSO', '0:M', 'NOTE']],
            'INDI:SLGC'        => [['_ASSO', '0:M', 'NOTE']],
            'INDI:SOUR:DATA'   => [['TEXT']],
            'INDI:TITL'        => [['_ASSO', '0:M', 'NOTE']],
            'INDI:WILL'        => [['_ASSO', '0:M', 'NOTE']],
            'NOTE'             => [['RESN', '0:1', 'CHAN']],
            'NOTE:CHAN'        => [['_WT_USER']],
            'NOTE:SOUR'        => [['NOTE', '0:0']],
            'NOTE:SOUR:DATA'   => [['TEXT']],
            'OBJE'             => [['RESN', '0:1', 'CHAN']],
            'OBJE:CHAN'        => [['_WT_USER']],
            'OBJE:SOUR'        => [['NOTE', '0:0']],
            'OBJE:SOUR:DATA'   => [['TEXT']],
            'REPO'             => [['RESN', '0:1', 'CHAN']],
            'REPO:CHAN'        => [['_WT_USER']],
            'SOUR'             => [['RESN', '0:1', 'CHAN']],
            'SOUR:CHAN'        => [['_WT_USER']],
            'SUBM'             => [['RESN', '0:1', 'CHAN']],
            'SUBM:CHAN'        => [['_WT_USER']],
        ];
    }

    /**
     * The application for which we are supporting custom tags.
     *
     * @return string
     */
    public function customTagApplication(): string
    {
        return 'webtrees™';
    }
}
