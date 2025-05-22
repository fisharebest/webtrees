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
use Fisharebest\Webtrees\Elements\AddressCity;
use Fisharebest\Webtrees\Elements\AddressCountry;
use Fisharebest\Webtrees\Elements\AddressEmail;
use Fisharebest\Webtrees\Elements\AddressFax;
use Fisharebest\Webtrees\Elements\AddressLine;
use Fisharebest\Webtrees\Elements\AddressLine1;
use Fisharebest\Webtrees\Elements\AddressLine2;
use Fisharebest\Webtrees\Elements\AddressLine3;
use Fisharebest\Webtrees\Elements\AddressPostalCode;
use Fisharebest\Webtrees\Elements\AddressState;
use Fisharebest\Webtrees\Elements\AddressWebPage;
use Fisharebest\Webtrees\Elements\CauseOfEvent;
use Fisharebest\Webtrees\Elements\CertaintyAssessment;
use Fisharebest\Webtrees\Elements\Change;
use Fisharebest\Webtrees\Elements\ChangeDate;
use Fisharebest\Webtrees\Elements\CustomElement;
use Fisharebest\Webtrees\Elements\DateValue;
use Fisharebest\Webtrees\Elements\DateValueToday;
use Fisharebest\Webtrees\Elements\EmptyElement;
use Fisharebest\Webtrees\Elements\EventAttributeType;
use Fisharebest\Webtrees\Elements\EventTypeCitedFrom;
use Fisharebest\Webtrees\Elements\FamilyStatusText;
use Fisharebest\Webtrees\Elements\GovIdentifier;
use Fisharebest\Webtrees\Elements\HierarchicalRelationship;
use Fisharebest\Webtrees\Elements\LanguageId;
use Fisharebest\Webtrees\Elements\LocationRecord;
use Fisharebest\Webtrees\Elements\MaidenheadLocator;
use Fisharebest\Webtrees\Elements\NamePieceGiven;
use Fisharebest\Webtrees\Elements\NoteStructure;
use Fisharebest\Webtrees\Elements\PafUid;
use Fisharebest\Webtrees\Elements\PhoneNumber;
use Fisharebest\Webtrees\Elements\PhoneticType;
use Fisharebest\Webtrees\Elements\PlaceHierarchy;
use Fisharebest\Webtrees\Elements\PlaceLatitude;
use Fisharebest\Webtrees\Elements\PlaceLongtitude;
use Fisharebest\Webtrees\Elements\PlaceName;
use Fisharebest\Webtrees\Elements\PlacePhoneticVariation;
use Fisharebest\Webtrees\Elements\PlaceRomanizedVariation;
use Fisharebest\Webtrees\Elements\RelationIsDescriptor;
use Fisharebest\Webtrees\Elements\ReligiousAffiliation;
use Fisharebest\Webtrees\Elements\ResearchTask;
use Fisharebest\Webtrees\Elements\ResearchTaskPriority;
use Fisharebest\Webtrees\Elements\ResearchTaskStatus;
use Fisharebest\Webtrees\Elements\ResearchTaskType;
use Fisharebest\Webtrees\Elements\ResponsibleAgency;
use Fisharebest\Webtrees\Elements\RestrictionNotice;
use Fisharebest\Webtrees\Elements\RoleInEvent;
use Fisharebest\Webtrees\Elements\RomanizedType;
use Fisharebest\Webtrees\Elements\SexXValue;
use Fisharebest\Webtrees\Elements\SourceData;
use Fisharebest\Webtrees\Elements\SubmitterText;
use Fisharebest\Webtrees\Elements\TextFromSource;
use Fisharebest\Webtrees\Elements\TimeValueNow;
use Fisharebest\Webtrees\Elements\VersionNumber;
use Fisharebest\Webtrees\Elements\WhereWithinSource;
use Fisharebest\Webtrees\Elements\XrefAssociate;
use Fisharebest\Webtrees\Elements\XrefLocation;
use Fisharebest\Webtrees\Elements\XrefMedia;
use Fisharebest\Webtrees\Elements\XrefRepository;
use Fisharebest\Webtrees\Elements\XrefSource;
use Fisharebest\Webtrees\I18N;

