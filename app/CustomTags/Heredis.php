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
use Fisharebest\Webtrees\Elements\AddressEmail;
use Fisharebest\Webtrees\Elements\AddressWebPage;
use Fisharebest\Webtrees\Elements\AgeAtEvent;
use Fisharebest\Webtrees\Elements\CertaintyAssessment;
use Fisharebest\Webtrees\Elements\Coordinates;
use Fisharebest\Webtrees\Elements\Creation;
use Fisharebest\Webtrees\Elements\CustomBooleanFact;
use Fisharebest\Webtrees\Elements\CustomElement;
use Fisharebest\Webtrees\Elements\DateValueExact;
use Fisharebest\Webtrees\Elements\EmptyElement;
use Fisharebest\Webtrees\Elements\HeredisFIL;
use Fisharebest\Webtrees\Elements\HeredisFNA;
use Fisharebest\Webtrees\Elements\HeredisQualEvid;
use Fisharebest\Webtrees\Elements\HeredisQualInfo;
use Fisharebest\Webtrees\Elements\HeredisQualSour;
use Fisharebest\Webtrees\Elements\HeredisRechElement;
use Fisharebest\Webtrees\Elements\HeredisUST;
use Fisharebest\Webtrees\Elements\NoteStructure;
use Fisharebest\Webtrees\Elements\PafUid;
use Fisharebest\Webtrees\Elements\PlaceLatitude;
use Fisharebest\Webtrees\Elements\PlaceLongtitude;
use Fisharebest\Webtrees\Elements\PlaceName;
use Fisharebest\Webtrees\Elements\RoleInEvent;
use Fisharebest\Webtrees\Elements\SourceCallNumber;
use Fisharebest\Webtrees\Elements\TimeValue;
use Fisharebest\Webtrees\Elements\UserReferenceNumber;
use Fisharebest\Webtrees\I18N;

/**
 * GEDCOM files created by Heredis
 *
 * @see https://www.heredis.com
 *
 * Reference: https://help.heredis.com/les-tags-gedcom/ and https://help.heredis.com/en/gedcom-tags/
 * Example GEDCOM fragment with lots of custom tags: https://www.geneanet.org/forum/viewtopic.php?p=2441735
 */
class Heredis implements CustomTagInterface
{
    /**
     * The name of the application.
     *
     * @return string
     */
    public function name(): string
    {
        return 'Heredis';
    }

