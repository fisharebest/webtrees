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
            'FAM:*:SOUR'     => [['NOTE', '0:0']],
            'FAM:CHAN'       => [['_WT_USER']],
            'FAM:MARR'       => [['_ASSO', '0:M', 'NOTE']],
            'FAM:SOUR:DATA'  => [['TEXT']],
            'INDI:*:SOUR'    => [['NOTE', '0:0']],
            'INDI:BIRT'      => [['FAMC', '0:0']],
            'INDI:CHAN'      => [['_WT_USER']],
            'INDI:SOUR:DATA' => [['TEXT']],
            'NOTE'           => [['RESN', '0:1', 'CHAN']],
            'NOTE:*:SOUR'    => [['NOTE', '0:0']],
            'NOTE:CHAN'      => [['_WT_USER']],
            'OBJE'           => [['RESN', '0:1', 'CHAN']],
            'OBJE:*:SOUR'    => [['NOTE', '0:0']],
            'OBJE:CHAN'      => [['_WT_USER']],
            'REPO'           => [['RESN', '0:1', 'CHAN']],
            'REPO:CHAN'      => [['_WT_USER']],
            'SOUR'           => [['RESN', '0:1', 'CHAN']],
            'SOUR:CHAN'      => [['_WT_USER']],
            'SUBM'           => [['RESN', '0:1', 'CHAN']],
            'SUBM:CHAN'      => [['_WT_USER']],
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