/**
 * GEDCOM files created by Gedcom-L
 *
 * @see https://www.genwiki.de/GEDCOM-L
 */
class GedcomL implements CustomTagInterface
{
    /**
     * The name of the application.
     *
     * @return string
     */
    public function name(): string
    {
        return 'GEDCOM-L';
    }

    /**
     * Tags created by this application.
     *
     * @return array<string,ElementInterface>
     */
    public function tags(): array
    {
        return [
            'FAM:*:ADDR:_NAME'                => new CustomElement(I18N::translate('Name of addressee')),
            // I18N: https://gov.genealogy.net
            'FAM:*:PLAC:_GOV'                 => new GovIdentifier(I18N::translate('GOV identifier')),
            'FAM:*:PLAC:_LOC'                 => new XrefLocation(I18N::translate('Location')),
            // I18N: https://en.wikipedia.org/wiki/Maidenhead_Locator_System
            'FAM:*:PLAC:_MAIDENHEAD'          => new MaidenheadLocator(I18N::translate('Maidenhead location code')),
            'FAM:*:PLAC:_POST'                => new AddressPostalCode(I18N::translate('Postal code')),
            'FAM:*:PLAC:_POST:DATE'           => new DateValue(I18N::translate('Date')),
            'FAM:*:_ASSO'                     => new XrefAssociate(I18N::translate('Associate')),
            'FAM:*:_ASSO:NOTE'                => new NoteStructure(I18N::translate('Note on association')),
            'FAM:*:_ASSO:RELA'                => new RelationIsDescriptor(I18N::translate('Relationship')),
            'FAM:*:_ASSO:SOUR'                => new XrefSource(I18N::translate('Source citation')),
            'FAM:*:_ASSO:SOUR:DATA'           => new SourceData(I18N::translate('Data')),
            'FAM:*:_ASSO:SOUR:DATA:DATE'      => new DateValue(I18N::translate('Date of entry in original source')),
            'FAM:*:_ASSO:SOUR:DATA:TEXT'      => new TextFromSource(I18N::translate('Text')),
            'FAM:*:_ASSO:SOUR:EVEN'           => new EventTypeCitedFrom(I18N::translate('Event')),
            'FAM:*:_ASSO:SOUR:EVEN:ROLE'      => new RoleInEvent(I18N::translate('Role')),
            'FAM:*:_ASSO:SOUR:NOTE'           => new NoteStructure(I18N::translate('Note on source citation')),
            'FAM:*:_ASSO:SOUR:OBJE'           => new XrefMedia(I18N::translate('Media object')),
            'FAM:*:_ASSO:SOUR:PAGE'           => new WhereWithinSource(I18N::translate('Citation details')),
            'FAM:*:_ASSO:SOUR:QUAY'           => new CertaintyAssessment(I18N::translate('Quality of data')),
            'FAM:*:_WITN'                     => new CustomElement(I18N::translate('Witnesses')),
            'FAM:_ASSO'                       => new XrefAssociate(I18N::translate('Associate')),
            'FAM:_ASSO:RELA'                  => new RelationIsDescriptor(I18N::translate('Relationship')),
            'FAM:_STAT'                       => new FamilyStatusText(I18N::translate('Family status')),
            'FAM:_TODO'                       => new ResearchTask(I18N::translate('Research task'), ['DESC' => '1:1', '_CAT' => '0:1', '_PRTY' => '0:1', 'TYPE' => '0:1', 'NOTE' => '0:M', 'DATA' => '0:1', 'STAT'  => '0:1', '_CDATE' => '0:1', '_RDATE' => '0:1', 'REPO' => '0:1', '_UID' => '0:M']),
            'FAM:_TODO:DATA'                  => new SubmitterText(I18N::translate('The solution')),
            'FAM:_TODO:DATE'                  => new DateValueToday(I18N::translate('Creation date')),
            'FAM:_TODO:DESC'                  => new CustomElement(I18N::translate('Description')),
            'FAM:_TODO:NOTE'                  => new NoteStructure(I18N::translate('Note')),
            'FAM:_TODO:REPO'                  => new XrefRepository(I18N::translate('Repository'), []),
            'FAM:_TODO:STAT'                  => new ResearchTaskStatus(I18N::translate('Status')),
            'FAM:_TODO:TYPE'                  => new ResearchTaskType(I18N::translate('Type of research task')),
            'FAM:_TODO:_CAT'                  => new CustomElement(I18N::translate('Category')),
            'FAM:_TODO:_CDATE'                => new DateValue(I18N::translate('Completion date')),
            'FAM:_TODO:_PRTY'                 => new ResearchTaskPriority(I18N::translate('Priority')),
            'FAM:_TODO:_RDATE'                => new DateValue(I18N::translate('Reminder date')),
            'FAM:_UID'                        => new PafUid(I18N::translate('Unique identifier')),
            'HEAD:GEDC:VERS:_ADDENDUM'        => new EmptyElement(I18N::translate('GEDCOM-L')),
            'HEAD:GEDC:VERS:_ADDENDUM:VERS'   => new VersionNumber(I18N::translate('Version')),
            'HEAD:GEDC:VERS:_ADDENDUM:WWW'    => new AddressWebPage(I18N::translate('URL')),
            'HEAD:SOUR:CORP:ADDR:_NAME'       => new CustomElement(I18N::translate('Name of addressee')),
            'HEAD:_SCHEMA'                    => new EmptyElement(I18N::translate('Schema')),
            'HEAD:_SCHEMA:*'                  => new EmptyElement(I18N::translate('Base GEDCOM tag')),
            'HEAD:_SCHEMA:*:*'                => new EmptyElement(I18N::translate('New GEDCOM tag')),
            'HEAD:_SCHEMA:*:*:*'              => new EmptyElement(I18N::translate('New GEDCOM tag')),
            'HEAD:_SCHEMA:*:*:*:*'            => new EmptyElement(I18N::translate('New GEDCOM tag')),
            'HEAD:_SCHEMA:*:*:*:*:*'          => new EmptyElement(I18N::translate('New GEDCOM tag')),
            'HEAD:_SCHEMA:*:*:*:*:*:*'        => new EmptyElement(I18N::translate('New GEDCOM tag')),
            'HEAD:_SCHEMA:*:*:*:*:*:*:_DEFN'  => new EmptyElement(I18N::translate('Definition')),
            'HEAD:_SCHEMA:*:*:*:*:*:_DEFN'    => new EmptyElement(I18N::translate('Definition')),
            'HEAD:_SCHEMA:*:*:*:*:_DEFN'      => new EmptyElement(I18N::translate('Definition')),
            'HEAD:_SCHEMA:*:*:*:_DEFN'        => new EmptyElement(I18N::translate('Definition')),
            'HEAD:_SCHEMA:*:*:_DEFN'          => new EmptyElement(I18N::translate('Definition')),
            'INDI:*:ADDR:_NAME'               => new CustomElement(I18N::translate('Name of addressee')),
            // I18N: https://gov.genealogy.net
            'INDI:*:PLAC:_GOV'                => new GovIdentifier(I18N::translate('GOV identifier')),
            'INDI:*:PLAC:_LOC'                => new XrefLocation(I18N::translate('Location')),
            // I18N: https://en.wikipedia.org/wiki/Maidenhead_Locator_System
            'INDI:*:PLAC:_MAIDENHEAD'         => new MaidenheadLocator(I18N::translate('Maidenhead location code')),
            'INDI:*:PLAC:_POST'               => new AddressPostalCode(I18N::translate('Postal code')),
            'INDI:*:PLAC:_POST:DATE'          => new DateValue(I18N::translate('Date')),
            'INDI:*:_ASSO'                    => new XrefAssociate(I18N::translate('Associate')),
            'INDI:*:_ASSO:NOTE'               => new NoteStructure(I18N::translate('Note on association')),
            'INDI:*:_ASSO:RELA'               => new RelationIsDescriptor(I18N::translate('Relationship')),
            'INDI:*:_ASSO:SOUR'               => new XrefSource(I18N::translate('Source citation')),
            'INDI:*:_ASSO:SOUR:DATA'          => new SourceData(I18N::translate('Data')),
            'INDI:*:_ASSO:SOUR:DATA:DATE'     => new DateValue(I18N::translate('Date of entry in original source')),
            'INDI:*:_ASSO:SOUR:DATA:TEXT'     => new TextFromSource(I18N::translate('Text')),
            'INDI:*:_ASSO:SOUR:EVEN'          => new EventTypeCitedFrom(I18N::translate('Event')),
            'INDI:*:_ASSO:SOUR:EVEN:ROLE'     => new RoleInEvent(I18N::translate('Role')),
            'INDI:*:_ASSO:SOUR:NOTE'          => new NoteStructure(I18N::translate('Note on source citation')),
            'INDI:*:_ASSO:SOUR:OBJE'          => new XrefMedia(I18N::translate('Media object')),
            'INDI:*:_ASSO:SOUR:PAGE'          => new WhereWithinSource(I18N::translate('Citation details')),
            'INDI:*:_ASSO:SOUR:QUAY'          => new CertaintyAssessment(I18N::translate('Quality of data')),
            'INDI:*:_WITN'                    => new CustomElement(I18N::translate('Witnesses')),
            'INDI:BAPM:_GODP'                 => new CustomElement(I18N::translate('Godparents')),
            'INDI:CHR:_GODP'                  => new CustomElement(I18N::translate('Godparents')),
            'INDI:NAME:_RUFNAME'              => new NamePieceGiven(I18N::translate('Rufname')),
            'INDI:OBJE:_PRIM'                 => new CustomElement(I18N::translate('Highlighted image')),
            'INDI:SEX'                        => new SexXValue(I18N::translate('Sex')),
            'INDI:_TODO'                      => new ResearchTask(I18N::translate('Research task')),
            'INDI:_TODO:DATA'                 => new SubmitterText(I18N::translate('The solution')),
            'INDI:_TODO:DATE'                 => new DateValueToday(I18N::translate('Creation date')),
            'INDI:_TODO:DESC'                 => new CustomElement(I18N::translate('Description')),
            'INDI:_TODO:NOTE'                 => new NoteStructure(I18N::translate('Note')),
            'INDI:_TODO:REPO'                 => new XrefRepository(I18N::translate('Repository'), []),
            'INDI:_TODO:STAT'                 => new ResearchTaskStatus(I18N::translate('Status')),
            'INDI:_TODO:TYPE'                 => new ResearchTaskType(I18N::translate('Type of research task')),
            'INDI:_TODO:_CAT'                 => new CustomElement(I18N::translate('Category')),
            'INDI:_TODO:_CDATE'               => new DateValue(I18N::translate('Completion date')),
            'INDI:_TODO:_PRTY'                => new ResearchTaskPriority(I18N::translate('Priority')),
            'INDI:_TODO:_RDATE'               => new DateValue(I18N::translate('Reminder date')),
            'INDI:_UID'                       => new PafUid(I18N::translate('Unique identifier')),
            'NOTE:_UID'                       => new PafUid(I18N::translate('Unique identifier')),
            'OBJE:FILE:_PRIM'                 => new CustomElement(I18N::translate('Highlighted image')),
            'OBJE:_UID'                       => new PafUid(I18N::translate('Unique identifier')),
            'REPO:ADDR:_NAME'                 => new CustomElement(I18N::translate('Name of addressee')),
            'REPO:_UID'                       => new PafUid(I18N::translate('Unique identifier')),
            'SOUR:_UID'                       => new PafUid(I18N::translate('Unique identifier')),
            'SOUR:DATA:EVEN:PLAC:_LOC'        => new XrefLocation(I18N::translate('Location')),
            // I18N: https://en.wikipedia.org/wiki/Maidenhead_Locator_System
            'SOUR:DATA:EVEN:PLAC:_MAIDENHEAD' => new MaidenheadLocator(I18N::translate('Maidenhead location code')),
            'SOUR:DATA:EVEN:PLAC:_POST'       => new AddressPostalCode(I18N::translate('Postal code')),
            'SOUR:DATA:EVEN:PLAC:_POST:DATE'  => new DateValue(I18N::translate('Date')),
            'SOUR:DATA:EVEN:PLAC:_GOV'        => new GovIdentifier(I18N::translate('GOV identifier')),
            'SUBM:ADDR:_NAME'                 => new CustomElement(I18N::translate('Name of addressee')),
            'SUBM:_UID'                       => new PafUid(I18N::translate('Unique identifier')),
            'SUBN:_UID'                       => new PafUid(I18N::translate('Unique identifier')),
            '_LOC'                            => new LocationRecord(I18N::translate('Location')),
            '_LOC:CHAN'                       => new Change(I18N::translate('Last change')),
            '_LOC:CHAN:DATE'                  => new ChangeDate(I18N::translate('Date of last change')),
            '_LOC:CHAN:DATE:TIME'             => new TimeValueNow(I18N::translate('Time of last change')),
            '_LOC:CHAN:NOTE'                  => new NoteStructure(I18N::translate('Note on last change')),
            '_LOC:EVEN'                       => new CustomElement(I18N::translate('Event'), ['TYPE'  => '0:1', 'DATE'  => '0:1', 'PLAC'  => '0:1', 'ADDR'  => '0:1', 'EMAIL' => '0:1:?', 'WWW'   => '0:1:?', 'PHON'  => '0:1:?', 'FAX'   => '0:1:?', 'CAUS'  => '0:1', 'AGNC'  => '0:1', 'RELI'  => '0:1', 'NOTE'  => '0:M', 'OBJE'  => '0:M', 'SOUR'  => '0:M', 'RESN'  => '0:1']),
            '_LOC:EVEN:ADDR'                  => new AddressLine(I18N::translate('Address')),
            '_LOC:EVEN:ADDR:ADR1'             => new AddressLine1(I18N::translate('Address line 1')),
            '_LOC:EVEN:ADDR:ADR2'             => new AddressLine2(I18N::translate('Address line 2')),
            '_LOC:EVEN:ADDR:ADR3'             => new AddressLine3(I18N::translate('Address line 3')),
            '_LOC:EVEN:ADDR:CITY'             => new AddressCity(I18N::translate('City')),
            '_LOC:EVEN:ADDR:CTRY'             => new AddressCountry(I18N::translate('Country')),
            '_LOC:EVEN:ADDR:POST'             => new AddressPostalCode(I18N::translate('Postal code')),
            '_LOC:EVEN:ADDR:STAE'             => new AddressState(I18N::translate('State')),
            '_LOC:EVEN:AGNC'                  => new ResponsibleAgency(I18N::translate('Agency')),
            '_LOC:EVEN:CAUS'                  => new CauseOfEvent(I18N::translate('Cause')),
            '_LOC:EVEN:DATE'                  => new DateValue(I18N::translate('Date of event')),
            '_LOC:EVEN:EMAIL'                 => new AddressEmail(I18N::translate('Email address')),
            '_LOC:EVEN:FAX'                   => new AddressFax(I18N::translate('Fax')),
            '_LOC:EVEN:NOTE'                  => new NoteStructure(I18N::translate('Note')),
            '_LOC:EVEN:OBJE'                  => new XrefMedia(I18N::translate('Media object')),
            '_LOC:EVEN:PHON'                  => new PhoneNumber(I18N::translate('Phone')),
            '_LOC:EVEN:PLAC'                  => new PlaceName(I18N::translate('Place of event')),
            '_LOC:EVEN:PLAC:FONE'             => new PlacePhoneticVariation(I18N::translate('Phonetic place')),
            '_LOC:EVEN:PLAC:FONE:TYPE'        => new PhoneticType(I18N::translate('Type')),
            '_LOC:EVEN:PLAC:FORM'             => new PlaceHierarchy(I18N::translate('Format')),
            '_LOC:EVEN:PLAC:MAP'              => new EmptyElement(I18N::translate('Coordinates'), ['LATI' => '1:1', 'LONG' => '1:1']),
            '_LOC:EVEN:PLAC:MAP:LATI'         => new PlaceLatitude(I18N::translate('Latitude')),
            '_LOC:EVEN:PLAC:MAP:LONG'         => new PlaceLongtitude(I18N::translate('Longitude')),
            '_LOC:EVEN:PLAC:NOTE'             => new NoteStructure(I18N::translate('Note on place')),
            '_LOC:EVEN:PLAC:ROMN'             => new PlaceRomanizedVariation(I18N::translate('Romanized place')),
            '_LOC:EVEN:PLAC:ROMN:TYPE'        => new RomanizedType(I18N::translate('Type')),
            '_LOC:EVEN:PLAC:_LOC'             => new XrefLocation(I18N::translate('Location')),
            '_LOC:EVEN:RELI'                  => new ReligiousAffiliation(I18N::translate('Religion'), []),
            '_LOC:EVEN:RESN'                  => new RestrictionNotice(I18N::translate('Restriction')),
            '_LOC:EVEN:SOUR'                  => new XrefSource(I18N::translate('Source citation')),
            '_LOC:EVEN:TYPE'                  => new EventAttributeType(I18N::translate('Type of event')),
            '_LOC:EVEN:WWW'                   => new CustomElement(I18N::translate('URL')),
            '_LOC:MAP'                        => new EmptyElement(I18N::translate('Coordinates'), ['LATI' => '1:1', 'LONG' => '1:1']),
            '_LOC:MAP:LATI'                   => new PlaceLatitude(I18N::translate('Latitude')),
            '_LOC:MAP:LONG'                   => new PlaceLongtitude(I18N::translate('Longitude')),
            '_LOC:NAME'                       => new PlaceName(I18N::translate('Place'), ['ABBR' => '0:1', 'DATE' => '0:1', 'LANG' => '0:1', 'SOUR' => '0:M']),
            '_LOC:NAME:ABBR'                  => new CustomElement(I18N::translate('Abbreviation'), ['TYPE' => '0:1']),
            '_LOC:NAME:ABBR:TYPE'             => new CustomElement(I18N::translate('Type of abbreviation')),
            '_LOC:NAME:DATE'                  => new DateValue(I18N::translate('Date')),
            '_LOC:NAME:LANG'                  => new LanguageId(I18N::translate('Language')),
            '_LOC:NAME:SOUR'                  => new XrefSource(I18N::translate('Source')),
            '_LOC:NOTE'                       => new NoteStructure(I18N::translate('Note')),
            '_LOC:OBJE'                       => new XrefMedia(I18N::translate('Media')),
            '_LOC:RELI'                       => new ReligiousAffiliation(I18N::translate('Religion'), []),
            '_LOC:SOUR'                       => new XrefSource(I18N::translate('Source')),
            '_LOC:SOUR:DATA'                  => new SourceData(I18N::translate('Data')),
            '_LOC:SOUR:DATA:DATE'             => new DateValue(I18N::translate('Date of entry in original source')),
            '_LOC:SOUR:DATA:TEXT'             => new TextFromSource(I18N::translate('Text')),
            '_LOC:SOUR:EVEN'                  => new EventTypeCitedFrom(I18N::translate('Event')),
            '_LOC:SOUR:EVEN:ROLE'             => new RoleInEvent(I18N::translate('Role')),
            '_LOC:SOUR:NOTE'                  => new NoteStructure(I18N::translate('Note on source citation')),
            '_LOC:SOUR:OBJE'                  => new XrefMedia(I18N::translate('Media object')),
            '_LOC:SOUR:PAGE'                  => new WhereWithinSource(I18N::translate('Citation details')),
            '_LOC:SOUR:QUAY'                  => new CertaintyAssessment(I18N::translate('Quality of data')),
            '_LOC:TYPE'                       => new CustomElement(I18N::translate('Type of location'), ['DATE' => '0:1', '_GOVTYPE' => '0:1', 'SOUR' => '0:M']),
            '_LOC:TYPE:DATE'                  => new DateValue(I18N::translate('Date')),
            '_LOC:TYPE:SOUR'                  => new XrefSource(I18N::translate('Source')),
            '_LOC:TYPE:_GOVTYPE'              => new CustomElement(I18N::translate('GOV identifier type')),
            '_LOC:_AIDN'                      => new CustomElement(I18N::translate('Administrative ID')),
            '_LOC:_AIDN:DATE'                 => new DateValue(I18N::translate('Date')),
            '_LOC:_AIDN:SOUR'                 => new XrefSource(I18N::translate('Source')),
            '_LOC:_AIDN:TYPE'                 => new CustomElement(I18N::translate('Type of administrative ID')),
            '_LOC:_DMGD'                      => new CustomElement(I18N::translate('Demographic data')),
            '_LOC:_DMGD:DATE'                 => new DateValue(I18N::translate('Date')),
            '_LOC:_DMGD:SOUR'                 => new XrefSource(I18N::translate('Source')),
            '_LOC:_DMGD:TYPE'                 => new CustomElement(I18N::translate('Type of demographic data')),
            // I18N: https://gov.genealogy.net
            '_LOC:_GOV'                       => new GovIdentifier(I18N::translate('GOV identifier')),
            '_LOC:_LOC'                       => new XrefLocation(I18N::translate('Parent location'), ['DATE' => '0:1', 'SOUR' => '0:M', 'TYPE' => '0:1']),
            '_LOC:_LOC:DATE'                  => new DateValue(I18N::translate('Date')),
            '_LOC:_LOC:SOUR'                  => new XrefSource(I18N::translate('Source')),
            '_LOC:_LOC:TYPE'                  => new HierarchicalRelationship(I18N::translate('Hierarchical relationship')),
            // I18N: https://en.wikipedia.org/wiki/Maidenhead_Locator_System
            '_LOC:_MAIDENHEAD'                => new MaidenheadLocator(I18N::translate('Maidenhead location code')),
            '_LOC:_POST'                      => new AddressPostalCode(I18N::translate('Postal code')),
            '_LOC:_POST:DATE'                 => new DateValue(I18N::translate('Date')),
            '_LOC:_POST:SOUR'                 => new XrefSource(I18N::translate('Source')),
            '_LOC:_UID'                       => new PafUid(I18N::translate('Unique identifier')),
            '_LOC:*:SOUR:DATA'                => new SourceData(I18N::translate('Data')),
            '_LOC:*:SOUR:DATA:DATE'           => new DateValue(I18N::translate('Date of entry in original source')),
            '_LOC:*:SOUR:DATA:TEXT'           => new TextFromSource(I18N::translate('Text')),
            '_LOC:*:SOUR:EVEN'                => new EventTypeCitedFrom(I18N::translate('Event')),
            '_LOC:*:SOUR:EVEN:ROLE'           => new RoleInEvent(I18N::translate('Role')),
            '_LOC:*:SOUR:NOTE'                => new NoteStructure(I18N::translate('Note on source citation')),
            '_LOC:*:SOUR:OBJE'                => new XrefMedia(I18N::translate('Media object')),
            '_LOC:*:SOUR:PAGE'                => new WhereWithinSource(I18N::translate('Citation details')),
            '_LOC:*:SOUR:QUAY'                => new CertaintyAssessment(I18N::translate('Quality of data')),
        ];
    }
}