    /**
     * Tags created by this application.
     *
     * @return array<string,ElementInterface>
     */
    public function tags(): array
    {
        return [
            'HEAD:_GUID'                  => new PafUid(I18N::translate('File identifier')),
            'FAM:_CREA'                   => new Creation(I18N::translate('Created at')),
            'FAM:_UST'                    => new HeredisUST(I18N::translate('Union type')),
            'FAM:*:DATE:_TIME'            => new TimeValue(I18N::translate('Time')),
            'FAM:*:PLAC:_SUBMAP'          => new Coordinates(I18N::translate('Geolocation of subdivisions')),
            'FAM:*:PLAC:_SUBMAP:LATI'     => new PlaceLatitude(I18N::translate('Latitude of subdivision')),
            'FAM:*:PLAC:_SUBMAP:LONG'     => new PlaceLongtitude(I18N::translate('Longitude of subdivision')),
            'FAM:*:SOUR:_QUAL'            => new EmptyElement(I18N::translate('Quality of completed citation')),
            'FAM:*:SOUR:_QUAL:_SOUR'      => new HeredisQualSour(I18N::translate('Quality of the source')),
            'FAM:*:SOUR:_QUAL:_INFO'      => new HeredisQualInfo(I18N::translate('Quality of the information')),
            'FAM:*:SOUR:_QUAL:_EVID'      => new HeredisQualEvid(I18N::translate('Quality of the evidence')),
            'FAM:*:_RECH'                 => new EmptyElement(I18N::translate('Research data')),
            'FAM:*:_RECH:_PROJ'           => new CustomElement(I18N::translate('Research project')),
            'FAM:*:_RECH:TYPE'            => new CustomElement(I18N::translate('Document')),
            'FAM:*:_RECH:PLAC'            => new PlaceName(I18N::translate('Search place')),
            'FAM:*:_RECH:DATE'            => new DateValueExact(I18N::translate('Search date')),
            'FAM:*:_RECH:REFN'            => new UserReferenceNumber(I18N::translate('Call number')),
            'FAM:*:_RECH:WWW'             => new AddressWebPage(I18N::translate('URL')),
            'FAM:*:_RECH:NOTE'            => new NoteStructure(I18N::translate('Note')),
            'INDI:_CLS'                   => new CustomBooleanFact(I18N::translate('Childless')),
            'INDI:_CREA'                  => new Creation(I18N::translate('Created at')),
            'INDI:_FIL'                   => new HeredisFIL(I18N::translate('Child status')),
            'INDI:_ETI'                   => new CustomElement(I18N::translate('Personalized flag')),
            'INDI:_FNF'                   => new CustomBooleanFact(I18N::translate('Untraceable father')),
            'INDI:_MNF'                   => new CustomBooleanFact(I18N::translate('Untraceable mother')),
            'INDI:_SEC'                   => new CustomBooleanFact(I18N::translate('Secondary person')),
            'INDI:_ULS'                   => new CustomBooleanFact(I18N::translate('Unmarried')),
            'INDI:SIGN'                   => new CustomBooleanFact(I18N::translate('Signature')),
            'INDI:ASSO:_AGE'              => new AgeAtEvent(I18N::translate('Age')),
            'INDI:ASSO:_ROLE'             => new RoleInEvent(I18N::translate('Role')),
            'INDI:ASSO:_TITL'             => new CustomElement(I18N::translate('Title')),
            'INDI:ASSO:_TYPE'             => new CustomElement(I18N::translate('Type')),
            'INDI:*:DATE:_TIME'           => new TimeValue(I18N::translate('Time')),
            'INDI:*:PLAC:_SUBMAP'         => new Coordinates(I18N::translate('Geolocation of subdivisions')),
            'INDI:*:PLAC:_SUBMAP:LATI'    => new PlaceLatitude(I18N::translate('Latitude of subdivision')),
            'INDI:*:PLAC:_SUBMAP:LONG'    => new PlaceLongtitude(I18N::translate('Longitude of subdivision')),
            'INDI:*:SOUR:_QUAL'           => new EmptyElement(I18N::translate('Quality of completed citation')),
            'INDI:*:SOUR:_QUAL:_SOUR'     => new HeredisQualSour(I18N::translate('Quality of the source')),
            'INDI:*:SOUR:_QUAL:_INFO'     => new HeredisQualInfo(I18N::translate('Quality of the information')),
            'INDI:*:SOUR:_QUAL:_EVID'     => new HeredisQualEvid(I18N::translate('Quality of proof')),
            'INDI:*:_ETI'                 => new CustomElement(I18N::translate('Personalized flag')),
            'INDI:*:_FNA'                 => new HeredisFNA(I18N::translate('Research status')),
            'INDI:*:_RECH'                => new HeredisRechElement(I18N::translate('Research data')),
            'INDI:*:_RECH:_PROJ'          => new CustomElement(I18N::translate('Research project')),
            'INDI:*:_RECH:TYPE'           => new CustomElement(I18N::translate('Document')),
            'INDI:*:_RECH:PLAC'           => new PlaceName(I18N::translate('Search place')),
            'INDI:*:_RECH:DATE'           => new DateValueExact(I18N::translate('Search date')),
            'INDI:*:_RECH:REFN'           => new SourceCallNumber(I18N::translate('Call number')),
            'INDI:*:_RECH:WWW'            => new AddressWebPage(I18N::translate('URL')),
            'INDI:*:_RECH:NOTE'           => new NoteStructure(I18N::translate('Note')),
            'NOTE:_CREA'                  => new Creation(I18N::translate('Created at')),
            'OBJE:_CREA'                  => new Creation(I18N::translate('Created at')),
            'REPO:_CREA'                  => new Creation(I18N::translate('Created at')),
            'SOUR:EMAIL'                  => new AddressEmail(I18N::translate('Email address')),
            'SOUR:QUAY'                   => new CertaintyAssessment(I18N::translate('Quality of data')),
            'SOUR:TYPE'                   => new CustomElement(I18N::translate('Type')),
            'SOUR:_ARCH'                  => new CustomElement(I18N::translate('Archive')),
            'SOUR:_CREA'                  => new Creation(I18N::translate('Created at')),
        ];
    }
}
