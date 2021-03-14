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

namespace Fisharebest\Webtrees\Factories;

use Fisharebest\Webtrees\Contracts\ElementFactoryInterface;
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
use Fisharebest\Webtrees\Elements\AdoptedByWhichParent;
use Fisharebest\Webtrees\Elements\Adoption;
use Fisharebest\Webtrees\Elements\AdultChristening;
use Fisharebest\Webtrees\Elements\AgeAtEvent;
use Fisharebest\Webtrees\Elements\AncestralFileNumber;
use Fisharebest\Webtrees\Elements\Annulment;
use Fisharebest\Webtrees\Elements\ApprovedSystemId;
use Fisharebest\Webtrees\Elements\AttributeDescriptor;
use Fisharebest\Webtrees\Elements\AutomatedRecordId;
use Fisharebest\Webtrees\Elements\Baptism;
use Fisharebest\Webtrees\Elements\BasMitzvah;
use Fisharebest\Webtrees\Elements\Birth;
use Fisharebest\Webtrees\Elements\Blessing;
use Fisharebest\Webtrees\Elements\Burial;
use Fisharebest\Webtrees\Elements\CasteName;
use Fisharebest\Webtrees\Elements\CauseOfEvent;
use Fisharebest\Webtrees\Elements\Census;
use Fisharebest\Webtrees\Elements\CertaintyAssessment;
use Fisharebest\Webtrees\Elements\Change;
use Fisharebest\Webtrees\Elements\ChangeDate;
use Fisharebest\Webtrees\Elements\CharacterSet;
use Fisharebest\Webtrees\Elements\ChildLinkageStatus;
use Fisharebest\Webtrees\Elements\Christening;
use Fisharebest\Webtrees\Elements\Confirmation;
use Fisharebest\Webtrees\Elements\ContentDescription;
use Fisharebest\Webtrees\Elements\CopyrightFile;
use Fisharebest\Webtrees\Elements\CopyrightSourceData;
use Fisharebest\Webtrees\Elements\CountOfChildren;
use Fisharebest\Webtrees\Elements\CountOfMarriages;
use Fisharebest\Webtrees\Elements\Cremation;
use Fisharebest\Webtrees\Elements\CustomElement;
use Fisharebest\Webtrees\Elements\CustomEvent;
use Fisharebest\Webtrees\Elements\CustomFact;
use Fisharebest\Webtrees\Elements\DateLdsOrd;
use Fisharebest\Webtrees\Elements\DateValue;
use Fisharebest\Webtrees\Elements\Death;
use Fisharebest\Webtrees\Elements\DescriptiveTitle;
use Fisharebest\Webtrees\Elements\Divorce;
use Fisharebest\Webtrees\Elements\DivorceFiled;
use Fisharebest\Webtrees\Elements\Emigration;
use Fisharebest\Webtrees\Elements\EmptyElement;
use Fisharebest\Webtrees\Elements\Engagement;
use Fisharebest\Webtrees\Elements\EntryRecordingDate;
use Fisharebest\Webtrees\Elements\EventAttributeType;
use Fisharebest\Webtrees\Elements\EventDescriptor;
use Fisharebest\Webtrees\Elements\EventOrFactClassification;
use Fisharebest\Webtrees\Elements\EventsRecorded;
use Fisharebest\Webtrees\Elements\EventTypeCitedFrom;
use Fisharebest\Webtrees\Elements\FamilyRecord;
use Fisharebest\Webtrees\Elements\FamilyStatusText;
use Fisharebest\Webtrees\Elements\FileName;
use Fisharebest\Webtrees\Elements\FirstCommunion;
use Fisharebest\Webtrees\Elements\Form;
use Fisharebest\Webtrees\Elements\GedcomElement;
use Fisharebest\Webtrees\Elements\GenerationsOfAncestors;
use Fisharebest\Webtrees\Elements\GenerationsOfDescendants;
use Fisharebest\Webtrees\Elements\GovIdentifier;
use Fisharebest\Webtrees\Elements\Graduation;
use Fisharebest\Webtrees\Elements\HeaderRecord;
use Fisharebest\Webtrees\Elements\HierarchicalRelationship;
use Fisharebest\Webtrees\Elements\Immigration;
use Fisharebest\Webtrees\Elements\IndividualRecord;
use Fisharebest\Webtrees\Elements\LanguageId;
use Fisharebest\Webtrees\Elements\LdsBaptism;
use Fisharebest\Webtrees\Elements\LdsBaptismDateStatus;
use Fisharebest\Webtrees\Elements\LdsChildSealing;
use Fisharebest\Webtrees\Elements\LdsChildSealingDateStatus;
use Fisharebest\Webtrees\Elements\LdsConfirmation;
use Fisharebest\Webtrees\Elements\LdsEndowment;
use Fisharebest\Webtrees\Elements\LdsEndowmentDateStatus;
use Fisharebest\Webtrees\Elements\LdsSpouseSealing;
use Fisharebest\Webtrees\Elements\LdsSpouseSealingDateStatus;
use Fisharebest\Webtrees\Elements\LocationRecord;
use Fisharebest\Webtrees\Elements\MaidenheadLocator;
use Fisharebest\Webtrees\Elements\Marriage;
use Fisharebest\Webtrees\Elements\MarriageBanns;
use Fisharebest\Webtrees\Elements\MarriageContract;
use Fisharebest\Webtrees\Elements\MarriageLicence;
use Fisharebest\Webtrees\Elements\MarriageSettlement;
use Fisharebest\Webtrees\Elements\MarriageType;
use Fisharebest\Webtrees\Elements\MediaRecord;
use Fisharebest\Webtrees\Elements\MultimediaFileReference;
use Fisharebest\Webtrees\Elements\MultimediaFormat;
use Fisharebest\Webtrees\Elements\NameOfBusiness;
use Fisharebest\Webtrees\Elements\NameOfFamilyFile;
use Fisharebest\Webtrees\Elements\NameOfProduct;
use Fisharebest\Webtrees\Elements\NameOfRepository;
use Fisharebest\Webtrees\Elements\NameOfSourceData;
use Fisharebest\Webtrees\Elements\NamePersonal;
use Fisharebest\Webtrees\Elements\NamePhoneticVariation;
use Fisharebest\Webtrees\Elements\NamePieceGiven;
use Fisharebest\Webtrees\Elements\NamePieceNickname;
use Fisharebest\Webtrees\Elements\NamePiecePrefix;
use Fisharebest\Webtrees\Elements\NamePieceSuffix;
use Fisharebest\Webtrees\Elements\NamePieceSurname;
use Fisharebest\Webtrees\Elements\NamePieceSurnamePrefix;
use Fisharebest\Webtrees\Elements\NameRomanizedVariation;
use Fisharebest\Webtrees\Elements\NameType;
use Fisharebest\Webtrees\Elements\NationalIdNumber;
use Fisharebest\Webtrees\Elements\NationOrTribalOrigin;
use Fisharebest\Webtrees\Elements\Naturalization;
use Fisharebest\Webtrees\Elements\NobilityTypeTitle;
use Fisharebest\Webtrees\Elements\NoteRecord;
use Fisharebest\Webtrees\Elements\NoteStructure;
use Fisharebest\Webtrees\Elements\Occupation;
use Fisharebest\Webtrees\Elements\OrdinanceProcessFlag;
use Fisharebest\Webtrees\Elements\Ordination;
use Fisharebest\Webtrees\Elements\PafUid;
use Fisharebest\Webtrees\Elements\PedigreeLinkageType;
use Fisharebest\Webtrees\Elements\PermanentRecordFileNumber;
use Fisharebest\Webtrees\Elements\PhoneNumber;
use Fisharebest\Webtrees\Elements\PhoneticType;
use Fisharebest\Webtrees\Elements\PhysicalDescription;
use Fisharebest\Webtrees\Elements\PlaceHierarchy;
use Fisharebest\Webtrees\Elements\PlaceLatitude;
use Fisharebest\Webtrees\Elements\PlaceLivingOrdinance;
use Fisharebest\Webtrees\Elements\PlaceLongtitude;
use Fisharebest\Webtrees\Elements\PlaceName;
use Fisharebest\Webtrees\Elements\PlacePhoneticVariation;
use Fisharebest\Webtrees\Elements\PlaceRomanizedVariation;
use Fisharebest\Webtrees\Elements\Possessions;
use Fisharebest\Webtrees\Elements\Probate;
use Fisharebest\Webtrees\Elements\PublicationDate;
use Fisharebest\Webtrees\Elements\ReceivingSystemName;
use Fisharebest\Webtrees\Elements\RelationIsDescriptor;
use Fisharebest\Webtrees\Elements\ReligiousAffiliation;
use Fisharebest\Webtrees\Elements\RepositoryRecord;
use Fisharebest\Webtrees\Elements\ResearchTask;
use Fisharebest\Webtrees\Elements\ResearchTaskPriority;
use Fisharebest\Webtrees\Elements\ResearchTaskStatus;
use Fisharebest\Webtrees\Elements\ResearchTaskType;
use Fisharebest\Webtrees\Elements\Residence;
use Fisharebest\Webtrees\Elements\ResponsibleAgency;
use Fisharebest\Webtrees\Elements\RestrictionNotice;
use Fisharebest\Webtrees\Elements\Retirement;
use Fisharebest\Webtrees\Elements\RoleInEvent;
use Fisharebest\Webtrees\Elements\RomanizedType;
use Fisharebest\Webtrees\Elements\ScholasticAchievement;
use Fisharebest\Webtrees\Elements\SexValue;
use Fisharebest\Webtrees\Elements\SexXValue;
use Fisharebest\Webtrees\Elements\SocialSecurityNumber;
use Fisharebest\Webtrees\Elements\SourceCallNumber;
use Fisharebest\Webtrees\Elements\SourceData;
use Fisharebest\Webtrees\Elements\SourceFiledByEntry;
use Fisharebest\Webtrees\Elements\SourceJurisdictionPlace;
use Fisharebest\Webtrees\Elements\SourceMediaType;
use Fisharebest\Webtrees\Elements\SourceOriginator;
use Fisharebest\Webtrees\Elements\SourcePublicationFacts;
use Fisharebest\Webtrees\Elements\SourceRecord;
use Fisharebest\Webtrees\Elements\SubmissionRecord;
use Fisharebest\Webtrees\Elements\SubmitterName;
use Fisharebest\Webtrees\Elements\SubmitterRecord;
use Fisharebest\Webtrees\Elements\SubmitterRegisteredRfn;
use Fisharebest\Webtrees\Elements\SubmitterText;
use Fisharebest\Webtrees\Elements\TempleCode;
use Fisharebest\Webtrees\Elements\TextFromSource;
use Fisharebest\Webtrees\Elements\TimeValue;
use Fisharebest\Webtrees\Elements\TransmissionDate;
use Fisharebest\Webtrees\Elements\UnknownElement;
use Fisharebest\Webtrees\Elements\UserReferenceNumber;
use Fisharebest\Webtrees\Elements\UserReferenceType;
use Fisharebest\Webtrees\Elements\VersionNumber;
use Fisharebest\Webtrees\Elements\WebtreesUser;
use Fisharebest\Webtrees\Elements\WhereWithinSource;
use Fisharebest\Webtrees\Elements\Will;
use Fisharebest\Webtrees\Elements\XrefFamily;
use Fisharebest\Webtrees\Elements\XrefIndividual;
use Fisharebest\Webtrees\Elements\XrefLocation;
use Fisharebest\Webtrees\Elements\XrefMedia;
use Fisharebest\Webtrees\Elements\XrefRepository;
use Fisharebest\Webtrees\Elements\XrefSource;
use Fisharebest\Webtrees\Elements\XrefSubmission;
use Fisharebest\Webtrees\Elements\XrefSubmitter;
use Fisharebest\Webtrees\I18N;

