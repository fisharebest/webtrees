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
use Fisharebest\Webtrees\Elements\BarMitzvah;
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
use Fisharebest\Webtrees\Elements\FileName;
use Fisharebest\Webtrees\Elements\FirstCommunion;
use Fisharebest\Webtrees\Elements\Form;
use Fisharebest\Webtrees\Elements\GedcomElement;
use Fisharebest\Webtrees\Elements\GenerationsOfAncestors;
use Fisharebest\Webtrees\Elements\GenerationsOfDescendants;
use Fisharebest\Webtrees\Elements\Graduation;
use Fisharebest\Webtrees\Elements\HeaderRecord;
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
use Fisharebest\Webtrees\Elements\Residence;
use Fisharebest\Webtrees\Elements\ResponsibleAgency;
use Fisharebest\Webtrees\Elements\RestrictionNotice;
use Fisharebest\Webtrees\Elements\Retirement;
use Fisharebest\Webtrees\Elements\RoleInEvent;
use Fisharebest\Webtrees\Elements\RomanizedType;
use Fisharebest\Webtrees\Elements\ScholasticAchievement;
use Fisharebest\Webtrees\Elements\SexValue;
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
use Fisharebest\Webtrees\Elements\WhereWithinSource;
use Fisharebest\Webtrees\Elements\Will;
use Fisharebest\Webtrees\Elements\XrefAssociate;
use Fisharebest\Webtrees\Elements\XrefFamily;
use Fisharebest\Webtrees\Elements\XrefIndividual;
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
    private array $elements = [];

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
            $this->elements = $this->gedcom551();
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

    /**
     * Definitions for GEDCOM 5.5.1.
     *
     * @return array<string,ElementInterface>
     */
    private function gedcom551(): array
    {
        return [
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
            'FAM:*:PLAC:MAP'           => new EmptyElement(I18N::translate('Coordinates'), ['LATI' => '1:1', 'LONG' => '1:1']),
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
            'INDI:*:PLAC:MAP'          => new EmptyElement(I18N::translate('Coordinates'), ['LATI' => '1:1', 'LONG' => '1:1']),
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
            'INDI:ASSO'                => new XrefAssociate(I18N::translate('Associate')),
            'INDI:ASSO:RELA'           => new RelationIsDescriptor(I18N::translate('Relationship')),
            'INDI:BAPL'                => new LdsBaptism(I18N::translate('LDS baptism')),
            'INDI:BAPL:DATE'           => new DateLdsOrd(I18N::translate('Date of LDS baptism')),
            'INDI:BAPL:PLAC'           => new PlaceLivingOrdinance(I18N::translate('Place of LDS baptism')),
            'INDI:BAPL:STAT'           => new LdsBaptismDateStatus(I18N::translate('Status')),
            'INDI:BAPL:STAT:DATE'      => new ChangeDate(I18N::translate('Status change date')),
            'INDI:BAPL:TEMP'           => new TempleCode(I18N::translate('Temple')),
            'INDI:BAPM'                => new Baptism(I18N::translate('Baptism')),
            'INDI:BAPM:DATE'           => new DateValue(I18N::translate('Date of baptism')),
            'INDI:BAPM:PLAC'           => new PlaceName(I18N::translate('Place of baptism')),
            'INDI:BARM'                => new BarMitzvah(I18N::translate('Bar mitzvah')),
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
            'INDI:CONL:PLAC'           => new PlaceLivingOrdinance(I18N::translate('Place of LDS confirmation')),
            'INDI:CONL:STAT'           => new LdsSpouseSealingDateStatus(I18N::translate('Status')),
            'INDI:CONL:STAT:DATE'      => new ChangeDate(I18N::translate('Status change date')),
            'INDI:CONL:TEMP'           => new TempleCode(I18N::translate('Temple')),
            'INDI:CREM'                => new Cremation(I18N::translate('Cremation')),
            'INDI:CREM:DATE'           => new DateValue(I18N::translate('Date of cremation')),
            'INDI:CREM:PLAC'           => new PlaceName(I18N::translate('Place of cremation')),
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
            'INDI:FAMC'                => new XrefFamily(I18N::translate('Family as a child'), ['NOTE' => '0:1', 'PEDI' => '0:1', 'STAT' => '0:1']),
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
            'INDI:NAME:FONE'           => new NamePhoneticVariation(I18N::translate('Phonetic name')),
            'INDI:NAME:FONE:GIVN'      => new NamePieceGiven(I18N::translate('Given names')),
            'INDI:NAME:FONE:NICK'      => new NamePieceNickname(I18N::translate('Nickname')),
            'INDI:NAME:FONE:NPFX'      => new NamePiecePrefix(I18N::translate('Name prefix')),
            'INDI:NAME:FONE:NSFX'      => new NamePieceSuffix(I18N::translate('Name suffix')),
            'INDI:NAME:FONE:SPFX'      => new NamePieceSurnamePrefix(I18N::translate('Surname prefix')),
            'INDI:NAME:FONE:SURN'      => new NamePieceSurname(I18N::translate('Surname')),
            'INDI:NAME:FONE:TYPE'      => new PhoneticType(I18N::translate('Phonetic type')),
            'INDI:NAME:GIVN'           => new NamePieceGiven(I18N::translate('Given names')),
            'INDI:NAME:NICK'           => new NamePieceNickname(I18N::translate('Nickname')),
            'INDI:NAME:NPFX'           => new NamePiecePrefix(I18N::translate('Name prefix')),
            'INDI:NAME:NSFX'           => new NamePieceSuffix(I18N::translate('Name suffix')),
            'INDI:NAME:ROMN'           => new NameRomanizedVariation(I18N::translate('Romanized name')),
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
            'SOUR:DATA:EVEN:PLAC'      => new SourceJurisdictionPlace(I18N::translate('Place'), []),
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
    }
}
