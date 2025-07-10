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
use Fisharebest\Webtrees\Elements\HeredisUST;
use Fisharebest\Webtrees\Elements\NoteStructure;
use Fisharebest\Webtrees\Elements\PafUid;
use Fisharebest\Webtrees\Elements\PlaceLatitude;
use Fisharebest\Webtrees\Elements\PlaceLongtitude;
use Fisharebest\Webtrees\Elements\PlaceName;
use Fisharebest\Webtrees\Elements\RoleInEvent;
use Fisharebest\Webtrees\Elements\TimeValue;
use Fisharebest\Webtrees\Elements\UserReferenceNumber;
use Fisharebest\Webtrees\I18N;

/**
 * GEDCOM files created by Heredis
 *
 * @see https://www.heredis.com
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
     * Heredis is predominantly used in France, so keeping unique tag labels in French is a service to its users.
     *
     * Reference: https://help.heredis.com/les-tags-gedcom/ and https://help.heredis.com/en/gedcom-tags/
     * Example GEDCOM fragment with lots of custom tags: https://www.geneanet.org/forum/viewtopic.php?p=2441735
     * *
     * @return array<string,ElementInterface>
     */
    public function tags(): array
    {
        return [
            'HEAD:_GUID'                  => new PafUid(I18N::translate('Identifiant du fichier')),
            'FAM:_CREA'                   => new Creation(I18N::translate('Created at')),
            'FAM:_UST'                    => new HeredisUST(I18N::translate('Statut d’union')),                           // Union type
            'FAM:*:DATE:_TIME'            => new TimeValue(I18N::translate('Time')),
            'FAM:*:PLAC:_SUBMAP'          => new Coordinates(I18N::translate('Géolocalisation des subdivisions')),        // Geolocation of subdivisions
            'FAM:*:PLAC:_SUBMAP:LATI'     => new PlaceLatitude(I18N::translate('Latitude') . ' des subdivisions'),
            'FAM:*:PLAC:_SUBMAP:LONG'     => new PlaceLongtitude(I18N::translate('Longitude') . ' des subdivisions'),
            'FAM:*:SOUR:_QUAL'            => new EmptyElement(I18N::translate('Qualité des citations complètes')),        // Quality of completed citations
            'FAM:*:SOUR:_QUAL:_SOUR'      => new HeredisQualSour(I18N::translate('Qualité de la source')),                // Quality of the source
            'FAM:*:SOUR:_QUAL:_INFO'      => new HeredisQualInfo(I18N::translate('Qualité de l’information')),            // Quality of the information
            'FAM:*:SOUR:_QUAL:_EVID'      => new HeredisQualEvid(I18N::translate('Qualité de la preuve')),                // Quality of proof
            'FAM:*:_RECH'                 => new EmptyElement(I18N::translate('Infos de recherche de l’événement')),      // Research Data of an event
            'FAM:*:_RECH:_PROJ'           => new CustomElement(I18N::translate('Projet de recherche')),                   // Research Data Project
            'FAM:*:_RECH:TYPE'            => new CustomElement(I18N::translate('Document de recherche')),                 // Search Document type of Search Data tab
            'FAM:*:_RECH:PLAC'            => new PlaceName(I18N::translate('Lieu de recherche')),                         // Search Place of Search Data tab
            'FAM:*:_RECH:DATE'            => new DateValueExact(I18N::translate('Date de recherche')),                    // Search date of Search Data tab
            'FAM:*:_RECH:REFN'            => new UserReferenceNumber(I18N::translate('Référence de la recherche')),       // Search data call number
            'FAM:*:_RECH:WWW'             => new AddressWebPage(I18N::translate('Site web de la recherche')),             // Search data website field
            'FAM:*:_RECH:NOTE'            => new NoteStructure(I18N::translate('Note de recherche')),                     // Research note
            'INDI:_CLS'                   => new CustomBooleanFact(I18N::translate('Individu sans postérité')),           // Person without descendants
            'INDI:_CREA'                  => new Creation(I18N::translate('Created at')),
            'INDI:_FIL'                   => new HeredisFIL(I18N::translate('Filiation de l’individu')),                  // Child Status
            'INDI:_FNF'                   => new CustomBooleanFact(I18N::translate('Père introuvable')),                  // Father not found, untraceable
            'INDI:_MNF'                   => new CustomBooleanFact(I18N::translate('Mère introuvable')),                  // Mother not found, untraceable
            'INDI:_SEC'                   => new CustomBooleanFact(I18N::translate('Individu secondaire')),               // Secondary person
            'INDI:_ULS'                   => new CustomBooleanFact(I18N::translate('Individu sans alliance')),            // Unmarried person
            'INDI:SIGN'                   => new CustomBooleanFact(I18N::translate('Signature')),
            'INDI:ASSO:_AGE'              => new AgeAtEvent(I18N::translate('Age')),
            'INDI:ASSO:_ROLE'             => new RoleInEvent(I18N::translate('Role')),
            'INDI:ASSO:_TITL'             => new CustomElement(I18N::translate('Title')),
            'INDI:ASSO:_TYPE'             => new CustomElement(I18N::translate('Type')),
            'INDI:*:DATE:_TIME'           => new TimeValue(I18N::translate('Time')),
            'INDI:*:PLAC:_SUBMAP'         => new Coordinates(I18N::translate('Géolocalisation des subdivisions')),        // Geolocation of subdivisions
            'INDI:*:PLAC:_SUBMAP:LATI'    => new PlaceLatitude(I18N::translate('Latitude') . ' des subdivisions'),
            'INDI:*:PLAC:_SUBMAP:LONG'    => new PlaceLongtitude(I18N::translate('Longitude') . ' des subdivisions'),
            'INDI:*:SOUR:_QUAL'           => new EmptyElement(I18N::translate('Qualité des citations complètes')),        // Quality of completed citations
            'INDI:*:SOUR:_QUAL:_SOUR'     => new HeredisQualSour(I18N::translate('Qualité de la source')),                // Quality of the source
            'INDI:*:SOUR:_QUAL:_INFO'     => new HeredisQualInfo(I18N::translate('Qualité de l’information')),            // Quality of the information
            'INDI:*:SOUR:_QUAL:_EVID'     => new HeredisQualEvid(I18N::translate('Qualité de la preuve')),                // Quality of proof
            'INDI:*:_ETI'                 => new CustomElement(I18N::translate('Etiquettes personnalisées')),             // Personalized flags
            'INDI:*:_FNA'                 => new HeredisFNA(I18N::translate('Etat des recherches d’un événement')),       // Research Status of an event
            'INDI:*:_RECH'                => new EmptyElement(I18N::translate('Infos de recherche de l’événement')),      // Research Data of an event
            'INDI:*:_RECH:_PROJ'          => new CustomElement(I18N::translate('Projet de recherche')),                   // Research Data Project
            'INDI:*:_RECH:TYPE'           => new CustomElement(I18N::translate('Document de recherche')),                 // Search Document type of Search Data tab
            'INDI:*:_RECH:PLAC'           => new PlaceName(I18N::translate('Lieu de recherche')),                         // Search Place of Search Data tab
            'INDI:*:_RECH:DATE'           => new DateValueExact(I18N::translate('Date de recherche')),                    // Search date of Search Data tab
            'INDI:*:_RECH:REFN'           => new UserReferenceNumber(I18N::translate('Référence de la recherche')),       // Search data call number
            'INDI:*:_RECH:WWW'            => new AddressWebPage(I18N::translate('Site web de la recherche')),             // Search data website field
            'INDI:*:_RECH:NOTE'           => new NoteStructure(I18N::translate('Note de recherche')),                     // Research note
            'NOTE:_CREA'                  => new Creation(I18N::translate('Created at')),
            'OBJE:_CREA'                  => new Creation(I18N::translate('Created at')),
            'REPO:_CREA'                  => new Creation(I18N::translate('Created at')),
            'SOUR:EMAIL'                  => new AddressEmail(I18N::translate('Email address')),
            'SOUR:QUAY'                   => new CertaintyAssessment(I18N::translate('Quality of data')),
            'SOUR:TYPE'                   => new CustomElement(I18N::translate('Type')),
            'SOUR:_ARCH'                  => new CustomElement(I18N::translate('Classement')),                            // Archive
            'SOUR:_CREA'                  => new Creation(I18N::translate('Created at')),
        ];
    }
}