use function preg_match;
use function strpos;

/**
 * Make a GEDCOM element.
 */
class ElementFactory implements ElementFactoryInterface
{
    /** @var array<string,ElementInterface> */
    private $elements = [];

    /**
     * Create a GEDCOM element that corresponds to a GEDCOM tag.
     * Finds the correct element for all valid tags.
     * Finds a likely element for custom tags.
     *
     * @param string $tag - Colon delimited hierarchy, e.g. 'INDI:BIRT:PLAC'
     *
     * @return ElementInterface
     */
    public function make(string $tag): ElementInterface
    {
        return $this->elements()[$tag] ?? $this->findElementByWildcard($tag) ?? new UnknownElement($tag);
    }

    /**
     * Association between GEDCOM tags and GEDCOM elements.
     * We can't initialise this in the constructor, as the I18N package isn't available then.
     *
     * @return array<string,ElementInterface>
     */
    private function elements(): array
    {
        if ($this->elements === []) {
            // Gedcom 5.5.1
            $this->elements = [
                'FAM'                      => new FamilyRecord(I18N::translate('Family')),
                'FAM:*:ADDR'               => new AddressLine(I18N::translate('Address')),
                'FAM:*:ADDR:ADR1'          => new AddressLine1(I18N::translate('Address line 1')),
                'FAM:*:ADDR:ADR2'          => new AddressLine2(I18N::translate('Address line 2')),
                'FAM:*:ADDR:ADR3'          => new AddressLine3(I18N::translate('Address line 3')),
                'FAM:*:ADDR:CITY'          => new AddressCity(I18N::translate('City')),
                'FAM:*:ADDR:CTRY'          => new AddressCountry(I18N::translate('Country')),
                'FAM:*:ADDR:POST'          => new AddressPostalCode(I18N::translate('Postal code')),
                'FAM:*:ADDR:STAE'          => new AddressState(I18N::translate('State')),
                'FAM:*:AGNC'               => new ResponsibleAgency(I18N::translate('Agency')),
                'FAM:*:CAUS'               => new CauseOfEvent(I18N::translate('Cause')),
                'FAM:*:DATE'               => new DateValue(I18N::translate('Date')),
                'FAM:*:EMAIL'              => new AddressEmail(I18N::translate('Email address')),
                'FAM:*:FAX'                => new AddressFax(I18N::translate('Fax')),
                'FAM:*:HUSB'               => new EmptyElement(I18N::translate('Husband'), ['AGE' => '0:1']),
                'FAM:*:HUSB:AGE'           => new AgeAtEvent(I18N::translate('Husband’s age')),
                'FAM:*:NOTE'               => new NoteStructure(I18N::translate('Note')),
                'FAM:*:OBJE'               => new XrefMedia(I18N::translate('Media object')),
                'FAM:*:PHON'               => new PhoneNumber(I18N::translate('Phone')),
                'FAM:*:PLAC'               => new PlaceName(I18N::translate('Place')),
                'FAM:*:PLAC:FONE'          => new PlacePhoneticVariation(I18N::translate('Phonetic place')),
                'FAM:*:PLAC:FONE:TYPE'     => new PhoneticType(I18N::translate('Type')),
                'FAM:*:PLAC:FORM'          => new PlaceHierarchy(I18N::translate('Format')),
                'FAM:*:PLAC:MAP'           => new EmptyElement(I18N::translate('Coordinates')),
                'FAM:*:PLAC:MAP:LATI'      => new PlaceLatitude(I18N::translate('Latitude')),
                'FAM:*:PLAC:MAP:LONG'      => new PlaceLongtitude(I18N::translate('Longitude')),
                'FAM:*:PLAC:NOTE'          => new NoteStructure(I18N::translate('Note')),
                'FAM:*:PLAC:ROMN'          => new PlaceRomanizedVariation(I18N::translate('Romanized place')),
                'FAM:*:PLAC:ROMN:TYPE'     => new RomanizedType(I18N::translate('Type')),
                'FAM:*:RELI'               => new ReligiousAffiliation(I18N::translate('Religion')),
                'FAM:*:RESN'               => new RestrictionNotice(I18N::translate('Restriction')),
                'FAM:*:SOUR'               => new XrefSource(I18N::translate('Source')),
                'FAM:*:SOUR:DATA'          => new SourceData(I18N::translate('Data')),
                'FAM:*:SOUR:DATA:DATE'     => new EntryRecordingDate(I18N::translate('Date of entry in original source')),
                'FAM:*:SOUR:DATA:TEXT'     => new TextFromSource(I18N::translate('Text')),
                'FAM:*:SOUR:EVEN'          => new EventTypeCitedFrom(I18N::translate('Event')),
                'FAM:*:SOUR:EVEN:ROLE'     => new RoleInEvent(I18N::translate('Role')),
                'FAM:*:SOUR:NOTE'          => new NoteStructure(I18N::translate('Note')),
                'FAM:*:SOUR:OBJE'          => new XrefMedia(I18N::translate('Media object')),
                'FAM:*:SOUR:PAGE'          => new WhereWithinSource(I18N::translate('Citation details')),
                'FAM:*:SOUR:QUAY'          => new CertaintyAssessment(I18N::translate('Quality of data')),
                'FAM:*:TYPE'               => new EventOrFactClassification(I18N::translate('Type')),
                'FAM:*:WIFE'               => new EmptyElement(I18N::translate('Wife'), ['AGE' => '0:1']),
                'FAM:*:WIFE:AGE'           => new AgeAtEvent(I18N::translate('Wife’s age')),
                'FAM:*:WWW'                => new AddressWebPage(I18N::translate('URL')),
                'FAM:ANUL'                 => new Annulment(I18N::translate('Annulment')),
                'FAM:CENS'                 => new Census(I18N::translate('Census')),
                'FAM:CHAN'                 => new Change(I18N::translate('Last change')),
                'FAM:CHAN:DATE'            => new ChangeDate(I18N::translate('Date of last change')),
                'FAM:CHAN:DATE:TIME'       => new TimeValue(I18N::translate('Time')),
                'FAM:CHAN:NOTE'            => new NoteStructure(I18N::translate('Note')),
                'FAM:CHIL'                 => new XrefIndividual(I18N::translate('Child')),
                'FAM:DIV'                  => new Divorce(I18N::translate('Divorce')),
                'FAM:DIVF'                 => new DivorceFiled(I18N::translate('Divorce filed')),
                'FAM:ENGA'                 => new Engagement(I18N::translate('Engagement')),
                'FAM:ENGA:DATE'            => new DateValue(I18N::translate('Date of engagement')),
                'FAM:ENGA:PLACE'           => new PlaceName(I18N::translate('Place of engagement')),
                'FAM:EVEN'                 => new EventDescriptor(I18N::translate('Event')),
                'FAM:EVEN:TYPE'            => new EventAttributeType(I18N::translate('Type of event')),
                'FAM:HUSB'                 => new XrefIndividual(I18N::translate('Husband')),
                'FAM:MARB'                 => new MarriageBanns(I18N::translate('Marriage banns')),
                'FAM:MARB:DATE'            => new DateValue(I18N::translate('Date of marriage banns')),
                'FAM:MARB:PLAC'            => new PlaceName(I18N::translate('Place of marriage banns')),
                'FAM:MARC'                 => new MarriageContract(I18N::translate('Marriage contract')),
                'FAM:MARL'                 => new MarriageLicence(I18N::translate('Marriage license')),
                'FAM:MARR'                 => new Marriage(I18N::translate('Marriage')),
                'FAM:MARR:DATE'            => new DateValue(I18N::translate('Date of marriage')),
                'FAM:MARR:PLAC'            => new PlaceName(I18N::translate('Place of marriage')),
                'FAM:MARR:TYPE'            => new MarriageType(I18N::translate('Type of marriage')),
                'FAM:MARS'                 => new MarriageSettlement(I18N::translate('Marriage settlement')),
                'FAM:NCHI'                 => new CountOfChildren(I18N::translate('Number of children')),
                'FAM:NOTE'                 => new NoteStructure(I18N::translate('Note')),
                'FAM:OBJE'                 => new XrefMedia(I18N::translate('Media object')),
                'FAM:REFN'                 => new UserReferenceNumber(I18N::translate('Reference number')),
                'FAM:REFN:TYPE'            => new UserReferenceType(I18N::translate('Type')),
                'FAM:RESI'                 => new Residence(I18N::translate('Residence')),
                'FAM:RESN'                 => new RestrictionNotice(I18N::translate('Restriction')),
                'FAM:RIN'                  => new AutomatedRecordId(I18N::translate('Record ID number')),
                'FAM:SLGS'                 => new LdsSpouseSealing(I18N::translate('LDS spouse sealing')),
                'FAM:SLGS:DATE'            => new DateLdsOrd(I18N::translate('Date')),
                'FAM:SLGS:NOTE'            => new NoteStructure(I18N::translate('Note')),
                'FAM:SLGS:PLAC'            => new PlaceLivingOrdinance(I18N::translate('Place')),
                'FAM:SLGS:STAT'            => new LdsSpouseSealingDateStatus(I18N::translate('Status')),
                'FAM:SLGS:STAT:DATE'       => new ChangeDate(I18N::translate('Status change date')),
                'FAM:SLGS:TEMP'            => new TempleCode(I18N::translate('Temple')),
                'FAM:SOUR'                 => new XrefSource(I18N::translate('Source')),
                'FAM:SOUR:DATA'            => new SourceData(I18N::translate('Data')),
                'FAM:SOUR:DATA:DATE'       => new EntryRecordingDate(I18N::translate('Date of entry in original source')),
                'FAM:SOUR:DATA:TEXT'       => new TextFromSource(I18N::translate('Text')),
                'FAM:SOUR:EVEN'            => new EventTypeCitedFrom(I18N::translate('Event')),
                'FAM:SOUR:EVEN:ROLE'       => new RoleInEvent(I18N::translate('Role')),
                'FAM:SOUR:NOTE'            => new NoteStructure(I18N::translate('Note')),
                'FAM:SOUR:OBJE'            => new XrefMedia(I18N::translate('Media object')),
                'FAM:SOUR:PAGE'            => new WhereWithinSource(I18N::translate('Citation details')),
                'FAM:SOUR:QUAY'            => new CertaintyAssessment(I18N::translate('Quality of data')),
                'FAM:SUBM'                 => new XrefSubmitter(I18N::translate('Submitter')),
                'FAM:WIFE'                 => new XrefIndividual(I18N::translate('Wife')),
                'HEAD'                     => new HeaderRecord(I18N::translate('Header')),
                'HEAD:CHAR'                => new CharacterSet(I18N::translate('Character set')),
                'HEAD:CHAR:VERS'           => new VersionNumber(I18N::translate('Version')),
                'HEAD:COPR'                => new CopyrightFile(I18N::translate('Copyright')),
                'HEAD:DATE'                => new TransmissionDate(I18N::translate('Date')),
                'HEAD:DATE:TIME'           => new TimeValue(I18N::translate('Time')),
                'HEAD:DEST'                => new ReceivingSystemName(I18N::translate('Destination')),
                'HEAD:FILE'                => new FileName(I18N::translate('Filename')),
                'HEAD:GEDC'                => new GedcomElement(I18N::translate('GEDCOM')),
                'HEAD:GEDC:FORM'           => new Form(I18N::translate('Format')),
                'HEAD:GEDC:VERS'           => new VersionNumber(I18N::translate('Version')),
                'HEAD:LANG'                => new LanguageId(I18N::translate('Language')),
                'HEAD:NOTE'                => new ContentDescription(I18N::translate('Note')),
                'HEAD:PLAC'                => new EmptyElement(I18N::translate('Place hierarchy'), ['FORM' => '1:1']),
                'HEAD:PLAC:FORM'           => new PlaceHierarchy(I18N::translate('Format')),
                'HEAD:SOUR'                => new ApprovedSystemId('Genealogy software'),
                'HEAD:SOUR:CORP'           => new NameOfBusiness(I18N::translate('Corporation')),
                'HEAD:SOUR:CORP:ADDR'      => new AddressLine(I18N::translate('Address')),
                'HEAD:SOUR:CORP:ADDR:ADR1' => new AddressLine1(I18N::translate('Address line 1')),
                'HEAD:SOUR:CORP:ADDR:ADR2' => new AddressLine2(I18N::translate('Address line 2')),
                'HEAD:SOUR:CORP:ADDR:ADR3' => new AddressLine3(I18N::translate('Address line 3')),
                'HEAD:SOUR:CORP:ADDR:CITY' => new AddressCity(I18N::translate('City')),
                'HEAD:SOUR:CORP:ADDR:CTRY' => new AddressCountry(I18N::translate('Country')),
                'HEAD:SOUR:CORP:ADDR:POST' => new AddressPostalCode(I18N::translate('Postal code')),
                'HEAD:SOUR:CORP:ADDR:STAE' => new AddressState(I18N::translate('State')),
                'HEAD:SOUR:CORP:EMAIL'     => new AddressEmail(I18N::translate('Email address')),
                'HEAD:SOUR:CORP:FAX'       => new AddressFax(I18N::translate('Fax')),
                'HEAD:SOUR:CORP:PHON'      => new PhoneNumber(I18N::translate('Phone')),
                'HEAD:SOUR:CORP:WWW'       => new AddressWebPage(I18N::translate('URL')),
                'HEAD:SOUR:DATA'           => new NameOfSourceData('Data'),
                'HEAD:SOUR:DATA:COPR'      => new CopyrightSourceData(I18N::translate('Copyright')),
                'HEAD:SOUR:DATA:DATE'      => new PublicationDate(I18N::translate('Date')),
                'HEAD:SOUR:NAME'           => new NameOfProduct('Software product'),
                'HEAD:SOUR:VERS'           => new VersionNumber(I18N::translate('Version')),
                'HEAD:SUBM'                => new XrefSubmitter(I18N::translate('Submitter')),
                'HEAD:SUBN'                => new XrefSubmission(I18N::translate('Submission')),
                'INDI'                     => new IndividualRecord(I18N::translate('Individual')),
                'INDI:*:ADDR'              => new AddressLine(I18N::translate('Address')),
                'INDI:*:ADDR:ADR1'         => new AddressLine1(I18N::translate('Address line 1')),
                'INDI:*:ADDR:ADR2'         => new AddressLine2(I18N::translate('Address line 2')),
                'INDI:*:ADDR:ADR3'         => new AddressLine3(I18N::translate('Address line 3')),
                'INDI:*:ADDR:CITY'         => new AddressCity(I18N::translate('City')),
                'INDI:*:ADDR:CTRY'         => new AddressCountry(I18N::translate('Country')),
                'INDI:*:ADDR:POST'         => new AddressPostalCode(I18N::translate('Postal code')),
                'INDI:*:ADDR:STAE'         => new AddressState(I18N::translate('State')),
                'INDI:*:AGE'               => new AgeAtEvent(I18N::translate('Age')),
                'INDI:*:AGNC'              => new ResponsibleAgency(I18N::translate('Agency')),
                'INDI:*:CAUS'              => new CauseOfEvent(I18N::translate('Cause')),
                'INDI:*:DATE'              => new DateValue(I18N::translate('Date')),
                'INDI:*:EMAIL'             => new AddressEmail(I18N::translate('Email address')),
                'INDI:*:FAX'               => new AddressFax(I18N::translate('Fax')),
                'INDI:*:NOTE'              => new NoteStructure(I18N::translate('Note')),
                'INDI:*:OBJE'              => new XrefMedia(I18N::translate('Media object')),
                'INDI:*:PHON'              => new PhoneNumber(I18N::translate('Phone')),
                'INDI:*:PLAC'              => new PlaceName(I18N::translate('Place')),
                'INDI:*:PLAC:FONE'         => new PlacePhoneticVariation(I18N::translate('Phonetic place')),
                'INDI:*:PLAC:FONE:TYPE'    => new PhoneticType(I18N::translate('Type')),
                'INDI:*:PLAC:FORM'         => new PlaceHierarchy(I18N::translate('Format')),
                'INDI:*:PLAC:MAP'          => new EmptyElement(I18N::translate('Coordinates')),
                'INDI:*:PLAC:MAP:LATI'     => new PlaceLatitude(I18N::translate('Latitude')),
                'INDI:*:PLAC:MAP:LONG'     => new PlaceLongtitude(I18N::translate('Longitude')),
                'INDI:*:PLAC:NOTE'         => new NoteStructure(I18N::translate('Note')),
                'INDI:*:PLAC:ROMN'         => new PlaceRomanizedVariation(I18N::translate('Romanized place')),
                'INDI:*:PLAC:ROMN:TYPE'    => new RomanizedType(I18N::translate('Type')),
                'INDI:*:RELI'              => new ReligiousAffiliation(I18N::translate('Religion')),
                'INDI:*:RESN'              => new RestrictionNotice(I18N::translate('Restriction')),
                'INDI:*:SOUR'              => new XrefSource(I18N::translate('Source')),
                'INDI:*:SOUR:DATA'         => new SourceData(I18N::translate('Data')),
                'INDI:*:SOUR:DATA:DATE'    => new EntryRecordingDate(I18N::translate('Date of entry in original source')),
                'INDI:*:SOUR:DATA:TEXT'    => new TextFromSource(I18N::translate('Text')),
                'INDI:*:SOUR:EVEN'         => new EventTypeCitedFrom(I18N::translate('Event')),
                'INDI:*:SOUR:EVEN:ROLE'    => new RoleInEvent(I18N::translate('Role')),
                'INDI:*:SOUR:NOTE'         => new NoteStructure(I18N::translate('Note')),
                'INDI:*:SOUR:OBJE'         => new XrefMedia(I18N::translate('Media object')),
                'INDI:*:SOUR:PAGE'         => new WhereWithinSource(I18N::translate('Citation details')),
                'INDI:*:SOUR:QUAY'         => new CertaintyAssessment(I18N::translate('Quality of data')),
                'INDI:*:TYPE'              => new EventOrFactClassification(I18N::translate('Type')),
                'INDI:*:WWW'               => new AddressWebPage(I18N::translate('URL')),
                'INDI:ADOP'                => new Adoption(I18N::translate('Adoption')),
                'INDI:ADOP:DATE'           => new DateValue(I18N::translate('Date of adoption')),
                'INDI:ADOP:FAMC'           => new XrefFamily(I18N::translate('Adoptive parents')),
                'INDI:ADOP:FAMC:ADOP'      => new AdoptedByWhichParent(I18N::translate('Adoption')),
                'INDI:ADOP:PLAC'           => new PlaceName(I18N::translate('Place of adoption')),
                'INDI:AFN'                 => new AncestralFileNumber(I18N::translate('Ancestral file number')),
                'INDI:ALIA'                => new XrefIndividual(I18N::translate('Alias')),
                'INDI:ANCI'                => new XrefSubmitter(I18N::translate('Ancestors interest')),
                'INDI:ASSO'                => new XrefIndividual(I18N::translate('Associate')),
                'INDI:ASSO:NOTE'           => new NoteStructure(I18N::translate('Note')),
                'INDI:ASSO:RELA'           => new RelationIsDescriptor(I18N::translate('Relationship')),
                'INDI:ASSO:SOUR'           => new XrefSource(I18N::translate('Source citation')),
                'INDI:BAPL'                => new LdsBaptism(I18N::translate('LDS baptism')),
                'INDI:BAPL:DATE'           => new DateLdsOrd(I18N::translate('Date of LDS baptism')),
                'INDI:BAPL:NOTE'           => new NoteStructure(I18N::translate('Note')),
                'INDI:BAPL:PLAC'           => new PlaceLivingOrdinance(I18N::translate('Place of LDS baptism')),
                'INDI:BAPL:STAT'           => new LdsBaptismDateStatus(I18N::translate('Status')),
                'INDI:BAPL:STAT:DATE'      => new ChangeDate(I18N::translate('Status change date')),
                'INDI:BAPL:TEMP'           => new TempleCode(I18N::translate('Temple')),
                'INDI:BAPM'                => new Baptism(I18N::translate('Baptism')),
                'INDI:BAPM:DATE'           => new DateValue(I18N::translate('Date of baptism')),
                'INDI:BAPM:PLAC'           => new PlaceName(I18N::translate('Place of baptism')),
                'INDI:BARM'                => new PlaceName(I18N::translate('Bar mitzvah')),
                'INDI:BARM:DATE'           => new DateValue(I18N::translate('Date of bar mitzvah')),
                'INDI:BARM:PLAC'           => new PlaceName(I18N::translate('Place of bar mitzvah')),
                'INDI:BASM'                => new BasMitzvah(I18N::translate('Bat mitzvah')),
                'INDI:BASM:DATE'           => new BasMitzvah(I18N::translate('Date of bat mitzvah')),
                'INDI:BASM:PLAC'           => new DateValue(I18N::translate('Place of bat mitzvah')),
                'INDI:BIRT'                => new Birth(I18N::translate('Birth')),
                'INDI:BIRT:DATE'           => new DateValue(I18N::translate('Date of birth')),
                'INDI:BIRT:FAMC'           => new XrefFamily(I18N::translate('Birth parents')),
                'INDI:BIRT:PLAC'           => new PlaceName(I18N::translate('Place of birth')),
                'INDI:BLES'                => new Blessing(I18N::translate('Blessing')),
                'INDI:BLES:DATE'           => new DateValue(I18N::translate('Date of blessing')),
                'INDI:BLES:PLAC'           => new PlaceName(I18N::translate('Place of blessing')),
                'INDI:BURI'                => new Burial(I18N::translate('Burial')),
                'INDI:BURI:DATE'           => new DateValue(I18N::translate('Date of burial')),
                'INDI:BURI:PLAC'           => new PlaceName(I18N::translate('Place of burial')),
                'INDI:CAST'                => new CasteName(I18N::translate('Caste')),
                'INDI:CENS'                => new Census(I18N::translate('Census')),
                'INDI:CENS:DATE'           => new DateValue(I18N::translate('Census date')),
                'INDI:CENS:PLAC'           => new PlaceName(I18N::translate('Census place')),
                'INDI:CHAN'                => new Change(I18N::translate('Last change')),
                'INDI:CHAN:DATE'           => new ChangeDate(I18N::translate('Date of last change')),
                'INDI:CHAN:DATE:TIME'      => new TimeValue(I18N::translate('Time')),
                'INDI:CHAN:NOTE'           => new NoteStructure(I18N::translate('Note')),
                'INDI:CHR'                 => new Christening(I18N::translate('Christening')),
                'INDI:CHR:DATE'            => new DateValue(I18N::translate('Date of christening')),
                'INDI:CHR:FAMC'            => new XrefFamily(I18N::translate('Godparents')),
                'INDI:CHR:PLAC'            => new PlaceName(I18N::translate('Place of christening')),
                'INDI:CHRA'                => new AdultChristening(I18N::translate('Adult christening')),
                'INDI:CONF'                => new Confirmation(I18N::translate('Confirmation')),
                'INDI:CONF:DATE'           => new DateValue(I18N::translate('Date of confirmation')),
                'INDI:CONF:PLAC'           => new PlaceName(I18N::translate('Place of confirmation')),
                'INDI:CONL'                => new LdsConfirmation(I18N::translate('LDS confirmation')),
                'INDI:CONL:DATE'           => new DateLdsOrd(I18N::translate('Date of LDS confirmation')),
                'INDI:CONL:NOTE'           => new NoteStructure(I18N::translate('Note')),
                'INDI:CONL:PLAC'           => new PlaceLivingOrdinance(I18N::translate('Place of LDS confirmation')),
                'INDI:CONL:STAT'           => new LdsSpouseSealingDateStatus(I18N::translate('Status')),
                'INDI:CONL:STAT:DATE'      => new ChangeDate(I18N::translate('Status change date')),
                'INDI:CONL:TEMP'           => new TempleCode(I18N::translate('Temple')),
                'INDI:CREM'                => new Cremation(I18N::translate('Cremation')),
                'INDI:CREM:DATE'           => new Cremation(I18N::translate('Date of cremation')),
                'INDI:CREM:PLAC'           => new Cremation(I18N::translate('Place of cremation')),
                'INDI:DEAT'                => new Death(I18N::translate('Death')),
                'INDI:DEAT:CAUS'           => new CauseOfEvent(I18N::translate('Cause of death')),
                'INDI:DEAT:DATE'           => new DateValue(I18N::translate('Date of death')),
                'INDI:DEAT:PLAC'           => new PlaceName(I18N::translate('Place of death')),
                'INDI:DESI'                => new XrefSubmitter(I18N::translate('Descendants interest')),
                'INDI:DSCR'                => new PhysicalDescription(I18N::translate('Description')),
                'INDI:EDUC'                => new ScholasticAchievement(I18N::translate('Education')),
                'INDI:EDUC:AGNC'           => new ResponsibleAgency(I18N::translate('School or college')),
                'INDI:EMIG'                => new Emigration(I18N::translate('Emigration')),
                'INDI:EMIG:DATE'           => new DateValue(I18N::translate('Date of emigration')),
                'INDI:EMIG:PLAC'           => new PlaceName(I18N::translate('Place of emigration')),
                'INDI:ENDL'                => new LdsEndowment(I18N::translate('LDS endowment')),
                'INDI:ENDL:DATE'           => new DateLdsOrd(I18N::translate('Date of LDS endowment')),
                'INDI:ENDL:NOTE'           => new NoteStructure(I18N::translate('Note')),
                'INDI:ENDL:PLAC'           => new PlaceLivingOrdinance(I18N::translate('Place of LDS endowment')),
                'INDI:ENDL:STAT'           => new LdsEndowmentDateStatus(I18N::translate('Status')),
                'INDI:ENDL:STAT:DATE'      => new ChangeDate(I18N::translate('Status change date')),
                'INDI:ENDL:TEMP'           => new TempleCode(I18N::translate('Temple')),
                'INDI:EVEN'                => new EventDescriptor(I18N::translate('Event')),
                'INDI:EVEN:DATE'           => new DateValue(I18N::translate('Date of event')),
                'INDI:EVEN:PLAC'           => new PlaceName(I18N::translate('Place of event')),
                'INDI:EVEN:TYPE'           => new EventAttributeType(I18N::translate('Type of event')),
                'INDI:FACT'                => new AttributeDescriptor(I18N::translate('Fact')),
                'INDI:FACT:TYPE'           => new EventAttributeType(I18N::translate('Type of fact')),
                'INDI:FAMC'                => new XrefFamily(I18N::translate('Family as a child')),
                'INDI:FAMC:NOTE'           => new NoteStructure(I18N::translate('Note')),
                'INDI:FAMC:PEDI'           => new PedigreeLinkageType(I18N::translate('Relationship to parents')),
                'INDI:FAMC:STAT'           => new ChildLinkageStatus(I18N::translate('Status')),
                'INDI:FAMS'                => new XrefFamily(I18N::translate('Family as a spouse')),
                'INDI:FCOM'                => new FirstCommunion(I18N::translate('First communion')),
                'INDI:FCOM:DATE'           => new DateValue(I18N::translate('Date of first communion')),
                'INDI:FCOM:PLAC'           => new PlaceName(I18N::translate('Place of first communion')),
                'INDI:GRAD'                => new Graduation(I18N::translate('Graduation')),
                'INDI:GRAD:AGNC'           => new ResponsibleAgency(I18N::translate('School or college')),
                'INDI:IDNO'                => new NationalIdNumber(I18N::translate('Identification number')),
                'INDI:IMMI'                => new Immigration(I18N::translate('Immigration')),
                'INDI:IMMI:DATE'           => new DateValue(I18N::translate('Date of immigration')),
                'INDI:IMMI:PLAC'           => new PlaceName(I18N::translate('Place of immigration')),
                'INDI:NAME'                => new NamePersonal(I18N::translate('Name')),
                'INDI:NAME:FONE'           => new NamePhoneticVariation(I18N::translate('Phonetic name'), ['TYPE' => '0,1']),
                'INDI:NAME:FONE:GIVN'      => new NamePieceGiven(I18N::translate('Given names')),
                'INDI:NAME:FONE:NICK'      => new NamePieceNickname(I18N::translate('Nickname')),
                'INDI:NAME:FONE:NPFX'      => new NamePiecePrefix(I18N::translate('Name prefix')),
                'INDI:NAME:FONE:NSFX'      => new NamePieceSuffix(I18N::translate('Name suffix')),
                'INDI:NAME:FONE:SPFX'      => new NamePieceSurnamePrefix(I18N::translate('Surname prefix')),
                'INDI:NAME:FONE:SURN'      => new NamePieceSurname(I18N::translate('Surname')),
                'INDI:NAME:FONE:TYPE'      => new PhoneticType(I18N::translate('Phonetic type')),
                'INDI:NAME:GIVN'           => new NamePieceGiven(I18N::translate('Given names')),
                'INDI:NAME:NICK'           => new NamePieceNickname(I18N::translate('Nickname')),
                'INDI:NAME:NOTE'           => new NoteStructure(I18N::translate('Note')),
                'INDI:NAME:NPFX'           => new NamePiecePrefix(I18N::translate('Name prefix')),
                'INDI:NAME:NSFX'           => new NamePieceSuffix(I18N::translate('Name suffix')),
                'INDI:NAME:ROMN'           => new NameRomanizedVariation(I18N::translate('Romanized name'), ['TYPE' => '0,1']),
                'INDI:NAME:ROMN:GIVN'      => new NamePieceGiven(I18N::translate('Given names')),
                'INDI:NAME:ROMN:NICK'      => new NamePieceNickname(I18N::translate('Nickname')),
                'INDI:NAME:ROMN:NPFX'      => new NamePiecePrefix(I18N::translate('Name prefix')),
                'INDI:NAME:ROMN:NSFX'      => new NamePieceSuffix(I18N::translate('Name suffix')),
                'INDI:NAME:ROMN:SPFX'      => new NamePieceSurnamePrefix(I18N::translate('Surname prefix')),
                'INDI:NAME:ROMN:SURN'      => new NamePieceSurname(I18N::translate('Surname')),
                'INDI:NAME:ROMN:TYPE'      => new RomanizedType(I18N::translate('Romanized type')),
                'INDI:NAME:SPFX'           => new NamePieceSurnamePrefix(I18N::translate('Surname prefix')),
                'INDI:NAME:SURN'           => new NamePieceSurname(I18N::translate('Surname')),
                'INDI:NAME:TYPE'           => new NameType(I18N::translate('Type of name')),
                'INDI:NATI'                => new NationOrTribalOrigin(I18N::translate('Nationality')),
                'INDI:NATU'                => new Naturalization(I18N::translate('Naturalization')),
                'INDI:NATU:DATE'           => new DateValue(I18N::translate('Date of naturalization')),
                'INDI:NATU:PLAC'           => new PlaceName(I18N::translate('Place of naturalization')),
                'INDI:NCHI'                => new CountOfChildren(I18N::translate('Number of children')),
                'INDI:NMR'                 => new CountOfMarriages(I18N::translate('Number of marriages')),
                'INDI:NOTE'                => new NoteStructure(I18N::translate('Note')),
                'INDI:OBJE'                => new XrefMedia(I18N::translate('Media object')),
                'INDI:OCCU'                => new Occupation(I18N::translate('Occupation')),
                'INDI:OCCU:AGNC'           => new ResponsibleAgency(I18N::translate('Employer')),
                'INDI:ORDN'                => new Ordination(I18N::translate('Ordination')),
                'INDI:ORDN:AGNC'           => new Ordination(I18N::translate('Religious institution')),
                'INDI:ORDN:DATE'           => new Ordination(I18N::translate('Date of ordination')),
                'INDI:ORDN:PLAC'           => new Ordination(I18N::translate('Place of ordination')),
                'INDI:PROB'                => new Probate(I18N::translate('Probate')),
                'INDI:PROP'                => new Possessions(I18N::translate('Property')),
                'INDI:REFN'                => new UserReferenceNumber(I18N::translate('Reference number')),
                'INDI:REFN:TYPE'           => new UserReferenceType(I18N::translate('Type')),
                'INDI:RELI'                => new ReligiousAffiliation(I18N::translate('Religion')),
                'INDI:RESI'                => new Residence(I18N::translate('Residence')),
                'INDI:RESI:DATE'           => new DateValue(I18N::translate('Date of residence')),
                'INDI:RESI:PLAC'           => new PlaceName(I18N::translate('Place of residence')),
                'INDI:RESN'                => new RestrictionNotice(I18N::translate('Restriction')),
                'INDI:RETI'                => new Retirement(I18N::translate('Retirement')),
                'INDI:RETI:AGNC'           => new ResponsibleAgency(I18N::translate('Employer')),
                'INDI:RFN'                 => new PermanentRecordFileNumber(I18N::translate('Record file number')),
                'INDI:RIN'                 => new AutomatedRecordId(I18N::translate('Record ID number')),
                'INDI:SEX'                 => new SexValue(I18N::translate('Gender')),
                'INDI:SLGC'                => new LdsChildSealing(I18N::translate('LDS child sealing')),
                'INDI:SLGC:DATE'           => new DateLdsOrd(I18N::translate('Date of LDS child sealing')),
                'INDI:SLGC:FAMC'           => new XrefFamily(I18N::translate('Parents')),
                'INDI:SLGC:NOTE'           => new NoteStructure(I18N::translate('Note')),
                'INDI:SLGC:PLAC'           => new PlaceLivingOrdinance(I18N::translate('Place of LDS child sealing')),
                'INDI:SLGC:STAT'           => new LdsChildSealingDateStatus(I18N::translate('Status')),
                'INDI:SLGC:STAT:DATE'      => new ChangeDate(I18N::translate('Status change date')),
                'INDI:SLGC:TEMP'           => new TempleCode(I18N::translate('Temple')),
                'INDI:SOUR'                => new XrefSource(I18N::translate('Source')),
                'INDI:SOUR:DATA'           => new SourceData(I18N::translate('Data')),
                'INDI:SOUR:DATA:DATE'      => new EntryRecordingDate(I18N::translate('Date of entry in original source')),
                'INDI:SOUR:DATA:TEXT'      => new TextFromSource(I18N::translate('Text')),
                'INDI:SOUR:EVEN'           => new EventTypeCitedFrom(I18N::translate('Event')),
                'INDI:SOUR:EVEN:ROLE'      => new RoleInEvent(I18N::translate('Role')),
                'INDI:SOUR:NOTE'           => new NoteStructure(I18N::translate('Note')),
                'INDI:SOUR:OBJE'           => new XrefMedia(I18N::translate('Media object')),
                'INDI:SOUR:PAGE'           => new WhereWithinSource(I18N::translate('Citation details')),
                'INDI:SOUR:QUAY'           => new CertaintyAssessment(I18N::translate('Quality of data')),
                'INDI:SSN'                 => new SocialSecurityNumber(I18N::translate('Social security number')),
                'INDI:SUBM'                => new XrefSubmitter(I18N::translate('Submitter')),
                'INDI:TITL'                => new NobilityTypeTitle(I18N::translate('Title')),
                'INDI:WILL'                => new Will(I18N::translate('Will')),
                'NOTE'                     => new NoteRecord(I18N::translate('Note')),
                'NOTE:CHAN'                => new Change(I18N::translate('Last change')),
                'NOTE:CHAN:DATE'           => new ChangeDate(I18N::translate('Date of last change')),
                'NOTE:CHAN:DATE:TIME'      => new TimeValue(I18N::translate('Time')),
                'NOTE:CHAN:NOTE'           => new NoteStructure(I18N::translate('Note')),
                'NOTE:CONC'                => new SubmitterText(I18N::translate('Note')),
                'NOTE:CONT'                => new SubmitterText(I18N::translate('Continued')),
                'NOTE:REFN'                => new UserReferenceNumber(I18N::translate('Reference number')),
                'NOTE:REFN:TYPE'           => new UserReferenceType(I18N::translate('Type')),
                'NOTE:RIN'                 => new AutomatedRecordId(I18N::translate('Record ID number')),
                'NOTE:SOUR'                => new XrefSource(I18N::translate('Source')),
                'NOTE:SOUR:DATA'           => new SourceData(I18N::translate('Data')),
                'NOTE:SOUR:DATA:DATE'      => new EntryRecordingDate(I18N::translate('Date of entry in original source')),
                'NOTE:SOUR:DATA:TEXT'      => new TextFromSource(I18N::translate('Text')),
                'NOTE:SOUR:EVEN'           => new EventTypeCitedFrom(I18N::translate('Event')),
                'NOTE:SOUR:EVEN:ROLE'      => new RoleInEvent(I18N::translate('Role')),
                'NOTE:SOUR:NOTE'           => new NoteStructure(I18N::translate('Note')),
                'NOTE:SOUR:OBJE'           => new XrefMedia(I18N::translate('Media object')),
                'NOTE:SOUR:PAGE'           => new WhereWithinSource(I18N::translate('Citation details')),
                'NOTE:SOUR:QUAY'           => new CertaintyAssessment(I18N::translate('Quality of data')),
                'OBJE'                     => new MediaRecord(I18N::translate('Media object')),
                'OBJE:CHAN'                => new Change(I18N::translate('Last change')),
                'OBJE:CHAN:DATE'           => new ChangeDate(I18N::translate('Date of last change')),
                'OBJE:CHAN:DATE:TIME'      => new TimeValue(I18N::translate('Time')),
                'OBJE:CHAN:NOTE'           => new NoteStructure(I18N::translate('Note')),
                'OBJE:FILE'                => new MultimediaFileReference(I18N::translate('Filename')),
                'OBJE:FILE:FORM'           => new MultimediaFormat(I18N::translate('Format')),
                'OBJE:FILE:FORM:TYPE'      => new SourceMediaType(I18N::translate('Media type')),
                'OBJE:FILE:TITL'           => new DescriptiveTitle(I18N::translate('Title')),
                'OBJE:NOTE'                => new NoteStructure(I18N::translate('Note')),
                'OBJE:REFN'                => new UserReferenceNumber(I18N::translate('Reference number')),
                'OBJE:REFN:TYPE'           => new UserReferenceType(I18N::translate('Type')),
                'OBJE:RIN'                 => new AutomatedRecordId(I18N::translate('Record ID number')),
                'OBJE:SOUR'                => new XrefSource(I18N::translate('Source')),
                'OBJE:SOUR:DATA'           => new SourceData(I18N::translate('Data')),
                'OBJE:SOUR:DATA:DATE'      => new EntryRecordingDate(I18N::translate('Date of entry in original source')),
                'OBJE:SOUR:DATA:TEXT'      => new TextFromSource(I18N::translate('Text')),
                'OBJE:SOUR:EVEN'           => new EventTypeCitedFrom(I18N::translate('Event')),
                'OBJE:SOUR:EVEN:ROLE'      => new RoleInEvent(I18N::translate('Role')),
                'OBJE:SOUR:NOTE'           => new NoteStructure(I18N::translate('Note')),
                'OBJE:SOUR:OBJE'           => new XrefMedia(I18N::translate('Media object')),
                'OBJE:SOUR:PAGE'           => new WhereWithinSource(I18N::translate('Citation details')),
                'OBJE:SOUR:QUAY'           => new CertaintyAssessment(I18N::translate('Quality of data')),
                'REPO'                     => new RepositoryRecord(I18N::translate('Repository')),
                'REPO:ADDR'                => new AddressLine(I18N::translate('Address')),
                'REPO:ADDR:ADR1'           => new AddressLine1(I18N::translate('Address line 1')),
                'REPO:ADDR:ADR2'           => new AddressLine2(I18N::translate('Address line 2')),
                'REPO:ADDR:ADR3'           => new AddressLine3(I18N::translate('Address line 3')),
                'REPO:ADDR:CITY'           => new AddressCity(I18N::translate('City')),
                'REPO:ADDR:CTRY'           => new AddressCountry(I18N::translate('Country')),
                'REPO:ADDR:POST'           => new AddressPostalCode(I18N::translate('Postal code')),
                'REPO:ADDR:STAE'           => new AddressState(I18N::translate('State')),
                'REPO:CHAN'                => new Change(I18N::translate('Last change')),
                'REPO:CHAN:DATE'           => new ChangeDate(I18N::translate('Date of last change')),
                'REPO:CHAN:DATE:TIME'      => new TimeValue(I18N::translate('Time')),
                'REPO:CHAN:NOTE'           => new NoteStructure(I18N::translate('Note')),
                'REPO:EMAIL'               => new AddressEmail(I18N::translate('Email address')),
                'REPO:FAX'                 => new AddressFax(I18N::translate('Fax')),
                'REPO:NAME'                => new NameOfRepository(I18N::translateContext('Repository', 'Name')),
                'REPO:NOTE'                => new NoteStructure(I18N::translate('Note')),
                'REPO:PHON'                => new PhoneNumber(I18N::translate('Phone')),
                'REPO:REFN'                => new UserReferenceNumber(I18N::translate('Reference number')),
                'REPO:REFN:TYPE'           => new UserReferenceType(I18N::translate('Type')),
                'REPO:RIN'                 => new AutomatedRecordId(I18N::translate('Record ID number')),
                'REPO:WWW'                 => new AddressWebPage(I18N::translate('URL')),
                'SOUR'                     => new SourceRecord(I18N::translate('Source')),
                'SOUR:ABBR'                => new SourceFiledByEntry(I18N::translate('Abbreviation')),
                'SOUR:AUTH'                => new SourceOriginator(I18N::translate('Author')),
                'SOUR:CHAN'                => new Change(I18N::translate('Last change')),
                'SOUR:CHAN:DATE'           => new ChangeDate(I18N::translate('Date of last change')),
                'SOUR:CHAN:DATE:TIME'      => new TimeValue(I18N::translate('Time')),
                'SOUR:CHAN:NOTE'           => new NoteStructure(I18N::translate('Note')),
                'SOUR:DATA'                => new EmptyElement(I18N::translate('Data'), ['EVEN' => '0:M', 'AGNC' => '0:1', 'NOTE' => '0:M']),
                'SOUR:DATA:AGNC'           => new ResponsibleAgency(I18N::translate('Agency')),
                'SOUR:DATA:EVEN'           => new EventsRecorded(I18N::translate('Events')),
                'SOUR:DATA:EVEN:DATE'      => new DateValue(I18N::translate('Date range')),
                'SOUR:DATA:EVEN:PLAC'      => new SourceJurisdictionPlace(I18N::translate('Place')),
                'SOUR:DATA:NOTE'           => new NoteStructure(I18N::translate('Note')),
                'SOUR:NOTE'                => new NoteStructure(I18N::translate('Note')),
                'SOUR:OBJE'                => new XrefMedia(I18N::translate('Media object')),
                'SOUR:PUBL'                => new SourcePublicationFacts(I18N::translate('Publication')),
                'SOUR:REFN'                => new UserReferenceNumber(I18N::translate('Reference number')),
                'SOUR:REFN:TYPE'           => new UserReferenceType(I18N::translate('Type')),
                'SOUR:REPO'                => new XrefRepository(I18N::translate('Repository')),
                'SOUR:REPO:CALN'           => new SourceCallNumber(I18N::translate('Call number')),
                'SOUR:REPO:CALN:MEDI'      => new SourceMediaType(I18N::translate('Media type')),
                'SOUR:REPO:NOTE'           => new NoteStructure(I18N::translate('Note')),
                'SOUR:RIN'                 => new AutomatedRecordId(I18N::translate('Record ID number')),
                'SOUR:TEXT'                => new TextFromSource(I18N::translate('Text')),
                'SOUR:TITL'                => new DescriptiveTitle(I18N::translate('Title')),
                'SUBM'                     => new SubmitterRecord(I18N::translate('Submitter')),
                'SUBM:ADDR'                => new AddressLine(I18N::translate('Address')),
                'SUBM:ADDR:ADR1'           => new AddressLine1(I18N::translate('Address line 1')),
                'SUBM:ADDR:ADR2'           => new AddressLine2(I18N::translate('Address line 2')),
                'SUBM:ADDR:ADR3'           => new AddressLine3(I18N::translate('Address line 3')),
                'SUBM:ADDR:CITY'           => new AddressCity(I18N::translate('City')),
                'SUBM:ADDR:CTRY'           => new AddressCountry(I18N::translate('Country')),
                'SUBM:ADDR:POST'           => new AddressPostalCode(I18N::translate('Postal code')),
                'SUBM:ADDR:STAE'           => new AddressState(I18N::translate('State')),
                'SUBM:CHAN'                => new Change(I18N::translate('Last change')),
                'SUBM:CHAN:DATE'           => new ChangeDate(I18N::translate('Date of last change')),
                'SUBM:CHAN:DATE:TIME'      => new TimeValue(I18N::translate('Time')),
                'SUBM:CHAN:NOTE'           => new NoteStructure(I18N::translate('Note')),
                'SUBM:EMAIL'               => new AddressEmail(I18N::translate('Email address')),
                'SUBM:FAX'                 => new AddressFax(I18N::translate('Fax')),
                'SUBM:LANG'                => new LanguageId(I18N::translate('Language')),
                'SUBM:NAME'                => new SubmitterName(I18N::translate('Name')),
                'SUBM:NOTE'                => new NoteStructure(I18N::translate('Note')),
                'SUBM:OBJE'                => new XrefMedia(I18N::translate('Media object')),
                'SUBM:PHON'                => new PhoneNumber(I18N::translate('Phone')),
                'SUBM:RFN'                 => new SubmitterRegisteredRfn(I18N::translate('Record file number')),
                'SUBM:RIN'                 => new AutomatedRecordId(I18N::translate('Record ID number')),
                'SUBM:WWW'                 => new AddressWebPage(I18N::translate('URL')),
                'SUBN'                     => new SubmissionRecord(I18N::translate('Submission')),
                'SUBN:ANCE'                => new GenerationsOfAncestors(I18N::translate('Generations of ancestors')),
                'SUBN:CHAN'                => new Change(I18N::translate('Last change')),
                'SUBN:CHAN:DATE'           => new ChangeDate(I18N::translate('Date of last change')),
                'SUBN:CHAN:DATE:TIME'      => new TimeValue(I18N::translate('Time')),
                'SUBN:CHAN:NOTE'           => new NoteStructure(I18N::translate('Note')),
                'SUBN:DESC'                => new GenerationsOfDescendants(I18N::translate('Generations of descendants')),
                'SUBN:FAMF'                => new NameOfFamilyFile(I18N::translate('Family file')),
                'SUBN:NOTE'                => new NoteStructure(I18N::translate('Note')),
                'SUBN:ORDI'                => new OrdinanceProcessFlag(I18N::translate('Ordinance')),
                'SUBN:RIN'                 => new AutomatedRecordId(I18N::translate('Record ID number')),
                'SUBN:SUBM'                => new XrefSubmitter(I18N::translate('Submitter')),
                'SUBN:TEMP'                => new TempleCode(/* I18N: https://en.wikipedia.org/wiki/Temple_(LDS_Church)*/ I18N::translate('Temple')),
                'TRLR'                     => new EmptyElement(I18N::translate('Trailer')),
            ];

            // Aldfaer extensions
            $this->register([
                'FAM:MARR_CIVIL'     => new CustomEvent(I18N::translate('Civil marriage')),
                'FAM:MARR_RELIGIOUS' => new CustomEvent(I18N::translate('Religious marriage')),
                'FAM:MARR_PARTNERS'  => new CustomEvent(I18N::translate('Registered partnership')),
                'FAM:MARR_UNKNOWN'   => new CustomEvent(I18N::translate('Marriage type unknown')),
            ]);

            // Ancestry extensions
            $this->register([
                'INDI:*:SOUR:_APID' => new CustomElement(I18N::translate('Ancestry PID')),
                'INDI:_EMPLOY'      => new CustomEvent(I18N::translate('Occupation')),
            ]);

            // Brother’s Keeper extensions
            $this->register([
                'FAM:*:_EVN'       => new CustomElement('Event number'),
                'FAM:CHIL:_FREL'   => new CustomElement('Relationship to father'),
                'FAM:CHIL:_MREL'   => new CustomElement('Relationship to mother'),
                'FAM:_COML'        => new CustomEvent(I18N::translate('Common law marriage')),
                'FAM:_MARI'        => new CustomEvent(I18N::translate('Marriage intention')),
                'FAM:_MBON'        => new CustomEvent(I18N::translate('Marriage bond')),
                'FAM:_NMR'         => new CustomEvent(I18N::translate('Not married'), ['NOTE' => '0:M', 'SOUR' => '0:M']),
                'FAM:_PRMN'        => new CustomElement(I18N::translate('Permanent number')),
                'FAM:_SEPR'        => new CustomEvent(I18N::translate('Separated')),
                'FAM:_TODO'        => new CustomElement(I18N::translate('Research task')),
                'INDI:*:_EVN'      => new CustomElement('Event number'),
                'INDI:NAME:_ADPN'  => new NamePersonal(I18N::translate('Adopted name')),
                'INDI:NAME:_AKAN'  => new NamePersonal(I18N::translate('Also known as')),
                'INDI:NAME:_BIRN'  => new NamePersonal(I18N::translate('Birth name')),
                'INDI:NAME:_CALL'  => new NamePersonal('Called name'),
                'INDI:NAME:_CENN'  => new NamePersonal('Census name'),
                'INDI:NAME:_CURN'  => new NamePersonal('Current name'),
                'INDI:NAME:_FARN'  => new NamePersonal(I18N::translate('Estate name')),
                'INDI:NAME:_FKAN'  => new NamePersonal('Formal name'),
                'INDI:NAME:_FRKA'  => new NamePersonal('Formerly known as'),
                'INDI:NAME:_GERN'  => new NamePersonal('German name'),
                'INDI:NAME:_HEBN'  => new NamePersonal(I18N::translate('Hebrew name')),
                'INDI:NAME:_HNM'   => new NamePersonal(I18N::translate('Hebrew name')),
                'INDI:NAME:_INDG'  => new NamePersonal('Indigenous name'),
                'INDI:NAME:_INDN'  => new NamePersonal('Indian name'),
                'INDI:NAME:_LNCH'  => new NamePersonal('Legal name change'),
                'INDI:NAME:_MARN'  => new NamePersonal('Married name'),
                'INDI:NAME:_MARNM' => new NamePersonal('Married name'),
                'INDI:NAME:_OTHN'  => new NamePersonal('Other name'),
                'INDI:NAME:_RELN'  => new NamePersonal('Religious name'),
                'INDI:NAME:_SHON'  => new NamePersonal('Short name'),
                'INDI:NAME:_SLDN'  => new NamePersonal('Soldier name'),
                'INDI:_ADPF'       => new CustomElement(I18N::translate('Adopted by father')),
                'INDI:_ADPM'       => new CustomElement(I18N::translate('Adopted by mother')),
                'INDI:_BRTM'       => new CustomEvent(I18N::translate('Brit milah')),
                'INDI:_BRTM:DATE'  => new DateValue(I18N::translate('Date of brit milah')),
                'INDI:_BRTM:PLAC'  => new PlaceName(I18N::translate('Place of brit milah')),
                'INDI:_EMAIL'      => new AddressEmail(I18N::translate('Email address')),
                'INDI:_EYEC'       => new CustomFact(I18N::translate('Eye color')),
                'INDI:_FRNL'       => new CustomElement(I18N::translate('Funeral')),
                'INDI:_HAIR'       => new CustomFact(I18N::translate('Hair color')),
                'INDI:_HEIG'       => new CustomFact(I18N::translate('Height')),
                'INDI:_INTE'       => new CustomElement(I18N::translate('Interment')),
                'INDI:_MEDC'       => new CustomFact(I18N::translate('Medical')),
                'INDI:_MILT'       => new CustomElement(I18N::translate('Military service')),
                'INDI:_NLIV'       => new CustomFact(I18N::translate('Not living')),
                'INDI:_NMAR'       => new CustomEvent(I18N::translate('Never married'), ['NOTE' => '0:M', 'SOUR' => '0:M']),
                'INDI:_PRMN'       => new CustomElement(I18N::translate('Permanent number')),
                'INDI:_TODO'       => new CustomElement(I18N::translate('Research task')),
                'INDI:_WEIG'       => new CustomFact(I18N::translate('Weight')),
                'INDI:_YART'       => new CustomEvent(I18N::translate('Yahrzeit')),
                // 1 XXXX
                // 2 _EVN ##
                // 1 ASSO @Xnnn@
                // 2 RELA Witness at event _EVN ##
            ]);

            // Family Tree Builder extensions
            $this->register([
                '*:_UPD'              => new CustomElement(I18N::translate('Last change')), // e.g. "1 _UPD 14 APR 2012 00:14:10 GMT-5"
                'INDI:NAME:_AKA'      => new NamePersonal(I18N::translate('Also known as')),
                'OBJE:_ALBUM'         => new CustomElement(I18N::translate('Album')), // XREF to an album
                'OBJE:_DATE'          => new DateValue(I18N::translate('Date')),
                'OBJE:_FILESIZE'      => new CustomElement(I18N::translate('File size')),
                'OBJE:_PHOTO_RIN'     => new CustomElement(I18N::translate('Photo')),
                'OBJE:_PLACE'         => new PlaceName(I18N::translate('Place')),
                '_ALBUM:_PHOTO'       => new CustomElement(I18N::translate('Photo')),
                '_ALBUM:_PHOTO:_PRIN' => new CustomElement(I18N::translate('Highlighted image')),
            ]);

            // Family Tree Maker extensions
            $this->register([
                'FAM:CHIL:_FREL'              => new CustomElement(I18N::translate('Relationship to father')),
                'FAM:CHIL:_MREL'              => new CustomElement(I18N::translate('Relationship to mother')),
                'FAM:_DETS'                   => new CustomElement(I18N::translate('Death of one spouse')),
                'FAM:_FA1'                    => new CustomElement('Fact 1'),
                'FAM:_FA10'                   => new CustomElement('Fact 10'),
                'FAM:_FA11'                   => new CustomElement('Fact 11'),
                'FAM:_FA12'                   => new CustomElement('Fact 12'),
                'FAM:_FA13'                   => new CustomElement('Fact 13'),
                'FAM:_FA2'                    => new CustomElement('Fact 2'),
                'FAM:_FA3'                    => new CustomElement('Fact 3'),
                'FAM:_FA4'                    => new CustomElement('Fact 4'),
                'FAM:_FA5'                    => new CustomElement('Fact 5'),
                'FAM:_FA6'                    => new CustomElement('Fact 6'),
                'FAM:_FA7'                    => new CustomElement('Fact 7'),
                'FAM:_FA8'                    => new CustomElement('Fact 8'),
                'FAM:_FA9'                    => new CustomElement('Fact 9'),
                'FAM:_MEND'                   => new CustomElement(I18N::translate('Marriage ending status')),
                'FAM:_MSTAT'                  => new CustomElement(I18N::translate('Marriage beginning status')),
                'FAM:_SEPR'                   => new CustomElement(I18N::translate('Separation')),
                'HEAD:_SCHEMA'                => new CustomElement('Schema'),
                'HEAD:_SCHEMA:FAM'            => new CustomElement(I18N::translate('Family')),
                'HEAD:_SCHEMA:FAM:_FA*:LABL'  => new CustomElement(I18N::translate('Label')),
                'HEAD:_SCHEMA:FAM:_FA1'       => new CustomElement(I18N::translate('Fact 1')),
                'HEAD:_SCHEMA:FAM:_FA10'      => new CustomElement(I18N::translate('Fact 10')),
                'HEAD:_SCHEMA:FAM:_FA11'      => new CustomElement(I18N::translate('Fact 11')),
                'HEAD:_SCHEMA:FAM:_FA12'      => new CustomElement(I18N::translate('Fact 12')),
                'HEAD:_SCHEMA:FAM:_FA13'      => new CustomElement(I18N::translate('Fact 13')),
                'HEAD:_SCHEMA:FAM:_FA2'       => new CustomElement(I18N::translate('Fact 2')),
                'HEAD:_SCHEMA:FAM:_FA3'       => new CustomElement(I18N::translate('Fact 3')),
                'HEAD:_SCHEMA:FAM:_FA4'       => new CustomElement(I18N::translate('Fact 4')),
                'HEAD:_SCHEMA:FAM:_FA5'       => new CustomElement(I18N::translate('Fact 5')),
                'HEAD:_SCHEMA:FAM:_FA6'       => new CustomElement(I18N::translate('Fact 6')),
                'HEAD:_SCHEMA:FAM:_FA7'       => new CustomElement(I18N::translate('Fact 7')),
                'HEAD:_SCHEMA:FAM:_FA8'       => new CustomElement(I18N::translate('Fact 8')),
                'HEAD:_SCHEMA:FAM:_FA9'       => new CustomElement(I18N::translate('Fact 9')),
                'HEAD:_SCHEMA:FAM:_M*:LABL'   => new CustomElement(I18N::translate('Label')),
                'HEAD:_SCHEMA:FAM:_MEND'      => new CustomElement(I18N::translate('Marriage ending status')),
                'HEAD:_SCHEMA:FAM:_MSTAT'     => new CustomElement(I18N::translate('Marriage beginning status')),
                'HEAD:_SCHEMA:INDI'           => new CustomElement(I18N::translate('Individual')),
                'HEAD:_SCHEMA:INDI:_FA*:LABL' => new CustomElement(I18N::translate('Label')),
                'HEAD:_SCHEMA:INDI:_FA1'      => new CustomElement(I18N::translate('Fact 1')),
                'HEAD:_SCHEMA:INDI:_FA10'     => new CustomElement(I18N::translate('Fact 10')),
                'HEAD:_SCHEMA:INDI:_FA11'     => new CustomElement(I18N::translate('Fact 11')),
                'HEAD:_SCHEMA:INDI:_FA12'     => new CustomElement(I18N::translate('Fact 12')),
                'HEAD:_SCHEMA:INDI:_FA13'     => new CustomElement(I18N::translate('Fact 13')),
                'HEAD:_SCHEMA:INDI:_FA2'      => new CustomElement(I18N::translate('Fact 2')),
                'HEAD:_SCHEMA:INDI:_FA3'      => new CustomElement(I18N::translate('Fact 3')),
                'HEAD:_SCHEMA:INDI:_FA4'      => new CustomElement(I18N::translate('Fact 4')),
                'HEAD:_SCHEMA:INDI:_FA5'      => new CustomElement(I18N::translate('Fact 5')),
                'HEAD:_SCHEMA:INDI:_FA6'      => new CustomElement(I18N::translate('Fact 6')),
                'HEAD:_SCHEMA:INDI:_FA7'      => new CustomElement(I18N::translate('Fact 7')),
                'HEAD:_SCHEMA:INDI:_FA8'      => new CustomElement(I18N::translate('Fact 8')),
                'HEAD:_SCHEMA:INDI:_FA9'      => new CustomElement(I18N::translate('Fact 9')),
                'HEAD:_SCHEMA:INDI:_FREL'     => new CustomElement('Relationship to father'),
                'HEAD:_SCHEMA:INDI:_M*:LABL'  => new CustomElement(I18N::translate('Label')),
                'HEAD:_SCHEMA:INDI:_MREL'     => new CustomElement('Relationship to mother'),
                'INDI:*:SOUR:_APID'           => new CustomElement('Ancestry.com source identifier'),
                'INDI:*:SOUR:_LINK'           => new CustomElement('External link'),
                'INDI:NAME:_AKA'              => new NamePersonal(I18N::translate('Also known as')),
                'INDI:NAME:_MARNM'            => new NamePersonal(I18N::translate('Married name')),
                'INDI:_CIRC'                  => new CustomElement('Circumcision'),
                'INDI:_DCAUSE'                => new CustomElement(I18N::translate('Cause of death')),
                'INDI:_DEG'                   => new CustomElement(I18N::translate('Degree')),
                'INDI:_DNA'                   => new CustomElement(I18N::translate('DNA markers')),
                'INDI:_ELEC'                  => new CustomElement('Elected'),
                'INDI:_EMPLOY'                => new CustomElement('Employment'),
                'INDI:_EXCM'                  => new CustomElement('Excommunicated'),
                'INDI:_FA1'                   => new CustomElement('Fact 1'),
                'INDI:_FA10'                  => new CustomElement('Fact 10'),
                'INDI:_FA11'                  => new CustomElement('Fact 11'),
                'INDI:_FA12'                  => new CustomElement('Fact 12'),
                'INDI:_FA13'                  => new CustomElement('Fact 13'),
                'INDI:_FA2'                   => new CustomElement('Fact 2'),
                'INDI:_FA3'                   => new CustomElement('Fact 3'),
                'INDI:_FA4'                   => new CustomElement('Fact 4'),
                'INDI:_FA5'                   => new CustomElement('Fact 5'),
                'INDI:_FA6'                   => new CustomElement('Fact 6'),
                'INDI:_FA7'                   => new CustomElement('Fact 7'),
                'INDI:_FA8'                   => new CustomElement('Fact 8'),
                'INDI:_FA9'                   => new CustomElement('Fact 9'),
                'INDI:_MDCL'                  => new CustomElement('Medical'),
                'INDI:_MILT'                  => new CustomElement(I18N::translate('Military service')),
                'INDI:_MILTID'                => new CustomElement('Military ID number'),
                'INDI:_MISN'                  => new CustomElement('Mission'),
                'INDI:_NAMS'                  => new CustomElement(I18N::translate('Namesake')),
                'INDI:_UNKN'                  => new CustomElement(I18N::translate('Unknown')), // Special individual ID code for later file comparisons
                // The context and meaning of these tags is unknown
                '_FOOT'                       => new CustomElement(''),
                '_FUN'                        => new CustomElement(''),
                '_JUST'                       => new CustomElement(''),
                '_PHOTO'                      => new CustomElement(''),
            ]);

            // Gedcom 5.3 extensions
            $this->register([
                'EVEN'                       => new CustomElement('Event'),
                'EVEN:*:*:NAME'              => new NamePersonal(I18N::translate('Name')),
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
            ]);

            // Gedcom 5.5 extensions
            $this->register([
                'OBJE:BLOB' => new UnknownElement(I18N::translate('Binary data object')),
            ]);

            // Gedcom-L extensions
            $this->register([
                'FAM:*:ADDR:_NAME'               => new CustomElement('Name of addressee'),
                'FAM:*:PLAC:_GOV'                => new GovIdentifier(I18N::translate('GOV identifier')),
                'FAM:*:PLAC:_LOC'                => new XrefLocation(I18N::translate('Location')),
                'FAM:*:PLAC:_MAIDENHEAD'         => new MaidenheadLocator('Maidenhead locator'),
                'FAM:*:PLAC:_POST'               => new AddressPostalCode('Postal code'),
                'FAM:*:PLAC:_POST:DATE'          => new DateValue(I18N::translate('Date')),
                'FAM:*:_ASSO'                    => new XrefIndividual(I18N::translate('Associate')),
                'FAM:*:_ASSO:NOTE'               => new NoteStructure(I18N::translate('Note')),
                'FAM:*:_ASSO:RELA'               => new RelationIsDescriptor(I18N::translate('Relationship')),
                'FAM:*:_ASSO:SOUR'               => new XrefSource(I18N::translate('Source citation')),
                'FAM:_STAT'                      => new FamilyStatusText(I18N::translate('Family status')),
                'FAM:_STAT:DATE'                 => new DateValue(I18N::translate('Date')),
                'FAM:_STAT:NOTE'                 => new NoteStructure(I18N::translate('Note')),
                'FAM:_STAT:PLAC'                 => new PlaceName(I18N::translate('Place')),
                'FAM:_STAT:SOUR'                 => new XrefSource(I18N::translate('Source citation')),
                'FAM:_TODO'                      => new ResearchTask(I18N::translate('Research task')),
                'FAM:_TODO:DATA'                 => new SubmitterText(I18N::translate('The solution')),
                'FAM:_TODO:DATE'                 => new DateValue(I18N::translate('Creation date')),
                'FAM:_TODO:DESC'                 => new CustomElement(I18N::translate('Description')),
                'FAM:_TODO:NOTE'                 => new SubmitterText(I18N::translate('The problem')),
                'FAM:_TODO:REPO'                 => new XrefRepository('Repository'),
                'FAM:_TODO:STAT'                 => new ResearchTaskStatus(I18N::translate('Status')),
                'FAM:_TODO:TYPE'                 => new ResearchTaskType(I18N::translate('Type of research task')),
                'FAM:_TODO:_CAT'                 => new CustomElement(I18N::translate('Category')),
                'FAM:_TODO:_CDATE'               => new DateValue(I18N::translate('Completion date')),
                'FAM:_TODO:_PRTY'                => new ResearchTaskPriority(I18N::translate('Priority')),
                'FAM:_TODO:_RDATE'               => new DateValue(I18N::translate('Reminder date')),
                'FAM:_UID'                       => new PafUid(I18N::translate('Unique identifier')),
                'HEAD:SOUR:CORP:ADDR:_NAME'      => new CustomElement('Name of addressee'),
                'HEAD:_SCHEMA'                   => new EmptyElement(I18N::translate('Schema')),
                'HEAD:_SCHEMA:*'                 => new EmptyElement(I18N::translate('Base GEDCOM tag')),
                'HEAD:_SCHEMA:*:*'               => new EmptyElement(I18N::translate('New GEDCOM tag')),
                'HEAD:_SCHEMA:*:*:*'             => new EmptyElement(I18N::translate('New GEDCOM tag')),
                'HEAD:_SCHEMA:*:*:*:*'           => new EmptyElement(I18N::translate('New GEDCOM tag')),
                'HEAD:_SCHEMA:*:*:*:*:*'         => new EmptyElement(I18N::translate('New GEDCOM tag')),
                'HEAD:_SCHEMA:*:*:*:*:*:*'       => new EmptyElement(I18N::translate('New GEDCOM tag')),
                'HEAD:_SCHEMA:*:*:*:*:*:*:_DEFN' => new EmptyElement(I18N::translate('Definition')),
                'HEAD:_SCHEMA:*:*:*:*:*:_DEFN'   => new EmptyElement(I18N::translate('Definition')),
                'HEAD:_SCHEMA:*:*:*:*:_DEFN'     => new EmptyElement(I18N::translate('Definition')),
                'HEAD:_SCHEMA:*:*:*:_DEFN'       => new EmptyElement(I18N::translate('Definition')),
                'HEAD:_SCHEMA:*:*:_DEFN'         => new EmptyElement(I18N::translate('Definition')),
                'INDI:*:ADDR:_NAME'              => new CustomElement('Name of addressee'),
                'INDI:*:PLAC:_GOV'               => new GovIdentifier(I18N::translate('GOV identifier')),
                'INDI:*:PLAC:_LOC'               => new XrefLocation(I18N::translate('Location')),
                'INDI:*:PLAC:_MAIDENHEAD'        => new MaidenheadLocator('Maidenhead locator'),
                'INDI:*:PLAC:_POST'              => new AddressPostalCode('Postal code'),
                'INDI:*:PLAC:_POST:DATE'         => new DateValue(I18N::translate('Date')),
                'INDI:*:_ASSO'                   => new XrefIndividual(I18N::translate('Associate')),
                'INDI:*:_ASSO:NOTE'              => new NoteStructure(I18N::translate('Note')),
                'INDI:*:_ASSO:RELA'              => new RelationIsDescriptor(I18N::translate('Relationship')),
                'INDI:*:_ASSO:SOUR'              => new XrefSource(I18N::translate('Source citation')),
                'INDI:*:_WITN'                   => new CustomElement('Witness'),
                'INDI:BAPM:_GODP'                => new CustomElement('Godparent'),
                'INDI:CHR:_GODP'                 => new CustomElement('Godparent'),
                'INDI:NAME:_RUFNAME'             => new NamePieceGiven(I18N::translate('Rufname')),
                'INDI:OBJE:_PRIM'                => new CustomElement(I18N::translate('Highlighted image')),
                'INDI:SEX'                       => new SexXValue(I18N::translate('Gender')),
                'INDI:_TODO'                     => new ResearchTask(I18N::translate('Research task')),
                'INDI:_TODO:DATA'                => new SubmitterText(I18N::translate('The solution')),
                'INDI:_TODO:DATE'                => new DateValue(I18N::translate('Creation date')),
                'INDI:_TODO:DESC'                => new CustomElement(I18N::translate('Description')),
                'INDI:_TODO:NOTE'                => new SubmitterText(I18N::translate('The problem')),
                'INDI:_TODO:REPO'                => new XrefRepository('Repository'),
                'INDI:_TODO:STAT'                => new ResearchTaskStatus(I18N::translate('Status')),
                'INDI:_TODO:TYPE'                => new ResearchTaskType(I18N::translate('Type of research task')),
                'INDI:_TODO:_CAT'                => new CustomElement(I18N::translate('Category')),
                'INDI:_TODO:_CDATE'              => new DateValue(I18N::translate('Completion date')),
                'INDI:_TODO:_PRTY'               => new ResearchTaskPriority(I18N::translate('Priority')),
                'INDI:_TODO:_RDATE'              => new DateValue(I18N::translate('Reminder date')),
                'INDI:_UID'                      => new PafUid(I18N::translate('Unique identifier')),
                'NOTE:_UID'                      => new PafUid(I18N::translate('Unique identifier')),
                'OBJE:FILE:_PRIM'                => new CustomElement(I18N::translate('Highlighted image')),
                'OBJE:_UID'                      => new PafUid(I18N::translate('Unique identifier')),
                'REPO:ADDR:_NAME'                => new CustomElement('Name of addressee'),
                'REPO:_UID'                      => new PafUid(I18N::translate('Unique identifier')),
                'SOUR:_UID'                      => new PafUid(I18N::translate('Unique identifier')),
                'SUBM:ADDR:_NAME'                => new CustomElement('Name of addressee'),
                'SUBM:_UID'                      => new PafUid(I18N::translate('Unique identifier')),
                'SUBN:_UID'                      => new PafUid(I18N::translate('Unique identifier')),
                '_LOC'                           => new LocationRecord(I18N::translate('Location')),
                '_LOC::NOTE'                     => new NoteStructure(I18N::translate('Note')),
                '_LOC::OBJE'                     => new XrefMedia(I18N::translate('Media object')),
                '_LOC::SOUR'                     => new XrefSource(I18N::translate('Source')),
                '_LOC:CHAN'                      => new Change(I18N::translate('Last change')),
                '_LOC:CHAN:DATE'                 => new ChangeDate(I18N::translate('Date of last change')),
                '_LOC:CHAN:DATE:TIME'            => new TimeValue(I18N::translate('Time')),
                '_LOC:CHAN:NOTE'                 => new NoteStructure(I18N::translate('Note')),
                '_LOC:EVEN'                      => new EventDescriptor(I18N::translate('Event'), ['TYPE' => '0:1']),
                '_LOC:EVEN:TYPE'                 => new EventAttributeType(I18N::translate('Type of event')),
                '_LOC:MAP'                       => new EmptyElement(I18N::translate('Coordinates')),
                '_LOC:MAP:LATI'                  => new PlaceLatitude(I18N::translate('Latitude')),
                '_LOC:MAP:LONG'                  => new PlaceLongtitude(I18N::translate('Longitude')),
                '_LOC:NAME'                      => new PlaceName(I18N::translate('Place')),
                '_LOC:NAME:ABBR'                 => new CustomElement(I18N::translate('Abbreviation')),
                '_LOC:NAME:ABBR:TYPE'            => new CustomElement(I18N::translate('Type of abbreviation')),
                '_LOC:NAME:DATE'                 => new DateValue(I18N::translate('Date')),
                '_LOC:NAME:LANG'                 => new LanguageId(I18N::translate('Language')),
                '_LOC:NAME:SOUR'                 => new XrefSource(I18N::translate('Source')),
                '_LOC:NOTE'                      => new NoteStructure(I18N::translate('Note')),
                '_LOC:OBJE'                      => new XrefMedia(I18N::translate('Media')),
                '_LOC:RELI'                      => new ReligiousAffiliation('Religion', []),
                '_LOC:SOUR'                      => new XrefSource(I18N::translate('Source')),
                '_LOC:SOUR:DATA'                 => new SourceData(I18N::translate('Data')),
                '_LOC:SOUR:DATA:DATE'            => new EntryRecordingDate(I18N::translate('Date of entry in original source')),
                '_LOC:SOUR:DATA:TEXT'            => new TextFromSource(I18N::translate('Text')),
                '_LOC:SOUR:EVEN'                 => new EventTypeCitedFrom(I18N::translate('Event')),
                '_LOC:SOUR:EVEN:ROLE'            => new RoleInEvent(I18N::translate('Role')),
                '_LOC:SOUR:NOTE'                 => new NoteStructure(I18N::translate('Note')),
                '_LOC:SOUR:OBJE'                 => new XrefMedia(I18N::translate('Media object')),
                '_LOC:SOUR:PAGE'                 => new WhereWithinSource(I18N::translate('Citation details')),
                '_LOC:SOUR:QUAY'                 => new CertaintyAssessment(I18N::translate('Quality of data')),
                '_LOC:TYPE'                      => new CustomElement(I18N::translate('Type of location')),
                '_LOC:TYPE:DATE'                 => new DateValue(I18N::translate('Date')),
                '_LOC:TYPE:SOUR'                 => new XrefSource(I18N::translate('Source')),
                '_LOC:TYPE:_GOVTYPE'             => new CustomElement('GOV identifier type'),
                '_LOC:_AIDN'                     => new CustomElement('Administrative ID'),
                '_LOC:_AIDN:DATE'                => new DateValue(I18N::translate('Date')),
                '_LOC:_AIDN:SOUR'                => new XrefSource(I18N::translate('Source')),
                '_LOC:_AIDN:TYPE'                => new CustomElement(I18N::translate('Type of administrative ID')),
                '_LOC:_DMGD'                     => new CustomElement('Demographic data'),
                '_LOC:_DMGD:DATE'                => new DateValue(I18N::translate('Date')),
                '_LOC:_DMGD:SOUR'                => new XrefSource(I18N::translate('Source')),
                '_LOC:_DMGD:TYPE'                => new CustomElement(I18N::translate('Type of demographic data')),
                '_LOC:_GOV'                      => new GovIdentifier(I18N::translate('GOV identifier')),
                '_LOC:_LOC'                      => new XrefLocation(I18N::translate('Parent')),
                '_LOC:_LOC:DATE'                 => new DateValue(I18N::translate('Date')),
                '_LOC:_LOC:SOUR'                 => new XrefSource(I18N::translate('Source')),
                '_LOC:_LOC:TYPE'                 => new HierarchicalRelationship(I18N::translate('Hierarchical relationship')),
                '_LOC:_MAIDENHEAD'               => new MaidenheadLocator('Maidenhead locator'),
                '_LOC:_POST'                     => new AddressPostalCode(I18N::translate('Postal code')),
                '_LOC:_POST:DATE'                => new DateValue(I18N::translate('Date')),
                '_LOC:_POST:SOUR'                => new XrefSource(I18N::translate('Source')),
                '_LOC:_UID'                      => new PafUid(I18N::translate('Unique identifier')),
            ]);

            // Legacy extensions
            $this->register([
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
            ]);

            // Personal Ancestral File extensions
            $this->register([
                'INDI:NAME:_ADPN' => new NamePersonal(I18N::translate('Adopted name')),
                'INDI:NAME:_AKA'  => new NamePersonal(I18N::translate('Also known as')),
                'INDI:NAME:_AKAN' => new NamePersonal(I18N::translate('Also known as')),
                'INDI:_EMAIL'      => new AddressEmail(I18N::translate('Email address')),
                'URL'             => new CustomElement(I18N::translate('URL')),
                '_HEB'            => new CustomElement(I18N::translate('Hebrew')),
                '_NAME'           => new CustomElement(I18N::translate('Mailing name')),
                '_SCBK'           => new CustomElement(I18N::translate('Scrapbook')),
                '_SSHOW'          => new CustomElement(I18N::translate('Slide show')),
                '_TYPE'           => new CustomElement(I18N::translate('Media type')),
                '_URL'            => new CustomElement(I18N::translate('URL')),
            ]);

            // PhpGedView extensions
            $this->register([
                'FAM:CHAN:_PGVU'        => new WebtreesUser(I18N::translate('Author of last change')),
                'FAM:COMM'              => new CustomElement(I18N::translate('Comment')),
                'INDI:CHAN:_PGVU'       => new WebtreesUser(I18N::translate('Author of last change')),
                'INDI:COMM'             => new CustomElement(I18N::translate('Comment')),
                'INDI:NAME:_HEB'        => new NamePersonal(I18N::translate('Name in Hebrew')),
                'INDI:EMAIL'            => new AddressEmail(I18N::translate('Email address')),
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
            ]);

            // Reunion extensions
            $this->register([
                'INDI:EMAL'  => new AddressEmail(I18N::translate('Email address')),
                'INDI:CITN'  => new CustomElement(I18N::translate('Citizenship')),
                'INDI:_LEGA' => new CustomElement(I18N::translate('Legatee')),
                'INDI:_MDCL' => new CustomElement(I18N::translate('Medical')),
                'INDI:_PURC' => new CustomElement('Land purchase'),
                'INDI:_SALE' => new CustomElement('Land sale'),
            ]);

            // Roots Magic extensions
            $this->register([
                'INDI:_DNA'  => new CustomElement(I18N::translate('DNA markers')),
                'SOUR:_BIBL' => new CustomElement(I18N::translate('Bibliography')),
                'SOUR:_SUBQ' => new CustomElement(I18N::translate('Abbreviation')),
            ]);

            // webtrees extensions
            $this->register([
                'FAM:*:_ASSO'        => new XrefIndividual(I18N::translate('Associate')),
                'FAM:*:_ASSO:RELA'   => new RelationIsDescriptor(I18N::translate('Relationship')),
                'FAM:CHAN:_WT_USER'  => new WebtreesUser(I18N::translate('Author of last change')),
                'FAM:_UID'           => new PafUid(I18N::translate('Unique identifier')),
                'INDI:*:ASSO'        => new XrefIndividual(I18N::translate('Associate')),
                'INDI:*:ASSO:RELA'   => new RelationIsDescriptor(I18N::translate('Relationship')),
                'INDI:*:PLAC:_HEB'   => new NoteStructure(I18N::translate('Place in Hebrew')),
                'INDI:*:_ASSO'       => new XrefIndividual(I18N::translate('Associate')),
                'INDI:*:_ASSO:RELA'  => new RelationIsDescriptor(I18N::translate('Relationship')),
                'INDI:CHAN:_WT_USER' => new WebtreesUser(I18N::translate('Author of last change')),
                'INDI:_UID'          => new PafUid(I18N::translate('Unique identifier')),
                'INDI:_WT_OBJE_SORT' => new XrefMedia(I18N::translate('Re-order media')),
                'NOTE:CHAN:_WT_USER' => new WebtreesUser(I18N::translate('Author of last change')),
                'NOTE:RESN'          => new RestrictionNotice(I18N::translate('Restriction')),
                'NOTE:_UID'          => new PafUid(I18N::translate('Unique identifier')),
                'OBJE:CHAN:_WT_USER' => new WebtreesUser(I18N::translate('Author of last change')),
                'OBJE:RESN'          => new RestrictionNotice(I18N::translate('Restriction')),
                'OBJE:_UID'          => new PafUid(I18N::translate('Unique identifier')),
                'REPO:CHAN:_WT_USER' => new WebtreesUser(I18N::translate('Author of last change')),
                'REPO:RESN'          => new RestrictionNotice(I18N::translate('Restriction')),
                'REPO:_UID'          => new PafUid(I18N::translate('Unique identifier')),
                'SOUR:CHAN:_WT_USER' => new WebtreesUser(I18N::translate('Author of last change')),
                'SOUR:RESN'          => new RestrictionNotice(I18N::translate('Restriction')),
                'SOUR:_UID'          => new PafUid(I18N::translate('Unique identifier')),
                'SUBM:CHAN:_WT_USER' => new WebtreesUser(I18N::translate('Author of last change')),
                'SUBM:RESN'          => new RestrictionNotice(I18N::translate('Restriction')),
                'SUBM:_UID'          => new PafUid(I18N::translate('Unique identifier')),
                'SUBN:CHAN:_WT_USER' => new WebtreesUser(I18N::translate('Author of last change')),
                'SUBN:RESN'          => new RestrictionNotice(I18N::translate('Restriction')),
                'SUBN:_UID'          => new PafUid(I18N::translate('Unique identifier')),
            ]);
        }

        return $this->elements;
    }

    /**
     * Register more elements.
     *
     * @param array<string,ElementInterface> $elements
     */
    public function register(array $elements): void
    {
        $this->elements = array_merge($this->elements(), $elements);
    }

    /**
     * @param string $tag
     *
     * @return ElementInterface|null
     */
    private function findElementByWildcard(string $tag): ?ElementInterface
    {
        foreach ($this->elements() as $tags => $element) {
            if (strpos($tags, '*') !== false) {
                $regex = '/^' . strtr($tags, ['*' => '[^:]+']) . '$/';

                if (preg_match($regex, $tag)) {
                    return $element;
                }
            }
        }

        return null;
    }
}
