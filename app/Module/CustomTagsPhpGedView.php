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
use Fisharebest\Webtrees\Elements\AddressEmail;
use Fisharebest\Webtrees\Elements\AddressLine;
use Fisharebest\Webtrees\Elements\AddressWebPage;
use Fisharebest\Webtrees\Elements\CustomElement;
use Fisharebest\Webtrees\Elements\CustomEvent;
use Fisharebest\Webtrees\Elements\NamePersonal;
use Fisharebest\Webtrees\Elements\NoteStructure;
use Fisharebest\Webtrees\Elements\RelationIsDescriptor;
use Fisharebest\Webtrees\Elements\RestrictionNotice;
use Fisharebest\Webtrees\Elements\TimeValue;
use Fisharebest\Webtrees\Elements\WebtreesUser;
use Fisharebest\Webtrees\Elements\XrefAssociate;
use Fisharebest\Webtrees\Elements\XrefMedia;
use Fisharebest\Webtrees\I18N;

/**
 * Class CustomTagsPhpGedView
 */
class CustomTagsPhpGedView extends AbstractModule implements ModuleConfigInterface, ModuleCustomTagsInterface
{
    use ModuleConfigTrait;
    use ModuleCustomTagsTrait;

    /**
     * @return array<string,ElementInterface>
     */
    public function customTags(): array
    {
        return [
            'FAM:CHAN:_PGVU'        => new WebtreesUser(I18N::translate('Author of last change')),
            'FAM:COMM'              => new CustomElement(I18N::translate('Comment')),
            'INDI:*:ASSO'           => new XrefAssociate(I18N::translate('Associate')),
            'INDI:*:ASSO:RELA'      => new RelationIsDescriptor(I18N::translate('Relationship')),
            'INDI:*:PLAC:_HEB'      => new NoteStructure(I18N::translate('Place in Hebrew')),
            'INDI:ADDR'             => new AddressLine(I18N::translate('Address')),
            'INDI:BIRT:DATE:TIME'   => new TimeValue(I18N::translate('Time')),
            'INDI:BURI:CEME'        => new CustomElement(I18N::translate('Cemetery')),
            'INDI:CHAN:_PGVU'       => new WebtreesUser(I18N::translate('Author of last change')),
            'INDI:COMM'             => new CustomElement(I18N::translate('Comment')),
            'INDI:DEAT:DATE:TIME'   => new TimeValue(I18N::translate('Time')),
            'INDI:EMAIL'            => new AddressEmail(I18N::translate('Email address')),
            'INDI:NAME:_HEB'        => new NamePersonal(I18N::translate('Name in Hebrew'), []),
            'INDI:_FNRL'            => new CustomEvent(I18N::translate('Funeral')),
            'INDI:_HOL'             => new CustomEvent(I18N::translate('Holocaust')),
            'INDI:_MILI'            => new CustomEvent(I18N::translate('Military')),
            'INDI:_PGV_OBJS'        => new XrefMedia(I18N::translate('Re-order media')),
            'NOTE:CHAN:_PGVU'       => new WebtreesUser(I18N::translate('Author of last change')),
            'OBJE:CHAN:_PGVU'       => new WebtreesUser(I18N::translate('Author of last change')),
            'OBJE:_PRIM'            => new CustomElement(I18N::translate('Highlighted image')),
            'OBJE:_THUM'            => new CustomElement(I18N::translate('Thumbnail image')),
            'REPO:CHAN:_PGVU'       => new WebtreesUser(I18N::translate('Author of last change')),
            'SOUR:CHAN:_PGVU'       => new WebtreesUser(I18N::translate('Author of last change')),
            'SOUR:SERV'             => new CustomElement(I18N::translate('Remote server')),
            'SOUR:URL'              => new AddressWebPage(I18N::translate('URL')),
            'SOUR:URL:TYPE'         => new CustomElement(I18N::translate('Type')), // e.g. "FamilySearch"
            'SOUR:URL:_BLOCK'       => new CustomElement(I18N::translate('Block')), // "e.g. "false"
            'SOUR:_DBID'            => new CustomElement(I18N::translate('Database name')),
            'SOUR:_DBID:_PASS'      => new CustomElement(I18N::translate('Database password')),
            'SOUR:_DBID:_PASS:RESN' => new RestrictionNotice(I18N::translate('Restriction')),
            'SOUR:_DBID:_USER'      => new CustomElement(I18N::translate('Database user account')),
        ];
    }

    /**
     * The application for which we are supporting custom tags.
     *
     * @return string
     */
    public function customTagApplication(): string
    {
        return 'PhpGedView™';
    }
}
