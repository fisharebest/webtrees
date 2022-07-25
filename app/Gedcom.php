<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
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

namespace Fisharebest\Webtrees;

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
use Fisharebest\Webtrees\Elements\Coordinates;
use Fisharebest\Webtrees\Elements\CopyrightFile;
use Fisharebest\Webtrees\Elements\CopyrightSourceData;
use Fisharebest\Webtrees\Elements\CountOfChildren;
use Fisharebest\Webtrees\Elements\CountOfMarriages;
use Fisharebest\Webtrees\Elements\Creation;
use Fisharebest\Webtrees\Elements\Cremation;
use Fisharebest\Webtrees\Elements\CustomElement;
use Fisharebest\Webtrees\Elements\CustomEvent;
use Fisharebest\Webtrees\Elements\CustomFact;
use Fisharebest\Webtrees\Elements\CustomFamilyEvent;
use Fisharebest\Webtrees\Elements\CustomIndividualEvent;
use Fisharebest\Webtrees\Elements\DateLdsOrd;
use Fisharebest\Webtrees\Elements\DateValue;
use Fisharebest\Webtrees\Elements\Death;
use Fisharebest\Webtrees\Elements\DescriptiveTitle;
use Fisharebest\Webtrees\Elements\Divorce;
use Fisharebest\Webtrees\Elements\DivorceFiled;
use Fisharebest\Webtrees\Elements\Emigration;
use Fisharebest\Webtrees\Elements\EmptyElement;
use Fisharebest\Webtrees\Elements\Engagement;
use Fisharebest\Webtrees\Elements\EventAttributeType;
use Fisharebest\Webtrees\Elements\EventOrFactClassification;
use Fisharebest\Webtrees\Elements\EventsRecorded;
use Fisharebest\Webtrees\Elements\EventTypeCitedFrom;
use Fisharebest\Webtrees\Elements\ExternalIdentifier;
use Fisharebest\Webtrees\Elements\ExternalIdentifierType;
use Fisharebest\Webtrees\Elements\FamilyCensus;
use Fisharebest\Webtrees\Elements\FamilyEvent;
use Fisharebest\Webtrees\Elements\FamilyFact;
use Fisharebest\Webtrees\Elements\FamilyRecord;
use Fisharebest\Webtrees\Elements\FamilyResidence;
use Fisharebest\Webtrees\Elements\FamilySearchFamilyTreeId;
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
use Fisharebest\Webtrees\Elements\IndividualEvent;
use Fisharebest\Webtrees\Elements\IndividualFact;
use Fisharebest\Webtrees\Elements\IndividualRecord;
use Fisharebest\Webtrees\Elements\LanguageId;
use Fisharebest\Webtrees\Elements\LdsBaptism;
use Fisharebest\Webtrees\Elements\LdsBaptismDateStatus;
use Fisharebest\Webtrees\Elements\LdsChildSealing;
use Fisharebest\Webtrees\Elements\LdsChildSealingDateStatus;
use Fisharebest\Webtrees\Elements\LdsConfirmation;
use Fisharebest\Webtrees\Elements\LdsEndowment;
use Fisharebest\Webtrees\Elements\LdsEndowmentDateStatus;
use Fisharebest\Webtrees\Elements\LdsInitiatory;
use Fisharebest\Webtrees\Elements\LdsOrdinanceStatus;
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
use Fisharebest\Webtrees\Elements\NationalOrTribalOrigin;
use Fisharebest\Webtrees\Elements\Naturalization;
use Fisharebest\Webtrees\Elements\NobilityTypeTitle;
use Fisharebest\Webtrees\Elements\NonEvent;
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
use Fisharebest\Webtrees\Elements\Uid;
use Fisharebest\Webtrees\Elements\UserReferenceNumber;
use Fisharebest\Webtrees\Elements\UserReferenceType;
use Fisharebest\Webtrees\Elements\VersionNumber;
use Fisharebest\Webtrees\Elements\WebtreesUser;
use Fisharebest\Webtrees\Elements\WhereWithinSource;
use Fisharebest\Webtrees\Elements\Will;
use Fisharebest\Webtrees\Elements\XrefAssociate;
use Fisharebest\Webtrees\Elements\XrefFamily;
use Fisharebest\Webtrees\Elements\XrefIndividual;
use Fisharebest\Webtrees\Elements\XrefLocation;
use Fisharebest\Webtrees\Elements\XrefMedia;
use Fisharebest\Webtrees\Elements\XrefRepository;
use Fisharebest\Webtrees\Elements\XrefSharedNote;
use Fisharebest\Webtrees\Elements\XrefSource;
use Fisharebest\Webtrees\Elements\XrefSubmission;
use Fisharebest\Webtrees\Elements\XrefSubmitter;

/**
 * GEDCOM 5.5.1 specification
 */
class Gedcom
{
    // 255 less the EOL character.
    public const LINE_LENGTH = 253;

    // Gedcom tags which indicate the start of life.
    public const BIRTH_EVENTS = ['BIRT', 'CHR', 'BAPM'];

    // Gedcom tags which indicate the end of life.
    public const DEATH_EVENTS = ['DEAT', 'BURI', 'CREM'];

    // Gedcom tags which indicate the start of a relationship.
    public const MARRIAGE_EVENTS = ['MARR', '_NMR'];

    // Gedcom tags which indicate the end of a relationship.
    public const DIVORCE_EVENTS = ['DIV', 'ANUL', '_SEPR'];

    // Regular expression to match a GEDCOM tag.
    public const REGEX_TAG = '[_A-Z][_A-Z0-9]*';

    // Regular expression to match a GEDCOM XREF.
    public const REGEX_XREF = '[A-Za-z0-9:_.-]{1,20}';

    // Regular expression to match a GEDCOM fact/event for editing raw GEDCOM.
    private const REGEX_VALUE   = '( .+)?';
    private const REGEX_LEVEL_9 = '\n9 ' . self::REGEX_TAG . self::REGEX_VALUE;
    private const REGEX_LEVEL_8 = '\n8 ' . self::REGEX_TAG . self::REGEX_VALUE . '(' . self::REGEX_LEVEL_9 . ')*';
    private const REGEX_LEVEL_7 = '\n7 ' . self::REGEX_TAG . self::REGEX_VALUE . '(' . self::REGEX_LEVEL_8 . ')*';
    private const REGEX_LEVEL_6 = '\n6 ' . self::REGEX_TAG . self::REGEX_VALUE . '(' . self::REGEX_LEVEL_7 . ')*';
    private const REGEX_LEVEL_5 = '\n5 ' . self::REGEX_TAG . self::REGEX_VALUE . '(' . self::REGEX_LEVEL_6 . ')*';
    private const REGEX_LEVEL_4 = '\n4 ' . self::REGEX_TAG . self::REGEX_VALUE . '(' . self::REGEX_LEVEL_5 . ')*';
    private const REGEX_LEVEL_3 = '\n3 ' . self::REGEX_TAG . self::REGEX_VALUE . '(' . self::REGEX_LEVEL_4 . ')*';
    private const REGEX_LEVEL_2 = '\n2 ' . self::REGEX_TAG . self::REGEX_VALUE . '(' . self::REGEX_LEVEL_3 . ')*';
    public const REGEX_FACT     = '1 ' . self::REGEX_TAG . self::REGEX_VALUE . '(' . self::REGEX_LEVEL_2 . ')*\n?';

    // Separates the parts of a place name.
    public const PLACE_SEPARATOR = ', ';

    // Regex to match a (badly formed) GEDCOM place separator.
    public const PLACE_SEPARATOR_REGEX = '/ *,[, ]*/';

    // LATI and LONG tags
    public const LATITUDE_NORTH = 'N';
    public const LATITUDE_SOUTH = 'S';
    public const LONGITUDE_EAST = 'E';
    public const LONGITUDE_WEST = 'W';

    // Not all record types allow a CHAN event.
    public const RECORDS_WITH_CHAN = [
        Family::RECORD_TYPE,
        Individual::RECORD_TYPE,
        Media::RECORD_TYPE,
        Note::RECORD_TYPE,
        Repository::RECORD_TYPE,
        Source::RECORD_TYPE,
        Submitter::RECORD_TYPE,
    ];

    // These preferences control multiple tag definitions
    public const HIDDEN_TAGS = [
        // Individual names
        'NAME_NPFX'  => ['INDI:NAME:NPFX', 'INDI:NAME:FONE:NPFX', 'INDI:NAME:ROMN:NPFX'],
        'NAME_SPFX'  => ['INDI:NAME:SPFX', 'INDI:NAME:FONE:SPFX', 'INDI:NAME:ROMN:SPFX'],
        'NAME_NSFX'  => ['INDI:NAME:NSFX', 'INDI:NAME:FONE:NSFX', 'INDI:NAME:ROMN:NSFX'],
        'NAME_NICK'  => ['INDI:NAME:NICK', 'INDI:NAME:FONE:NICK', 'INDI:NAME:ROMN:NICK'],
        'NAME_FONE'  => ['INDI:NAME:FONE'],
        'NAME_ROMN'  => ['INDI:NAME:ROMN'],
        'NAME_NOTE'  => ['INDI:NAME:NOTE', 'INDI:NAME:FONE:NOTE', 'INDI:NAME:ROMN:NOTE'],
        'NAME_SOUR'  => ['INDI:NAME:SOUR', 'INDI:NAME:FONE:SOUR', 'INDI:NAME:ROMN:SOUR'],
        // Places
        'PLAC_MAP'   => [':PLAC:MAP'],
        'PLAC_FONE'  => [':PLAC:FONE'],
        'PLAC_ROMN'  => [':PLAC:ROMN'],
        'PLAC_FORM'  => [':PLAC:FORM', 'HEAD:PLAC'],
        'PLAC_NOTE'  => [':PLAC:NOTE'],
        // Addresses
        'ADDR_EMAIL' => [':EMAIL'],
        'ADDR_PHON'  => [':PHON'],
        'ADDR_WWW'   => [':WWW'],
        // Source citations
        'SOUR_EVEN'  => [':SOUR:EVEN'],
        'SOUR_DATE'  => [':SOUR:DATA:DATE'],
        'SOUR_NOTE'  => [':SOUR:NOTE'],
        'SOUR_QUAY'  => [':SOUR:QUAY'],
        // Sources
        'SOUR_DATA'  => ['SOUR:DATA:EVEN', 'SOUR:DATA:AGNC', 'SOUR:DATA:NOTE'],
        // Individuals
        'BIRT_FAMC'  => ['INDI:BIRT:FAMC'],
        'RELI'       => ['INDI:RELI'],
        'BAPM'       => ['INDI:BAPM'],
        'CHR'        => ['INDI:CHR', 'INDI:CHRA'],
        'FCOM'       => ['INDI:FCOM', 'INDI:CONF'],
        'ORDN'       => ['INDI:ORDN'],
        'BARM'       => ['INDI:BARM', 'INDI:BASM'],
        'ALIA'       => ['INDI:ALIA'],
        'ASSO'       => ['INDI:ASSO'],
        // Families
        'ENGA'       => ['FAM:ENGA'],
        'MARB'       => ['FAM:MARB'],
        'MARC'       => ['FAM:MARC'],
        'MARL'       => ['FAM:MARL'],
        'MARS'       => ['FAM:MARS'],
        'ANUL'       => ['FAM:ANUL'],
        'DIVF'       => ['FAM:DIVF'],
        'FAM_RESI'   => ['FAM:RESI'],
        'FAM_CENS'   => ['FAM:CENS'],
        // LDS church
        'LDS'        => ['INDI:BAPL', 'INDI:CONL', 'INDI:ENDL', 'INDI:SLGC', 'FAM:SLGS', 'HEAD:SUBN'],
        // Identifiers
        'AFN'        => ['INDI:AFN'],
        'IDNO'       => ['INDI:IDNO'],
        'SSN'        => ['INDI:SSN'],
        'RFN'        => [':RFN'],
        'REFN'       => [':REFN'],
        'RIN'        => [':RIN'],
        // Submitters
        'SUBM'       => ['INDI:SUBM', 'FAM:SUBM'],
        'ANCI'       => ['INDI:ANCI', 'INDI:DESI'],
    ];

    // Custom GEDCOM tags that can be created in webtrees.
    public const CUSTOM_FAMILY_TAGS = [
        'FACT',
        '_COML',
        '_MARI',
        '_MBON',
        '_NMR',
        '_SEPR',
    ];

    public const CUSTOM_INDIVIDUAL_TAGS = [
        '_BRTM',
        '_CIRC',
        '_DEG',
        '_DNA',
        '_EXCM',
        '_EYEC',
        '_FNRL',
        '_FSFTID',
        '_HAIR',
        '_HEIG',
        '_INTE',
        '_MDCL',
        '_MEDC',
        '_MILI',
        '_MILT',
        '_NAMS',
        '_NMAR',
        '_PRMN',
        '_WEIG',
        '_YART',
    ];

    // Some applications create GEDCOM files containing records without XREFS.
    // We cannot process these.
    public const CUSTOM_RECORDS_WITHOUT_XREFS = [
        'EMOTIONALRELATIONSHIP', // GenoPro
        'GENOMAP', // GenoPro
        'GLOBAL', // GenoPro
        'LABEL', // GenoPro
        'PEDIGREELINK', // GenoPro
        'SOCIALRELATIONSHIP', // GenoPro
        '_EVDEF', // RootsMagic
        '_EVENT_DEFN', // PAF and Legacy
        '_HASHTAG_DEFN', // Legacy
        '_PUBLISH', // MyHeritage
        '_TASK', // Ages
        '_TODO', // Legacy
    ];

    /**
     * Definitions for GEDCOM 5.5.1.
     *
     * @return array<string,ElementInterface>
     */
    private function gedcom551Tags(): array
    {
        return [
            'FAM'                        => new FamilyRecord(I18N::translate('Family')),
            'FAM:*:ADDR'                 => new AddressLine(I18N::translate('Address')),
            'FAM:*:ADDR:ADR1'            => new AddressLine1(I18N::translate('Address line 1')),
            'FAM:*:ADDR:ADR2'            => new AddressLine2(I18N::translate('Address line 2')),
            'FAM:*:ADDR:ADR3'            => new AddressLine3(I18N::translate('Address line 3')),
            'FAM:*:ADDR:CITY'            => new AddressCity(I18N::translate('City')),
            'FAM:*:ADDR:CTRY'            => new AddressCountry(I18N::translate('Country')),
            'FAM:*:ADDR:POST'            => new AddressPostalCode(I18N::translate('Postal code')),
            'FAM:*:ADDR:STAE'            => new AddressState(I18N::translate('State')),
            'FAM:*:AGNC'                 => new ResponsibleAgency(I18N::translate('Agency')),
            'FAM:*:CAUS'                 => new CauseOfEvent(I18N::translate('Cause')),
            'FAM:*:DATE'                 => new DateValue(I18N::translate('Date')),
            'FAM:*:EMAIL'                => new AddressEmail(I18N::translate('Email address')),
            'FAM:*:FAX'                  => new AddressFax(I18N::translate('Fax')),
            'FAM:*:HUSB'                 => new EmptyElement(I18N::translate('Husband'), ['AGE' => '0:1']),
            'FAM:*:HUSB:AGE'             => new AgeAtEvent(I18N::translate('Husband’s age')),
            'FAM:*:NOTE'                 => new NoteStructure(I18N::translate('Note')),
            'FAM:*:OBJE'                 => new XrefMedia(I18N::translate('Media object')),
            'FAM:*:PHON'                 => new PhoneNumber(I18N::translate('Phone')),
            'FAM:*:PLAC'                 => new PlaceName(I18N::translate('Place')),
            'FAM:*:PLAC:FONE'            => new PlacePhoneticVariation(I18N::translate('Phonetic place')),
            'FAM:*:PLAC:FONE:TYPE'       => new PhoneticType(I18N::translate('Type')),
            'FAM:*:PLAC:FORM'            => new PlaceHierarchy(I18N::translate('Format')),
            'FAM:*:PLAC:MAP'             => new Coordinates(I18N::translate('Coordinates')),
            'FAM:*:PLAC:MAP:LATI'        => new PlaceLatitude(I18N::translate('Latitude')),
            'FAM:*:PLAC:MAP:LONG'        => new PlaceLongtitude(I18N::translate('Longitude')),
            'FAM:*:PLAC:NOTE'            => new NoteStructure(I18N::translate('Note')),
            'FAM:*:PLAC:ROMN'            => new PlaceRomanizedVariation(I18N::translate('Romanized place')),
            'FAM:*:PLAC:ROMN:TYPE'       => new RomanizedType(I18N::translate('Type')),
            'FAM:*:RELI'                 => new ReligiousAffiliation(I18N::translate('Religion'), []),
            'FAM:*:RESN'                 => new RestrictionNotice(I18N::translate('Restriction')),
            'FAM:*:SOUR'                 => new XrefSource(I18N::translate('Source citation')),
            'FAM:*:SOUR:DATA'            => new SourceData(I18N::translate('Data')),
            'FAM:*:SOUR:DATA:DATE'       => new DateValue(I18N::translate('Date of entry in original source')),
            'FAM:*:SOUR:DATA:TEXT'       => new TextFromSource(I18N::translate('Text')),
            'FAM:*:SOUR:EVEN'            => new EventTypeCitedFrom(I18N::translate('Event')),
            'FAM:*:SOUR:EVEN:ROLE'       => new RoleInEvent(I18N::translate('Role')),
            'FAM:*:SOUR:NOTE'            => new NoteStructure(I18N::translate('Note')),
            'FAM:*:SOUR:OBJE'            => new XrefMedia(I18N::translate('Media object')),
            'FAM:*:SOUR:PAGE'            => new WhereWithinSource(I18N::translate('Citation details')),
            'FAM:*:SOUR:QUAY'            => new CertaintyAssessment(I18N::translate('Quality of data')),
            'FAM:*:TYPE'                 => new EventOrFactClassification(I18N::translate('Type')),
            'FAM:*:WIFE'                 => new EmptyElement(I18N::translate('Wife'), ['AGE' => '0:1']),
            'FAM:*:WIFE:AGE'             => new AgeAtEvent(I18N::translate('Wife’s age')),
            'FAM:*:WWW'                  => new AddressWebPage(I18N::translate('URL')),
            'FAM:ANUL'                   => new Annulment(I18N::translate('Annulment')),
            'FAM:CENS'                   => new FamilyCensus(I18N::translate('Family census')),
            'FAM:CHAN'                   => new Change(I18N::translate('Last change')),
            'FAM:CHAN:DATE'              => new ChangeDate(I18N::translate('Date of last change')),
            'FAM:CHAN:DATE:TIME'         => new TimeValue(I18N::translate('Time of last change')),
            'FAM:CHIL'                   => new XrefIndividual(I18N::translate('Child')),
            'FAM:DIV'                    => new Divorce(I18N::translate('Divorce')),
            'FAM:DIV:DATE'               => new DateValue(I18N::translate('Date of divorce')),
            'FAM:DIVF'                   => new DivorceFiled(I18N::translate('Divorce filed')),
            'FAM:ENGA'                   => new Engagement(I18N::translate('Engagement')),
            'FAM:ENGA:DATE'              => new DateValue(I18N::translate('Date of engagement')),
            'FAM:ENGA:PLAC'              => new PlaceName(I18N::translate('Place of engagement')),
            'FAM:EVEN'                   => new FamilyEvent(I18N::translate('Event')),
            'FAM:EVEN:TYPE'              => new EventOrFactClassification(I18N::translate('Type of event')),
            'FAM:HUSB'                   => new XrefIndividual(I18N::translate('Husband')),
            'FAM:MARB'                   => new MarriageBanns(I18N::translate('Marriage banns')),
            'FAM:MARB:DATE'              => new DateValue(I18N::translate('Date of marriage banns')),
            'FAM:MARB:PLAC'              => new PlaceName(I18N::translate('Place of marriage banns')),
            'FAM:MARC'                   => new MarriageContract(I18N::translate('Marriage contract')),
            'FAM:MARL'                   => new MarriageLicence(I18N::translate('Marriage license')),
            'FAM:MARR'                   => new Marriage(I18N::translate('Marriage')),
            'FAM:MARR:DATE'              => new DateValue(I18N::translate('Date of marriage')),
            'FAM:MARR:PLAC'              => new PlaceName(I18N::translate('Place of marriage')),
            'FAM:MARR:TYPE'              => new MarriageType(I18N::translate('Type of marriage')),
            'FAM:MARS'                   => new MarriageSettlement(I18N::translate('Marriage settlement')),
            'FAM:NCHI'                   => new CountOfChildren(I18N::translate('Number of children')),
            'FAM:NOTE'                   => new NoteStructure(I18N::translate('Note')),
            'FAM:OBJE'                   => new XrefMedia(I18N::translate('Media object')),
            'FAM:REFN'                   => new UserReferenceNumber(I18N::translate('Reference number')),
            'FAM:REFN:TYPE'              => new UserReferenceType(I18N::translate('Type of reference number')),
            'FAM:RESI'                   => new FamilyResidence(I18N::translate('Family residence')),
            'FAM:RESN'                   => new RestrictionNotice(I18N::translate('Restriction')),
            'FAM:RIN'                    => new AutomatedRecordId(I18N::translate('Record ID number')),
            'FAM:SLGS'                   => new LdsSpouseSealing(I18N::translate('LDS spouse sealing')),
            'FAM:SLGS:DATE'              => new DateLdsOrd(I18N::translate('Date of LDS spouse sealing')),
            'FAM:SLGS:PLAC'              => new PlaceLivingOrdinance(I18N::translate('Place of LDS spouse sealing')),
            'FAM:SLGS:STAT'              => new LdsSpouseSealingDateStatus(I18N::translate('Status')),
            'FAM:SLGS:STAT:DATE'         => new ChangeDate(I18N::translate('Status change date')),
            'FAM:SLGS:TEMP'              => new TempleCode(I18N::translate('Temple')),
            'FAM:SOUR'                   => new XrefSource(I18N::translate('Source citation')),
            'FAM:SOUR:DATA'              => new SourceData(I18N::translate('Data')),
            'FAM:SOUR:DATA:DATE'         => new DateValue(I18N::translate('Date of entry in original source')),
            'FAM:SOUR:DATA:TEXT'         => new TextFromSource(I18N::translate('Text')),
            'FAM:SOUR:EVEN'              => new EventTypeCitedFrom(I18N::translate('Event')),
            'FAM:SOUR:EVEN:ROLE'         => new RoleInEvent(I18N::translate('Role')),
            'FAM:SOUR:NOTE'              => new NoteStructure(I18N::translate('Note')),
            'FAM:SOUR:OBJE'              => new XrefMedia(I18N::translate('Media object')),
            'FAM:SOUR:PAGE'              => new WhereWithinSource(I18N::translate('Citation details')),
            'FAM:SOUR:QUAY'              => new CertaintyAssessment(I18N::translate('Quality of data')),
            'FAM:SUBM'                   => new XrefSubmitter(I18N::translate('Submitter')),
            'FAM:WIFE'                   => new XrefIndividual(I18N::translate('Wife')),
            'HEAD'                       => new HeaderRecord(I18N::translate('Header')),
            'HEAD:CHAR'                  => new CharacterSet(I18N::translate('Character set')),
            'HEAD:CHAR:VERS'             => new VersionNumber(I18N::translate('Version')),
            'HEAD:COPR'                  => new CopyrightFile(I18N::translate('Copyright')),
            'HEAD:DATE'                  => new TransmissionDate(I18N::translate('Date')),
            'HEAD:DATE:TIME'             => new TimeValue(I18N::translate('Time')),
            'HEAD:DEST'                  => new ReceivingSystemName(I18N::translate('Destination')),
            'HEAD:FILE'                  => new FileName(I18N::translate('Filename')),
            'HEAD:GEDC'                  => new GedcomElement(I18N::translate('GEDCOM')),
            'HEAD:GEDC:FORM'             => new Form(I18N::translate('Format')),
            'HEAD:GEDC:VERS'             => new VersionNumber(I18N::translate('Version')),
            'HEAD:LANG'                  => new LanguageId(I18N::translate('Language')),
            'HEAD:NOTE'                  => new ContentDescription(I18N::translate('Note')),
            'HEAD:PLAC'                  => new EmptyElement(I18N::translate('Place hierarchy'), ['FORM' => '1:1']),
            'HEAD:PLAC:FORM'             => new PlaceHierarchy(I18N::translate('Format')),
            'HEAD:SOUR'                  => new ApprovedSystemId(I18N::translate('Application ID')),
            'HEAD:SOUR:CORP'             => new NameOfBusiness(I18N::translate('Corporation')),
            'HEAD:SOUR:CORP:ADDR'        => new AddressLine(I18N::translate('Address')),
            'HEAD:SOUR:CORP:ADDR:ADR1'   => new AddressLine1(I18N::translate('Address line 1')),
            'HEAD:SOUR:CORP:ADDR:ADR2'   => new AddressLine2(I18N::translate('Address line 2')),
            'HEAD:SOUR:CORP:ADDR:ADR3'   => new AddressLine3(I18N::translate('Address line 3')),
            'HEAD:SOUR:CORP:ADDR:CITY'   => new AddressCity(I18N::translate('City')),
            'HEAD:SOUR:CORP:ADDR:CTRY'   => new AddressCountry(I18N::translate('Country')),
            'HEAD:SOUR:CORP:ADDR:POST'   => new AddressPostalCode(I18N::translate('Postal code')),
            'HEAD:SOUR:CORP:ADDR:STAE'   => new AddressState(I18N::translate('State')),
            'HEAD:SOUR:CORP:EMAIL'       => new AddressEmail(I18N::translate('Email address')),
            'HEAD:SOUR:CORP:FAX'         => new AddressFax(I18N::translate('Fax')),
            'HEAD:SOUR:CORP:PHON'        => new PhoneNumber(I18N::translate('Phone')),
            'HEAD:SOUR:CORP:WWW'         => new AddressWebPage(I18N::translate('URL')),
            'HEAD:SOUR:DATA'             => new NameOfSourceData(I18N::translate('Data')),
            'HEAD:SOUR:DATA:COPR'        => new CopyrightSourceData(I18N::translate('Copyright')),
            'HEAD:SOUR:DATA:DATE'        => new PublicationDate(I18N::translate('Date')),
            'HEAD:SOUR:NAME'             => new NameOfProduct(I18N::translate('Application name')),
            'HEAD:SOUR:VERS'             => new VersionNumber(I18N::translate('Version')),
            'HEAD:SUBM'                  => new XrefSubmitter(I18N::translate('Submitter')),
            'HEAD:SUBN'                  => new XrefSubmission(I18N::translate('Submission')),
            'INDI'                       => new IndividualRecord(I18N::translate('Individual')),
            'INDI:*:ADDR'                => new AddressLine(I18N::translate('Address')),
            'INDI:*:ADDR:ADR1'           => new AddressLine1(I18N::translate('Address line 1')),
            'INDI:*:ADDR:ADR2'           => new AddressLine2(I18N::translate('Address line 2')),
            'INDI:*:ADDR:ADR3'           => new AddressLine3(I18N::translate('Address line 3')),
            'INDI:*:ADDR:CITY'           => new AddressCity(I18N::translate('City')),
            'INDI:*:ADDR:CTRY'           => new AddressCountry(I18N::translate('Country')),
            'INDI:*:ADDR:POST'           => new AddressPostalCode(I18N::translate('Postal code')),
            'INDI:*:ADDR:STAE'           => new AddressState(I18N::translate('State')),
            'INDI:*:AGE'                 => new AgeAtEvent(I18N::translate('Age')),
            'INDI:*:AGNC'                => new ResponsibleAgency(I18N::translate('Agency')),
            'INDI:*:CAUS'                => new CauseOfEvent(I18N::translate('Cause')),
            'INDI:*:DATE'                => new DateValue(I18N::translate('Date')),
            'INDI:*:EMAIL'               => new AddressEmail(I18N::translate('Email address')),
            'INDI:*:FAX'                 => new AddressFax(I18N::translate('Fax')),
            'INDI:*:NOTE'                => new NoteStructure(I18N::translate('Note')),
            'INDI:*:OBJE'                => new XrefMedia(I18N::translate('Media object')),
            'INDI:*:PHON'                => new PhoneNumber(I18N::translate('Phone')),
            'INDI:*:PLAC'                => new PlaceName(I18N::translate('Place')),
            'INDI:*:PLAC:FONE'           => new PlacePhoneticVariation(I18N::translate('Phonetic place')),
            'INDI:*:PLAC:FONE:TYPE'      => new PhoneticType(I18N::translate('Type')),
            'INDI:*:PLAC:FORM'           => new PlaceHierarchy(I18N::translate('Format')),
            'INDI:*:PLAC:MAP'            => new Coordinates(I18N::translate('Coordinates')),
            'INDI:*:PLAC:MAP:LATI'       => new PlaceLatitude(I18N::translate('Latitude')),
            'INDI:*:PLAC:MAP:LONG'       => new PlaceLongtitude(I18N::translate('Longitude')),
            'INDI:*:PLAC:NOTE'           => new NoteStructure(I18N::translate('Note')),
            'INDI:*:PLAC:ROMN'           => new PlaceRomanizedVariation(I18N::translate('Romanized place')),
            'INDI:*:PLAC:ROMN:TYPE'      => new RomanizedType(I18N::translate('Type')),
            'INDI:*:RELI'                => new ReligiousAffiliation(I18N::translate('Religion'), []),
            'INDI:*:RESN'                => new RestrictionNotice(I18N::translate('Restriction')),
            'INDI:*:SOUR'                => new XrefSource(I18N::translate('Source citation')),
            'INDI:*:SOUR:DATA'           => new SourceData(I18N::translate('Data')),
            'INDI:*:SOUR:DATA:DATE'      => new DateValue(I18N::translate('Date of entry in original source')),
            'INDI:*:SOUR:DATA:TEXT'      => new TextFromSource(I18N::translate('Text')),
            'INDI:*:SOUR:EVEN'           => new EventTypeCitedFrom(I18N::translate('Event')),
            'INDI:*:SOUR:EVEN:ROLE'      => new RoleInEvent(I18N::translate('Role')),
            'INDI:*:SOUR:NOTE'           => new NoteStructure(I18N::translate('Note')),
            'INDI:*:SOUR:OBJE'           => new XrefMedia(I18N::translate('Media object')),
            'INDI:*:SOUR:PAGE'           => new WhereWithinSource(I18N::translate('Citation details')),
            'INDI:*:SOUR:QUAY'           => new CertaintyAssessment(I18N::translate('Quality of data')),
            'INDI:*:TYPE'                => new EventOrFactClassification(I18N::translate('Type')),
            'INDI:*:WWW'                 => new AddressWebPage(I18N::translate('URL')),
            'INDI:ADOP'                  => new Adoption(I18N::translate('Adoption')),
            'INDI:ADOP:DATE'             => new DateValue(I18N::translate('Date of adoption')),
            'INDI:ADOP:FAMC'             => new XrefFamily(I18N::translate('Adoptive parents')),
            'INDI:ADOP:FAMC:ADOP'        => new AdoptedByWhichParent(I18N::translate('Adoption')),
            'INDI:ADOP:PLAC'             => new PlaceName(I18N::translate('Place of adoption')),
            'INDI:AFN'                   => new AncestralFileNumber(I18N::translate('Ancestral file number')),
            'INDI:ALIA'                  => new XrefIndividual(I18N::translate('Alias')),
            'INDI:ANCI'                  => new XrefSubmitter(I18N::translate('Ancestors interest')),
            'INDI:ASSO'                  => new XrefAssociate(I18N::translate('Associate')),
            'INDI:ASSO:RELA'             => new RelationIsDescriptor(I18N::translate('Relationship')),
            'INDI:BAPL'                  => new LdsBaptism(I18N::translate('LDS baptism')),
            'INDI:BAPL:DATE'             => new DateLdsOrd(I18N::translate('Date of LDS baptism')),
            'INDI:BAPL:PLAC'             => new PlaceLivingOrdinance(I18N::translate('Place of LDS baptism')),
            'INDI:BAPL:STAT'             => new LdsBaptismDateStatus(I18N::translate('Status')),
            'INDI:BAPL:STAT:DATE'        => new ChangeDate(I18N::translate('Status change date')),
            'INDI:BAPL:TEMP'             => new TempleCode(I18N::translate('Temple')),
            'INDI:BAPM'                  => new Baptism(I18N::translate('Baptism')),
            'INDI:BAPM:DATE'             => new DateValue(I18N::translate('Date of baptism')),
            'INDI:BAPM:PLAC'             => new PlaceName(I18N::translate('Place of baptism')),
            'INDI:BARM'                  => new BarMitzvah(I18N::translate('Bar mitzvah')),
            'INDI:BARM:DATE'             => new DateValue(I18N::translate('Date of bar mitzvah')),
            'INDI:BARM:PLAC'             => new PlaceName(I18N::translate('Place of bar mitzvah')),
            'INDI:BASM'                  => new BasMitzvah(I18N::translate('Bat mitzvah')),
            'INDI:BASM:DATE'             => new BasMitzvah(I18N::translate('Date of bat mitzvah')),
            'INDI:BASM:PLAC'             => new DateValue(I18N::translate('Place of bat mitzvah')),
            'INDI:BIRT'                  => new Birth(I18N::translate('Birth')),
            'INDI:BIRT:DATE'             => new DateValue(I18N::translate('Date of birth')),
            'INDI:BIRT:FAMC'             => new XrefFamily(I18N::translate('Birth parents')),
            'INDI:BIRT:PLAC'             => new PlaceName(I18N::translate('Place of birth')),
            'INDI:BLES'                  => new Blessing(I18N::translate('Blessing')),
            'INDI:BLES:DATE'             => new DateValue(I18N::translate('Date of blessing')),
            'INDI:BLES:PLAC'             => new PlaceName(I18N::translate('Place of blessing')),
            'INDI:BURI'                  => new Burial(I18N::translate('Burial')),
            'INDI:BURI:DATE'             => new DateValue(I18N::translate('Date of burial')),
            'INDI:BURI:PLAC'             => new PlaceName(I18N::translate('Place of burial')),
            'INDI:CAST'                  => new CasteName(I18N::translate('Caste')),
            'INDI:CENS'                  => new Census(I18N::translate('Census')),
            'INDI:CENS:DATE'             => new DateValue(I18N::translate('Census date')),
            'INDI:CENS:PLAC'             => new PlaceName(I18N::translate('Census place')),
            'INDI:CHAN'                  => new Change(I18N::translate('Last change')),
            'INDI:CHAN:DATE'             => new ChangeDate(I18N::translate('Date of last change')),
            'INDI:CHAN:DATE:TIME'        => new TimeValue(I18N::translate('Time of last change')),
            'INDI:CHR'                   => new Christening(I18N::translate('Christening')),
            'INDI:CHR:DATE'              => new DateValue(I18N::translate('Date of christening')),
            'INDI:CHR:FAMC'              => new XrefFamily(I18N::translate('Godparents')),
            'INDI:CHR:PLAC'              => new PlaceName(I18N::translate('Place of christening')),
            'INDI:CHRA'                  => new AdultChristening(I18N::translate('Adult christening')),
            'INDI:CHRA:PLAC'             => new PlaceName(I18N::translate('Place of christening')),
            'INDI:CONF'                  => new Confirmation(I18N::translate('Confirmation')),
            'INDI:CONF:DATE'             => new DateValue(I18N::translate('Date of confirmation')),
            'INDI:CONF:PLAC'             => new PlaceName(I18N::translate('Place of confirmation')),
            'INDI:CONL'                  => new LdsConfirmation(I18N::translate('LDS confirmation')),
            'INDI:CONL:DATE'             => new DateLdsOrd(I18N::translate('Date of LDS confirmation')),
            'INDI:CONL:PLAC'             => new PlaceLivingOrdinance(I18N::translate('Place of LDS confirmation')),
            'INDI:CONL:STAT'             => new LdsBaptismDateStatus(I18N::translate('Status')),
            'INDI:CONL:STAT:DATE'        => new ChangeDate(I18N::translate('Status change date')),
            'INDI:CONL:TEMP'             => new TempleCode(I18N::translate('Temple')),
            'INDI:CREM'                  => new Cremation(I18N::translate('Cremation')),
            'INDI:CREM:DATE'             => new DateValue(I18N::translate('Date of cremation')),
            'INDI:CREM:PLAC'             => new PlaceName(I18N::translate('Place of cremation')),
            'INDI:DEAT'                  => new Death(I18N::translate('Death')),
            'INDI:DEAT:CAUS'             => new CauseOfEvent(I18N::translate('Cause of death')),
            'INDI:DEAT:DATE'             => new DateValue(I18N::translate('Date of death')),
            'INDI:DEAT:PLAC'             => new PlaceName(I18N::translate('Place of death')),
            'INDI:DESI'                  => new XrefSubmitter(I18N::translate('Descendants interest')),
            'INDI:DSCR'                  => new PhysicalDescription(I18N::translate('Description')),
            'INDI:EDUC'                  => new ScholasticAchievement(I18N::translate('Education')),
            'INDI:EDUC:AGNC'             => new ResponsibleAgency(I18N::translate('School or college')),
            'INDI:EMIG'                  => new Emigration(I18N::translate('Emigration')),
            'INDI:EMIG:DATE'             => new DateValue(I18N::translate('Date of emigration')),
            'INDI:EMIG:PLAC'             => new PlaceName(I18N::translate('Place of emigration')),
            'INDI:ENDL'                  => new LdsEndowment(I18N::translate('LDS endowment')),
            'INDI:ENDL:DATE'             => new DateLdsOrd(I18N::translate('Date of LDS endowment')),
            'INDI:ENDL:PLAC'             => new PlaceLivingOrdinance(I18N::translate('Place of LDS endowment')),
            'INDI:ENDL:STAT'             => new LdsEndowmentDateStatus(I18N::translate('Status')),
            'INDI:ENDL:STAT:DATE'        => new ChangeDate(I18N::translate('Status change date')),
            'INDI:ENDL:TEMP'             => new TempleCode(I18N::translate('Temple')),
            'INDI:EVEN'                  => new IndividualEvent(I18N::translate('Event')),
            'INDI:EVEN:DATE'             => new DateValue(I18N::translate('Date of event')),
            'INDI:EVEN:PLAC'             => new PlaceName(I18N::translate('Place of event')),
            'INDI:EVEN:TYPE'             => new EventOrFactClassification(I18N::translate('Type of event')),
            'INDI:FACT'                  => new IndividualFact(I18N::translate('Fact')),
            'INDI:FACT:TYPE'             => new EventOrFactClassification(I18N::translate('Type of fact')),
            'INDI:FAMC'                  => new XrefFamily(I18N::translate('Family as a child'), ['NOTE' => '0:1', 'PEDI' => '0:1', 'STAT' => '0:1']),
            'INDI:FAMC:PEDI'             => new PedigreeLinkageType(I18N::translate('Relationship to parents')),
            'INDI:FAMC:STAT'             => new ChildLinkageStatus(I18N::translate('Status')),
            'INDI:FAMS'                  => new XrefFamily(I18N::translate('Family as a spouse')),
            'INDI:FCOM'                  => new FirstCommunion(I18N::translate('First communion')),
            'INDI:FCOM:DATE'             => new DateValue(I18N::translate('Date of first communion')),
            'INDI:FCOM:PLAC'             => new PlaceName(I18N::translate('Place of first communion')),
            'INDI:GRAD'                  => new Graduation(I18N::translate('Graduation')),
            'INDI:GRAD:AGNC'             => new ResponsibleAgency(I18N::translate('School or college')),
            'INDI:IDNO'                  => new NationalIdNumber(I18N::translate('Identification number')),
            'INDI:IDNO:TYPE'             => new EventOrFactClassification(I18N::translate('Type of identification number')),
            'INDI:IMMI'                  => new Immigration(I18N::translate('Immigration')),
            'INDI:IMMI:DATE'             => new DateValue(I18N::translate('Date of immigration')),
            'INDI:IMMI:PLAC'             => new PlaceName(I18N::translate('Place of immigration')),
            'INDI:NAME'                  => new NamePersonal(I18N::translate('Name')),
            'INDI:NAME:*:SOUR'           => new XrefSource(I18N::translate('Source citation')),
            'INDI:NAME:*:SOUR:DATA'      => new SourceData(I18N::translate('Data')),
            'INDI:NAME:*:SOUR:DATA:DATE' => new DateValue(I18N::translate('Date of entry in original source')),
            'INDI:NAME:*:SOUR:DATA:TEXT' => new TextFromSource(I18N::translate('Text')),
            'INDI:NAME:*:SOUR:EVEN'      => new EventTypeCitedFrom(I18N::translate('Event')),
            'INDI:NAME:*:SOUR:EVEN:ROLE' => new RoleInEvent(I18N::translate('Role')),
            'INDI:NAME:*:SOUR:NOTE'      => new NoteStructure(I18N::translate('Note')),
            'INDI:NAME:*:SOUR:OBJE'      => new XrefMedia(I18N::translate('Media object')),
            'INDI:NAME:*:SOUR:PAGE'      => new WhereWithinSource(I18N::translate('Citation details')),
            'INDI:NAME:*:SOUR:QUAY'      => new CertaintyAssessment(I18N::translate('Quality of data')),
            'INDI:NAME:FONE'             => new NamePhoneticVariation(I18N::translate('Phonetic name')),
            'INDI:NAME:FONE:GIVN'        => new NamePieceGiven(I18N::translate('Given names')),
            'INDI:NAME:FONE:NICK'        => new NamePieceNickname(I18N::translate('Nickname')),
            'INDI:NAME:FONE:NOTE'        => new NoteStructure(I18N::translate('Note')),
            'INDI:NAME:FONE:NPFX'        => new NamePiecePrefix(I18N::translate('Name prefix')),
            'INDI:NAME:FONE:NSFX'        => new NamePieceSuffix(I18N::translate('Name suffix')),
            'INDI:NAME:FONE:SOUR'        => new XrefSource(I18N::translate('Source citation')),
            'INDI:NAME:FONE:SPFX'        => new NamePieceSurnamePrefix(I18N::translate('Surname prefix')),
            'INDI:NAME:FONE:SURN'        => new NamePieceSurname(I18N::translate('Surname')),
            'INDI:NAME:FONE:TYPE'        => new PhoneticType(I18N::translate('Phonetic type')),
            'INDI:NAME:GIVN'             => new NamePieceGiven(I18N::translate('Given names')),
            'INDI:NAME:NICK'             => new NamePieceNickname(I18N::translate('Nickname')),
            'INDI:NAME:NPFX'             => new NamePiecePrefix(I18N::translate('Name prefix')),
            'INDI:NAME:NSFX'             => new NamePieceSuffix(I18N::translate('Name suffix')),
            'INDI:NAME:ROMN'             => new NameRomanizedVariation(I18N::translate('Romanized name')),
            'INDI:NAME:ROMN:GIVN'        => new NamePieceGiven(I18N::translate('Given names')),
            'INDI:NAME:ROMN:NICK'        => new NamePieceNickname(I18N::translate('Nickname')),
            'INDI:NAME:ROMN:NOTE'        => new NoteStructure(I18N::translate('Note')),
            'INDI:NAME:ROMN:NPFX'        => new NamePiecePrefix(I18N::translate('Name prefix')),
            'INDI:NAME:ROMN:NSFX'        => new NamePieceSuffix(I18N::translate('Name suffix')),
            'INDI:NAME:ROMN:SOUR'        => new XrefSource(I18N::translate('Source citation')),
            'INDI:NAME:ROMN:SPFX'        => new NamePieceSurnamePrefix(I18N::translate('Surname prefix')),
            'INDI:NAME:ROMN:SURN'        => new NamePieceSurname(I18N::translate('Surname')),
            'INDI:NAME:ROMN:TYPE'        => new RomanizedType(I18N::translate('Romanized type')),
            'INDI:NAME:SPFX'             => new NamePieceSurnamePrefix(I18N::translate('Surname prefix')),
            'INDI:NAME:SURN'             => new NamePieceSurname(I18N::translate('Surname')),
            'INDI:NAME:TYPE'             => new NameType(I18N::translate('Type of name')),
            'INDI:NATI'                  => new NationalOrTribalOrigin(I18N::translate('Nationality')),
            'INDI:NATU'                  => new Naturalization(I18N::translate('Naturalization')),
            'INDI:NATU:DATE'             => new DateValue(I18N::translate('Date of naturalization')),
            'INDI:NATU:PLAC'             => new PlaceName(I18N::translate('Place of naturalization')),
            'INDI:NCHI'                  => new CountOfChildren(I18N::translate('Number of children')),
            'INDI:NMR'                   => new CountOfMarriages(I18N::translate('Number of marriages')),
            'INDI:NOTE'                  => new NoteStructure(I18N::translate('Note')),
            'INDI:OBJE'                  => new XrefMedia(I18N::translate('Media object')),
            'INDI:OCCU'                  => new Occupation(I18N::translate('Occupation')),
            'INDI:OCCU:AGNC'             => new ResponsibleAgency(I18N::translate('Employer')),
            'INDI:ORDN'                  => new Ordination(I18N::translate('Ordination')),
            'INDI:ORDN:AGNC'             => new Ordination(I18N::translate('Religious institution')),
            'INDI:ORDN:DATE'             => new Ordination(I18N::translate('Date of ordination')),
            'INDI:ORDN:PLAC'             => new Ordination(I18N::translate('Place of ordination')),
            'INDI:PROB'                  => new Probate(I18N::translate('Probate')),
            'INDI:PROP'                  => new Possessions(I18N::translate('Property')),
            'INDI:REFN'                  => new UserReferenceNumber(I18N::translate('Reference number')),
            'INDI:REFN:TYPE'             => new UserReferenceType(I18N::translate('Type of reference number')),
            'INDI:RELI'                  => new ReligiousAffiliation(I18N::translate('Religion')),
            'INDI:RESI'                  => new Residence(I18N::translate('Residence')),
            'INDI:RESI:DATE'             => new DateValue(I18N::translate('Date of residence')),
            'INDI:RESI:PLAC'             => new PlaceName(I18N::translate('Place of residence')),
            'INDI:RESN'                  => new RestrictionNotice(I18N::translate('Restriction')),
            'INDI:RETI'                  => new Retirement(I18N::translate('Retirement')),
            'INDI:RETI:AGNC'             => new ResponsibleAgency(I18N::translate('Employer')),
            'INDI:RFN'                   => new PermanentRecordFileNumber(I18N::translate('Record file number')),
            'INDI:RIN'                   => new AutomatedRecordId(I18N::translate('Record ID number')),
            'INDI:SEX'                   => new SexValue(I18N::translate('Gender')),
            'INDI:SLGC'                  => new LdsChildSealing(I18N::translate('LDS child sealing')),
            'INDI:SLGC:DATE'             => new DateLdsOrd(I18N::translate('Date of LDS child sealing')),
            'INDI:SLGC:FAMC'             => new XrefFamily(I18N::translate('Parents')),
            'INDI:SLGC:PLAC'             => new PlaceLivingOrdinance(I18N::translate('Place of LDS child sealing')),
            'INDI:SLGC:STAT'             => new LdsChildSealingDateStatus(I18N::translate('Status')),
            'INDI:SLGC:STAT:DATE'        => new ChangeDate(I18N::translate('Status change date')),
            'INDI:SLGC:TEMP'             => new TempleCode(I18N::translate('Temple')),
            'INDI:SOUR'                  => new XrefSource(I18N::translate('Source citation')),
            'INDI:SOUR:DATA'             => new SourceData(I18N::translate('Data')),
            'INDI:SOUR:DATA:DATE'        => new DateValue(I18N::translate('Date of entry in original source')),
            'INDI:SOUR:DATA:TEXT'        => new TextFromSource(I18N::translate('Text')),
            'INDI:SOUR:EVEN'             => new EventTypeCitedFrom(I18N::translate('Event')),
            'INDI:SOUR:EVEN:ROLE'        => new RoleInEvent(I18N::translate('Role')),
            'INDI:SOUR:NOTE'             => new NoteStructure(I18N::translate('Note')),
            'INDI:SOUR:OBJE'             => new XrefMedia(I18N::translate('Media object')),
            'INDI:SOUR:PAGE'             => new WhereWithinSource(I18N::translate('Citation details')),
            'INDI:SOUR:QUAY'             => new CertaintyAssessment(I18N::translate('Quality of data')),
            'INDI:SSN'                   => new SocialSecurityNumber(I18N::translate('Social security number')),
            'INDI:SUBM'                  => new XrefSubmitter(I18N::translate('Submitter')),
            'INDI:TITL'                  => new NobilityTypeTitle(I18N::translate('Title')),
            'INDI:WILL'                  => new Will(I18N::translate('Will')),
            'NOTE'                       => new NoteRecord(I18N::translate('Shared note')),
            'NOTE:CHAN'                  => new Change(I18N::translate('Last change')),
            'NOTE:CHAN:DATE'             => new ChangeDate(I18N::translate('Date of last change')),
            'NOTE:CHAN:DATE:TIME'        => new TimeValue(I18N::translate('Time of last change')),
            'NOTE:CHAN:NOTE'             => new NoteStructure(I18N::translate('Note')),
            'NOTE:CONC'                  => new SubmitterText(I18N::translate('Note')),
            'NOTE:CONT'                  => new SubmitterText(I18N::translate('Continuation')),
            'NOTE:REFN'                  => new UserReferenceNumber(I18N::translate('Reference number')),
            'NOTE:REFN:TYPE'             => new UserReferenceType(I18N::translate('Type of reference number')),
            'NOTE:RIN'                   => new AutomatedRecordId(I18N::translate('Record ID number')),
            'NOTE:SOUR'                  => new XrefSource(I18N::translate('Source citation')),
            'NOTE:SOUR:DATA'             => new SourceData(I18N::translate('Data')),
            'NOTE:SOUR:DATA:DATE'        => new DateValue(I18N::translate('Date of entry in original source')),
            'NOTE:SOUR:DATA:TEXT'        => new TextFromSource(I18N::translate('Text')),
            'NOTE:SOUR:EVEN'             => new EventTypeCitedFrom(I18N::translate('Event')),
            'NOTE:SOUR:EVEN:ROLE'        => new RoleInEvent(I18N::translate('Role')),
            'NOTE:SOUR:NOTE'             => new NoteStructure(I18N::translate('Note')),
            'NOTE:SOUR:OBJE'             => new XrefMedia(I18N::translate('Media object')),
            'NOTE:SOUR:PAGE'             => new WhereWithinSource(I18N::translate('Citation details')),
            'NOTE:SOUR:QUAY'             => new CertaintyAssessment(I18N::translate('Quality of data')),
            'OBJE'                       => new MediaRecord(I18N::translate('Media object')),
            'OBJE:BLOB'                  => new CustomElement(I18N::translate('Binary data object')),
            'OBJE:CHAN'                  => new Change(I18N::translate('Last change')),
            'OBJE:CHAN:DATE'             => new ChangeDate(I18N::translate('Date of last change')),
            'OBJE:CHAN:DATE:TIME'        => new TimeValue(I18N::translate('Time of last change')),
            'OBJE:CHAN:NOTE'             => new NoteStructure(I18N::translate('Note')),
            'OBJE:FILE'                  => new MultimediaFileReference(I18N::translate('Filename')),
            'OBJE:FILE:FORM'             => new MultimediaFormat(I18N::translate('Format')),
            'OBJE:FILE:FORM:TYPE'        => new SourceMediaType(I18N::translate('Media type')),
            'OBJE:FILE:TITL'             => new DescriptiveTitle(I18N::translate('Title')),
            'OBJE:NOTE'                  => new NoteStructure(I18N::translate('Note')),
            'OBJE:REFN'                  => new UserReferenceNumber(I18N::translate('Reference number')),
            'OBJE:REFN:TYPE'             => new UserReferenceType(I18N::translate('Type of reference number')),
            'OBJE:RIN'                   => new AutomatedRecordId(I18N::translate('Record ID number')),
            'OBJE:SOUR'                  => new XrefSource(I18N::translate('Source citation')),
            'OBJE:SOUR:DATA'             => new SourceData(I18N::translate('Data')),
            'OBJE:SOUR:DATA:DATE'        => new DateValue(I18N::translate('Date of entry in original source')),
            'OBJE:SOUR:DATA:TEXT'        => new TextFromSource(I18N::translate('Text')),
            'OBJE:SOUR:EVEN'             => new EventTypeCitedFrom(I18N::translate('Event')),
            'OBJE:SOUR:EVEN:ROLE'        => new RoleInEvent(I18N::translate('Role')),
            'OBJE:SOUR:NOTE'             => new NoteStructure(I18N::translate('Note')),
            'OBJE:SOUR:OBJE'             => new XrefMedia(I18N::translate('Media object')),
            'OBJE:SOUR:PAGE'             => new WhereWithinSource(I18N::translate('Citation details')),
            'OBJE:SOUR:QUAY'             => new CertaintyAssessment(I18N::translate('Quality of data')),
            'REPO'                       => new RepositoryRecord(I18N::translate('Repository')),
            'REPO:ADDR'                  => new AddressLine(I18N::translate('Address')),
            'REPO:ADDR:ADR1'             => new AddressLine1(I18N::translate('Address line 1')),
            'REPO:ADDR:ADR2'             => new AddressLine2(I18N::translate('Address line 2')),
            'REPO:ADDR:ADR3'             => new AddressLine3(I18N::translate('Address line 3')),
            'REPO:ADDR:CITY'             => new AddressCity(I18N::translate('City')),
            'REPO:ADDR:CTRY'             => new AddressCountry(I18N::translate('Country')),
            'REPO:ADDR:POST'             => new AddressPostalCode(I18N::translate('Postal code')),
            'REPO:ADDR:STAE'             => new AddressState(I18N::translate('State')),
            'REPO:CHAN'                  => new Change(I18N::translate('Last change')),
            'REPO:CHAN:DATE'             => new ChangeDate(I18N::translate('Date of last change')),
            'REPO:CHAN:DATE:TIME'        => new TimeValue(I18N::translate('Time of last change')),
            'REPO:CHAN:NOTE'             => new NoteStructure(I18N::translate('Note')),
            'REPO:EMAIL'                 => new AddressEmail(I18N::translate('Email address')),
            'REPO:FAX'                   => new AddressFax(I18N::translate('Fax')),
            'REPO:NAME'                  => new NameOfRepository(I18N::translateContext('Repository', 'Name')),
            'REPO:NOTE'                  => new NoteStructure(I18N::translate('Note')),
            'REPO:PHON'                  => new PhoneNumber(I18N::translate('Phone')),
            'REPO:REFN'                  => new UserReferenceNumber(I18N::translate('Reference number')),
            'REPO:REFN:TYPE'             => new UserReferenceType(I18N::translate('Type of reference number')),
            'REPO:RIN'                   => new AutomatedRecordId(I18N::translate('Record ID number')),
            'REPO:WWW'                   => new AddressWebPage(I18N::translate('URL')),
            'SOUR'                       => new SourceRecord(I18N::translate('Source')),
            'SOUR:ABBR'                  => new SourceFiledByEntry(I18N::translate('Abbreviation')),
            'SOUR:AUTH'                  => new SourceOriginator(I18N::translate('Author')),
            'SOUR:CHAN'                  => new Change(I18N::translate('Last change')),
            'SOUR:CHAN:DATE'             => new ChangeDate(I18N::translate('Date of last change')),
            'SOUR:CHAN:DATE:TIME'        => new TimeValue(I18N::translate('Time of last change')),
            'SOUR:CHAN:NOTE'             => new NoteStructure(I18N::translate('Note')),
            'SOUR:DATA'                  => new EmptyElement(I18N::translate('Data'), ['EVEN' => '0:M', 'AGNC' => '0:1', 'NOTE' => '0:M']),
            'SOUR:DATA:AGNC'             => new ResponsibleAgency(I18N::translate('Agency')),
            'SOUR:DATA:EVEN'             => new EventsRecorded(I18N::translate('Events')),
            'SOUR:DATA:EVEN:DATE'        => new DateValue(I18N::translate('Date range')),
            'SOUR:DATA:EVEN:PLAC'        => new SourceJurisdictionPlace(I18N::translate('Place'), []),
            'SOUR:DATA:NOTE'             => new NoteStructure(I18N::translate('Note')),
            'SOUR:NOTE'                  => new NoteStructure(I18N::translate('Note')),
            'SOUR:OBJE'                  => new XrefMedia(I18N::translate('Media object')),
            'SOUR:PUBL'                  => new SourcePublicationFacts(I18N::translate('Publication')),
            'SOUR:REFN'                  => new UserReferenceNumber(I18N::translate('Reference number')),
            'SOUR:REFN:TYPE'             => new UserReferenceType(I18N::translate('Type of reference number')),
            'SOUR:REPO'                  => new XrefRepository(I18N::translate('Repository')),
            'SOUR:REPO:CALN'             => new SourceCallNumber(I18N::translate('Call number')),
            'SOUR:REPO:CALN:MEDI'        => new SourceMediaType(I18N::translate('Media type')),
            'SOUR:REPO:NOTE'             => new NoteStructure(I18N::translate('Note')),
            'SOUR:RIN'                   => new AutomatedRecordId(I18N::translate('Record ID number')),
            'SOUR:TEXT'                  => new TextFromSource(I18N::translate('Text')),
            'SOUR:TITL'                  => new DescriptiveTitle(I18N::translate('Title')),
            'SUBM'                       => new SubmitterRecord(I18N::translate('Submitter')),
            'SUBM:ADDR'                  => new AddressLine(I18N::translate('Address')),
            'SUBM:ADDR:ADR1'             => new AddressLine1(I18N::translate('Address line 1')),
            'SUBM:ADDR:ADR2'             => new AddressLine2(I18N::translate('Address line 2')),
            'SUBM:ADDR:ADR3'             => new AddressLine3(I18N::translate('Address line 3')),
            'SUBM:ADDR:CITY'             => new AddressCity(I18N::translate('City')),
            'SUBM:ADDR:CTRY'             => new AddressCountry(I18N::translate('Country')),
            'SUBM:ADDR:POST'             => new AddressPostalCode(I18N::translate('Postal code')),
            'SUBM:ADDR:STAE'             => new AddressState(I18N::translate('State')),
            'SUBM:CHAN'                  => new Change(I18N::translate('Last change')),
            'SUBM:CHAN:DATE'             => new ChangeDate(I18N::translate('Date of last change')),
            'SUBM:CHAN:DATE:TIME'        => new TimeValue(I18N::translate('Time of last change')),
            'SUBM:CHAN:NOTE'             => new NoteStructure(I18N::translate('Note')),
            'SUBM:EMAIL'                 => new AddressEmail(I18N::translate('Email address')),
            'SUBM:FAX'                   => new AddressFax(I18N::translate('Fax')),
            'SUBM:LANG'                  => new LanguageId(I18N::translate('Language')),
            'SUBM:NAME'                  => new SubmitterName(I18N::translate('Name')),
            'SUBM:NOTE'                  => new NoteStructure(I18N::translate('Note')),
            'SUBM:OBJE'                  => new XrefMedia(I18N::translate('Media object')),
            'SUBM:PHON'                  => new PhoneNumber(I18N::translate('Phone')),
            'SUBM:RFN'                   => new SubmitterRegisteredRfn(I18N::translate('Record file number')),
            'SUBM:RIN'                   => new AutomatedRecordId(I18N::translate('Record ID number')),
            'SUBM:WWW'                   => new AddressWebPage(I18N::translate('URL')),
            'SUBN'                       => new SubmissionRecord(I18N::translate('Submission')),
            'SUBN:ANCE'                  => new GenerationsOfAncestors(I18N::translate('Generations of ancestors')),
            'SUBN:CHAN'                  => new Change(I18N::translate('Last change')),
            'SUBN:CHAN:DATE'             => new ChangeDate(I18N::translate('Date of last change')),
            'SUBN:CHAN:DATE:TIME'        => new TimeValue(I18N::translate('Time of last change')),
            'SUBN:CHAN:NOTE'             => new NoteStructure(I18N::translate('Note')),
            'SUBN:DESC'                  => new GenerationsOfDescendants(I18N::translate('Generations of descendants')),
            'SUBN:FAMF'                  => new NameOfFamilyFile(I18N::translate('Family file')),
            'SUBN:NOTE'                  => new NoteStructure(I18N::translate('Note')),
            'SUBN:ORDI'                  => new OrdinanceProcessFlag(I18N::translate('Ordinance')),
            'SUBN:RIN'                   => new AutomatedRecordId(I18N::translate('Record ID number')),
            'SUBN:SUBM'                  => new XrefSubmitter(I18N::translate('Submitter')),
            'SUBN:TEMP'                  => new TempleCode(/* I18N: https://en.wikipedia.org/wiki/Temple_(LDS_Church)*/ I18N::translate('Temple')),
            'TRLR'                       => new EmptyElement(I18N::translate('Trailer')),
        ];
    }

    /**
     * Definitions for GEDCOM 7.
     *
     * @return array<string,ElementInterface>
     */
    private function gedcom7Tags(): array
    {
        return [
            'FAM:NO' => new NonEvent('Event did not happen'),
            'INDI:NO' => new NonEvent('Event did not happen'),
            'FAM:*:ASSO'                 => new XrefAssociate(I18N::translate('Associate')),
            'FAM:*:ASSO:PHRASE'          => new CustomElement(I18N::translate('Phrase')),
            'FAM:*:ASSO:ROLE'            => new RoleInEvent(I18N::translate('Role')),
            'FAM:*:ASSO:ROLE:PHRASE'     => new CustomElement(I18N::translate('Phrase')),
            'FAM:*:DATE:TIME'            => new TimeValue(I18N::translate('Time')),
            'FAM:*:PLAC:EXID'            => new ExternalIdentifier(I18N::translate('External identifier')),
            'FAM:*:PLAC:EXID:TYPE'       => new ExternalIdentifierType(I18N::translate('Type')),
            'FAM:*:SDATE'                => new CustomElement(I18N::translate('Sort date')),
            'FAM:*:SDATE:PHRASE'         => new CustomElement(I18N::translate('Phrase')),
            'FAM:*:SDATE:TIME'           => new CustomElement(I18N::translate('Sort time')),
            'FAM:*:SNOTE'                => new XrefSharedNote(I18N::translate('Shared note')),
            'FAM:*:UID'                  => new Uid(I18N::translate('Unique identifier')),
            'FAM:ASSO'                   => new XrefAssociate(I18N::translate('Associate')),
            'FAM:ASSO:PHRASE'            => new CustomElement(I18N::translate('Phrase')),
            'FAM:ASSO:ROLE'              => new RoleInEvent(I18N::translate('Role')),
            'FAM:ASSO:ROLE:PHRASE'       => new CustomElement(I18N::translate('Phrase')),
            'FAM:CREA'                   => new Creation(I18N::translate('Created at')),
            'FAM:CREA:DATE'              => new DateValue(I18N::translate('Creation date')),
            'FAM:CREA:DATE:TIME'         => new TimeValue(I18N::translate('Creation time')),
            'FAM:EXID'                   => new ExternalIdentifier(I18N::translate('External identifier')),
            'FAM:EXID:TYPE'              => new ExternalIdentifierType(I18N::translate('Type')),
            'FAM:FACT'                   => new FamilyFact(I18N::translate('Fact')),
            'FAM:REFN'                   => new UserReferenceNumber(I18N::translate('Reference number')),
            'FAM:REFN:TYPE'              => new UserReferenceType(I18N::translate('Type')),
            'FAM:SNOTE'                  => new XrefSharedNote(I18N::translate('Shared note')),
            'FAM:UID'                    => new Uid(I18N::translate('Unique identifier')),
            'INDI:*:ASSO'                => new XrefAssociate(I18N::translate('Associate')),
            'INDI:*:ASSO:PHRASE'         => new CustomElement(I18N::translate('Phrase')),
            'INDI:*:ASSO:ROLE'           => new RoleInEvent(I18N::translate('Role')),
            'INDI:*:ASSO:ROLE:PHRASE'    => new CustomElement(I18N::translate('Phrase')),
            'INDI:*:DATE:TIME'           => new TimeValue(I18N::translate('Time')),
            'INDI:*:PLAC:EXID'           => new ExternalIdentifier(I18N::translate('External identifier')),
            'INDI:*:PLAC:EXID:TYPE'      => new ExternalIdentifierType(I18N::translate('Type')),
            'INDI:*:SDATE'               => new CustomElement(I18N::translate('Sort date')),
            'INDI:*:SDATE:PHRASE'        => new CustomElement(I18N::translate('Phrase')),
            'INDI:*:SDATE:TIME'          => new CustomElement(I18N::translate('Sort time')),
            'INDI:*:SNOTE'               => new XrefSharedNote(I18N::translate('Shared note')),
            'INDI:*:UID'                 => new Uid(I18N::translate('Unique identifier')),
            'INDI:ADOP:FAMC:ADOP:PHRASE' => new CustomElement(I18N::translate('Phrase')),
            'INDI:ALIA:PHRASE'           => new CustomElement(I18N::translate('Phrase')),
            'INDI:ASSO'                  => new XrefAssociate(I18N::translate('Associate')),
            'INDI:ASSO:PHRASE'           => new CustomElement(I18N::translate('Phrase')),
            'INDI:ASSO:ROLE'             => new RoleInEvent(I18N::translate('Role')),
            'INDI:ASSO:ROLE:PHRASE'      => new CustomElement(I18N::translate('Phrase')),
            'INDI:BIRT:DATE:TIME'        => new TimeValue(I18N::translate('Time of birth')),
            'INDI:CREA'                  => new Creation(I18N::translate('Created at')),
            'INDI:CREA:DATE'             => new DateValue(I18N::translate('Creation date')),
            'INDI:CREA:DATE:TIME'        => new TimeValue(I18N::translate('Creation time')),
            'INDI:DEAT:DATE:TIME'        => new TimeValue(I18N::translate('Time of death')),
            'INDI:EXID'                  => new ExternalIdentifier(I18N::translate('External identifier')),
            'INDI:EXID:TYPE'             => new ExternalIdentifierType(I18N::translate('Type')),
            'INDI:INIL'                  => /* I18N: GEDCOM tag INIL - an LDS ceremony */ new LdsInitiatory(I18N::translate('LDS initiatory')),
            'INDI:INIL:STAT'             => new LdsOrdinanceStatus(I18N::translate('Status')),
            'INDI:INIL:STAT:DATE'        => new ChangeDate(I18N::translate('Date of status change')),
            'INDI:INIL:STAT:DATE:TIME'   => new TimeValue(I18N::translate('Time of status change')),
            'INDI:INIL:TEMP'             => new TempleCode(I18N::translate('Temple')),
            'INDI:REFN'                  => new UserReferenceNumber(I18N::translate('Reference number')),
            'INDI:REFN:TYPE'             => new UserReferenceType(I18N::translate('Type')),
            'INDI:SNOTE'                 => new XrefSharedNote(I18N::translate('Shared note')),
            'INDI:UID'                   => new Uid(I18N::translate('Unique identifier')),
            'OBJE:CREA'                  => new Creation(I18N::translate('Created at')),
            'OBJE:CREA:DATE'             => new DateValue(I18N::translate('Creation date')),
            'OBJE:CREA:DATE:TIME'        => new TimeValue(I18N::translate('Creation time')),
            'OBJE:SNOTE'                 => new XrefSharedNote(I18N::translate('Shared note')),
            'REPO:CREA'                  => new Creation(I18N::translate('Created at')),
            'REPO:CREA:DATE'             => new DateValue(I18N::translate('Creation date')),
            'REPO:CREA:DATE:TIME'        => new TimeValue(I18N::translate('Creation time')),
            'REPO:EXID'                  => new ExternalIdentifier(I18N::translate('External identifier')),
            'REPO:EXID:TYPE'             => new ExternalIdentifierType(I18N::translate('Type')),
            'REPO:REFN'                  => new UserReferenceNumber(I18N::translate('Reference number')),
            'REPO:REFN:TYPE'             => new UserReferenceType(I18N::translate('Type')),
            'REPO:SNOTE'                 => new XrefSharedNote(I18N::translate('Shared note')),
            'REPO:UID'                   => new Uid(I18N::translate('Unique identifier')),
            'SNOTE:CREA'                 => new Creation(I18N::translate('Created at')),
            'SNOTE:CREA:DATE'            => new DateValue(I18N::translate('Creation date')),
            'SNOTE:CREA:DATE:TIME'       => new TimeValue(I18N::translate('Creation time')),
            'SNOTE:EXID'                 => new ExternalIdentifier(I18N::translate('External identifier')),
            'SNOTE:EXID:TYPE'            => new ExternalIdentifierType(I18N::translate('Type')),
            'SNOTE:REFN'                 => new UserReferenceNumber(I18N::translate('Reference number')),
            'SNOTE:REFN:TYPE'            => new UserReferenceType(I18N::translate('Type')),
            'SNOTE:UID'                  => new Uid(I18N::translate('Unique identifier')),
            'SOUR:CREA'                  => new Creation(I18N::translate('Created at')),
            'SOUR:CREA:DATE'             => new DateValue(I18N::translate('Creation date')),
            'SOUR:CREA:DATE:TIME'        => new TimeValue(I18N::translate('Creation time')),
            'SOUR:EXID'                  => new ExternalIdentifier(I18N::translate('External identifier')),
            'SOUR:EXID:TYPE'             => new ExternalIdentifierType(I18N::translate('Type')),
            'SOUR:REFN'                  => new UserReferenceNumber(I18N::translate('Reference number')),
            'SOUR:REFN:TYPE'             => new UserReferenceType(I18N::translate('Type')),
            'SOUR:SNOTE'                 => new XrefSharedNote(I18N::translate('Shared note')),
            'SOUR:UID'                   => new Uid(I18N::translate('Unique identifier')),
            'SUBM:CREA'                  => new Creation(I18N::translate('Created at')),
            'SUBM:CREA:DATE'             => new DateValue(I18N::translate('Creation date')),
            'SUBM:CREA:DATE:TIME'        => new TimeValue(I18N::translate('Creation time')),
            'SUBM:EXID'                  => new ExternalIdentifier(I18N::translate('External identifier')),
            'SUBM:EXID:TYPE'             => new ExternalIdentifierType(I18N::translate('Type')),
            'SUBM:REFN'                  => new UserReferenceNumber(I18N::translate('Reference number')),
            'SUBM:REFN:TYPE'             => new UserReferenceType(I18N::translate('Type')),
            'SUBM:SNOTE'                 => new XrefSharedNote(I18N::translate('Shared note')),
            'SUBM:UID'                   => new Uid(I18N::translate('Unique identifier')),
        ];
    }

    /**
     * Aldfaer also uses: _BOLD, _ITALIC, _UNDERLINE, _COLOR
     *
     * @return array<string,ElementInterface>
     */
    private function aldfaerTags(): array
    {
        return [
            'FAM:MARR_CIVIL'           => new Marriage(I18N::translate('Civil marriage')),
            'FAM:MARR_PARTNERS'        => new Marriage(I18N::translate('Registered partnership')),
            'FAM:MARR_RELIGIOUS'       => new Marriage(I18N::translate('Religious marriage')),
            'FAM:MARR_UNKNOWN'         => new Marriage(I18N::translate('Marriage')),
            'FAM:_ALDFAER_NOREL'       => new EmptyElement('No relation'), // What is this?
            'HEAD:SUBM:ADDR'           => new AddressLine(I18N::translate('Address')),
            'HEAD:SUBM:PHON'           => new PhoneNumber(I18N::translate('Phone')),
            'HEAD:SUBM:_EMAI'          => new AddressEmail(I18N::translate('Email')),
            'HEAD:SUBM:_FAX'           => new AddressFax(I18N::translate('Fax')),
            'HEAD:SUBM:_WWW'           => new AddressWebPage(I18N::translate('URL')),
            'INDI:BIRT:_ALDFAER_TIME'  => new TimeValue(I18N::translate('Time of birth')),
            'INDI:BIRT:_LENGTH'        => new CustomElement(I18N::translate('Length')),
            'INDI:BIRT:_WEIGHT'        => new CustomElement(I18N::translate('Weight')),
            'INDI:DEAT:_ALDFAER_TIME'  => new TimeValue(I18N::translate('Time of death')),
            'INDI:_REFERENCE'          => new CustomElement(''),
            'INDI:_PRIVACY'            => new CustomElement(''),
            'INDI:_PRIVACY:_OBJECTION' => new CustomElement(''),
            'INDI:_PRIVACY:_PUBLISH'   => new CustomElement(''),
            'INDI:NAME:_SURNAS'        => new CustomElement(I18N::translate('Alternative spelling of surname')),
            'INDI:DEAT:_DATE'          => new DateValue(I18N::translate('Date')),
            'INDI:_INQUBIRT'           => new CustomElement(I18N::translate('')),
            'INDI:_INQUCHIL'           => new CustomElement(I18N::translate('')),
            'INDI:_INQURELA'           => new CustomElement(I18N::translate('')),
            'INDI:_INQUDEAT'           => new CustomElement(I18N::translate('')),
            'INDI:_INQUVAR1'           => new CustomElement(I18N::translate('')),
            'INDI:_INQUVAR1CAT'        => new CustomElement(I18N::translate('')),
            'INDI:_INQUVAR2'           => new CustomElement(I18N::translate('')),
            'INDI:_INQUVAR2CAT'        => new CustomElement(I18N::translate('')),
            'INDI:_INQUVAR3'           => new CustomElement(I18N::translate('')),
            'INDI:_INQUVAR3CAT'        => new CustomElement(I18N::translate('')),
            'INDI:_NOPARTNER'          => new CustomElement(I18N::translate('')),
            'INDI:_NEW'                => new CustomElement(I18N::translate('')),
        ];
    }

    /**
     * @return array<string,ElementInterface>
     *
     * @see https://www.webtrees.net/index.php/en/forum/help-for-release-2-1-x/36664-2-1-beta-support-for-indi-even-sour-data-note-and-the-like
     */
    private function ancestryTags(): array
    {
        return [
            'HEAD:SOUR:_TREE'       => new CustomElement(I18N::translate('Family tree')),
            'HEAD:SOUR:_TREE:NOTE'  => new SubmitterText(I18N::translate('Note')),
            'HEAD:SOUR:_TREE:RIN'   => new AutomatedRecordId(I18N::translate('Record ID number')),
            'INDI:*:SOUR:_APID'     => /* I18N: GEDCOM tag _APID */ new CustomElement(I18N::translate('Ancestry PID')),
            'INDI:*:SOUR:DATA:NOTE' => new SubmitterText(I18N::translate('Note')),
            'INDI:_EMPLOY'          => new CustomFact(I18N::translate('Occupation')),
            'INDI:_FUN'             => new CustomEvent(I18N::translate('Funeral')),
            'INDI:_INIT'            => new LdsInitiatory(I18N::translate('LDS initiatory')),
            'INDI:_ORDI'            => new CustomEvent(I18N::translate('Ordination')),
            'INDI:_ORIG'            => new CustomFact(I18N::translate('Origin')),
            'INDI:_DEST'            => new CustomFact(I18N::translate('Destination')),
            'OBJE:DATE'             => new DateValue(I18N::translate('Date')),
            'OBJE:PLAC'             => new PlaceName(I18N::translate('Place')),
            'OBJE:_CREA'            => /* I18N: GEDCOM tag _CREA */ new CustomElement(I18N::translate('Created at')),
            'OBJE:_ORIG'            => /* I18N: GEDCOM tag _ORIG */ new CustomElement(I18N::translate('Original text')),
            'OBJE:_ORIG:_URL'       => new AddressWebPage(I18N::translate('URL')),
        ];
    }

    /**
     * @return array<string,ElementInterface>
     *
     * @see https://wiki-de.genealogy.net/GEDCOM/_Nutzerdef-Tag
     */
    private function brothersKeeperTags(): array
    {
        return [
            'FAM:*:_EVN'       => new CustomElement('Event number'),
            'FAM:CHIL:_FREL'   => new CustomElement(I18N::translate('Relationship to father')),
            'FAM:CHIL:_MREL'   => new CustomElement(I18N::translate('Relationship to mother')),
            'FAM:_COML'        => new CustomFamilyEvent(I18N::translate('Common law marriage')),
            'FAM:_MARI'        => new CustomFamilyEvent(I18N::translate('Marriage intention')),
            'FAM:_MBON'        => new CustomFamilyEvent(I18N::translate('Marriage bond')),
            'FAM:_NMR'         => new CustomFamilyEvent(I18N::translate('Not married'), ['NOTE' => '0:M', 'SOUR' => '0:M']),
            'FAM:_PRMN'        => new CustomElement(I18N::translate('Permanent number')),
            'FAM:_SEPR'        => new CustomFamilyEvent(I18N::translate('Separated')),
            'FAM:_TODO'        => new CustomElement(I18N::translate('Research task')),
            'INDI:*:_EVN'      => new CustomElement('Event number'),
            'INDI:NAME:_ADPN'  => new NamePersonal(I18N::translate('Adopted name'), []),
            'INDI:NAME:_AKAN'  => new NamePersonal(I18N::translate('Also known as'), []),
            'INDI:NAME:_BIRN'  => new NamePersonal(I18N::translate('Birth name'), []),
            'INDI:NAME:_CALL'  => new NamePersonal('Called name', []),
            'INDI:NAME:_CENN'  => new NamePersonal('Census name', []),
            'INDI:NAME:_CURN'  => new NamePersonal('Current name', []),
            'INDI:NAME:_FARN'  => new NamePersonal(I18N::translate('Estate name'), []),
            'INDI:NAME:_FKAN'  => new NamePersonal('Formal name', []),
            'INDI:NAME:_FRKA'  => new NamePersonal('Formerly known as', []),
            'INDI:NAME:_GERN'  => new NamePersonal('German name', []),
            'INDI:NAME:_HEBN'  => new NamePersonal(I18N::translate('Hebrew name'), []),
            'INDI:NAME:_HNM'   => new NamePersonal(I18N::translate('Hebrew name'), []),
            'INDI:NAME:_INDG'  => new NamePersonal('Indigenous name', []),
            'INDI:NAME:_INDN'  => new NamePersonal('Indian name', []),
            'INDI:NAME:_LNCH'  => new NamePersonal('Legal name change', []),
            'INDI:NAME:_MARN'  => new NamePersonal('Married name', []),
            'INDI:NAME:_MARNM' => new NamePersonal('Married name', []),
            'INDI:NAME:_OTHN'  => new NamePersonal('Other name', []),
            'INDI:NAME:_RELN'  => new NamePersonal('Religious name', []),
            'INDI:NAME:_SHON'  => new NamePersonal('Short name', []),
            'INDI:NAME:_SLDN'  => new NamePersonal('Soldier name', []),
            'INDI:_ADPF'       => new CustomElement(I18N::translate('Adopted by father')),
            'INDI:_ADPM'       => new CustomElement(I18N::translate('Adopted by mother')),
            'INDI:_BRTM'       => new CustomIndividualEvent(I18N::translate('Brit milah')),
            'INDI:_BRTM:DATE'  => new DateValue(I18N::translate('Date of brit milah')),
            'INDI:_BRTM:PLAC'  => new PlaceName(I18N::translate('Place of brit milah')),
            'INDI:_EMAIL'      => new AddressEmail(I18N::translate('Email address')),
            'INDI:_EYEC'       => new CustomFact(I18N::translate('Eye color')),
            'INDI:_FNRL'       => new CustomElement(I18N::translate('Funeral')),
            'INDI:_HAIR'       => new CustomFact(I18N::translate('Hair color')),
            'INDI:_HEIG'       => new CustomFact(I18N::translate('Height')),
            'INDI:_INTE'       => new CustomElement(I18N::translate('Interment')),
            'INDI:_MEDC'       => new CustomFact(I18N::translate('Medical')),
            'INDI:_MILT'       => new CustomElement(I18N::translate('Military service')),
            'INDI:_NLIV'       => new CustomFact(I18N::translate('Not living')),
            'INDI:_NMAR'       => new CustomFact(I18N::translate('Never married'), ['NOTE' => '0:M', 'SOUR' => '0:M']),
            'INDI:_PRMN'       => new CustomElement(I18N::translate('Permanent number')),
            'INDI:_TODO'       => new CustomElement(I18N::translate('Research task')),
            'INDI:_WEIG'       => new CustomFact(I18N::translate('Weight')),
            'INDI:_YART'       => new CustomIndividualEvent(I18N::translate('Yahrzeit')),
            // 1 XXXX
            // 2 _EVN ##
            // 1 ASSO @Xnnn@
            // 2 RELA Witness at event _EVN ##
        ];
    }

    /**
     * @return array<string,ElementInterface>
     */
    private function familySearchTags(): array
    {
        return [
            'INDI:_FSFTID' => /* I18N: familysearch.org */ new FamilySearchFamilyTreeId(I18N::translate('FamilySearch ID')),
        ];
    }

    /**
     * @return array<string,ElementInterface>
     */
    private function familyTreeBuilderTags(): array
    {
        return [
            '*:_UPD'              => /* I18N: GEDCOM tag _UPD */ new CustomElement(I18N::translate('Updated at')), // e.g. "1 _UPD 14 APR 2012 00:14:10 GMT-5"
            'INDI:NAME:_AKA'      => new NamePersonal(I18N::translate('Also known as'), []),
            'OBJE:_ALBUM'         => new CustomElement(I18N::translate('Album')), // XREF to an album
            'OBJE:_DATE'          => new DateValue(I18N::translate('Date')),
            'OBJE:_FILESIZE'      => new CustomElement(I18N::translate('File size')),
            'OBJE:_PHOTO_RIN'     => new CustomElement(I18N::translate('Record ID number')),
            'OBJE:_PLACE'         => new PlaceName(I18N::translate('Place')),
            '_ALBUM:_PHOTO'       => new CustomElement(I18N::translate('Photo')),
            '_ALBUM:_PHOTO:_PRIN' => new CustomElement(I18N::translate('Highlighted image')),
        ];
    }

    /**
     * @return array<string,ElementInterface>
     *
     * @see https://wiki-de.genealogy.net/GEDCOM/_Nutzerdef-Tag
     */
    private function familyTreeMakerTags(): array
    {
        return [
            'FAM:CHIL:_FREL'              => new CustomElement(I18N::translate('Relationship to father')),
            'FAM:CHIL:_MREL'              => new CustomElement(I18N::translate('Relationship to mother')),
            'FAM:_DETS'                   => new CustomElement(I18N::translate('Death of one spouse')),
            'FAM:_FA1'                    => new CustomElement(I18N::translate('Fact 1')),
            'FAM:_FA10'                   => new CustomElement(I18N::translate('Fact 10')),
            'FAM:_FA11'                   => new CustomElement(I18N::translate('Fact 11')),
            'FAM:_FA12'                   => new CustomElement(I18N::translate('Fact 12')),
            'FAM:_FA13'                   => new CustomElement(I18N::translate('Fact 13')),
            'FAM:_FA2'                    => new CustomElement(I18N::translate('Fact 2')),
            'FAM:_FA3'                    => new CustomElement(I18N::translate('Fact 3')),
            'FAM:_FA4'                    => new CustomElement(I18N::translate('Fact 4')),
            'FAM:_FA5'                    => new CustomElement(I18N::translate('Fact 5')),
            'FAM:_FA6'                    => new CustomElement(I18N::translate('Fact 6')),
            'FAM:_FA7'                    => new CustomElement(I18N::translate('Fact 7')),
            'FAM:_FA8'                    => new CustomElement(I18N::translate('Fact 8')),
            'FAM:_FA9'                    => new CustomElement(I18N::translate('Fact 9')),
            'FAM:_MEND'                   => new CustomElement(I18N::translate('Marriage ending status')),
            'FAM:_MSTAT'                  => new CustomElement(I18N::translate('Marriage beginning status')),
            'FAM:_SEPR'                   => new CustomElement(I18N::translate('Separation')),
            'HEAD:_SCHEMA'                => new CustomElement(I18N::translate('Schema')),
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
            'HEAD:_SCHEMA:INDI:_FREL'     => new CustomElement(I18N::translate('Relationship to father')),
            'HEAD:_SCHEMA:INDI:_M*:LABL'  => new CustomElement(I18N::translate('Label')),
            'HEAD:_SCHEMA:INDI:_MREL'     => new CustomElement(I18N::translate('Relationship to mother')),
            'INDI:*:SOUR:_APID'           => /* I18N: GEDCOM tag _APID */ new CustomElement(I18N::translate('Ancestry.com source identifier')),
            'INDI:*:SOUR:_LINK'           => new CustomElement(I18N::translate('External link')),
            'INDI:NAME:_AKA'              => new NamePersonal(I18N::translate('Also known as'), []),
            'INDI:NAME:_MARNM'            => new NamePersonal(I18N::translate('Married name'), []),
            'INDI:_CIRC'                  => new CustomElement(I18N::translate('Circumcision')),
            'INDI:_DCAUSE'                => new CustomElement(I18N::translate('Cause of death')),
            'INDI:_DEG'                   => new CustomElement(I18N::translate('Degree')),
            'INDI:_DNA'                   => new CustomElement(I18N::translate('DNA markers')),
            'INDI:_ELEC'                  => new CustomElement('Elected'),
            'INDI:_EMPLOY'                => new CustomElement('Employment'),
            'INDI:_EXCM'                  => new CustomElement('Excommunicated'),
            'INDI:_FA1'                   => new CustomElement(I18N::translate('Fact 1')),
            'INDI:_FA10'                  => new CustomElement(I18N::translate('Fact 10')),
            'INDI:_FA11'                  => new CustomElement(I18N::translate('Fact 11')),
            'INDI:_FA12'                  => new CustomElement(I18N::translate('Fact 12')),
            'INDI:_FA13'                  => new CustomElement(I18N::translate('Fact 13')),
            'INDI:_FA2'                   => new CustomElement(I18N::translate('Fact 2')),
            'INDI:_FA3'                   => new CustomElement(I18N::translate('Fact 3')),
            'INDI:_FA4'                   => new CustomElement(I18N::translate('Fact 4')),
            'INDI:_FA5'                   => new CustomElement(I18N::translate('Fact 5')),
            'INDI:_FA6'                   => new CustomElement(I18N::translate('Fact 6')),
            'INDI:_FA7'                   => new CustomElement(I18N::translate('Fact 7')),
            'INDI:_FA8'                   => new CustomElement(I18N::translate('Fact 8')),
            'INDI:_FA9'                   => new CustomElement(I18N::translate('Fact 9')),
            'INDI:_MDCL'                  => new CustomElement(I18N::translate('Medical')),
            'INDI:_MILT'                  => new CustomElement(I18N::translate('Military service')),
            'INDI:_MILTID'                => new CustomElement('Military ID number'),
            'INDI:_MISN'                  => new CustomElement('Mission'),
            'INDI:_NAMS'                  => new CustomElement(I18N::translate('Namesake')),
            'INDI:_UNKN'                  => new CustomElement(I18N::translate('Unknown')), // Special individual ID code for later file comparisons
        ];
    }

    /**
     * @return array<string,ElementInterface>
     */
    private function gedcomLTags(): array
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
            'FAM:*:_ASSO:NOTE'                => new NoteStructure(I18N::translate('Note')),
            'FAM:*:_ASSO:RELA'                => new RelationIsDescriptor(I18N::translate('Relationship')),
            'FAM:*:_ASSO:SOUR'                => new XrefSource(I18N::translate('Source citation')),
            'FAM:*:_ASSO:SOUR:DATA'           => new SourceData(I18N::translate('Data')),
            'FAM:*:_ASSO:SOUR:DATA:DATE'      => new DateValue(I18N::translate('Date of entry in original source')),
            'FAM:*:_ASSO:SOUR:DATA:TEXT'      => new TextFromSource(I18N::translate('Text')),
            'FAM:*:_ASSO:SOUR:EVEN'           => new EventTypeCitedFrom(I18N::translate('Event')),
            'FAM:*:_ASSO:SOUR:EVEN:ROLE'      => new RoleInEvent(I18N::translate('Role')),
            'FAM:*:_ASSO:SOUR:NOTE'           => new NoteStructure(I18N::translate('Note')),
            'FAM:*:_ASSO:SOUR:OBJE'           => new XrefMedia(I18N::translate('Media object')),
            'FAM:*:_ASSO:SOUR:PAGE'           => new WhereWithinSource(I18N::translate('Citation details')),
            'FAM:*:_ASSO:SOUR:QUAY'           => new CertaintyAssessment(I18N::translate('Quality of data')),
            'FAM:*:_WITN'                     => new CustomElement(I18N::translate('Witnesses')),
            'FAM:_ASSO'                       => new XrefAssociate(I18N::translate('Associate')),
            'FAM:_ASSO:RELA'                  => new RelationIsDescriptor(I18N::translate('Relationship')),
            'FAM:_STAT'                       => new FamilyStatusText(I18N::translate('Family status')),
            'FAM:_TODO'                       => new ResearchTask(I18N::translate('Research task'), ['DESC' => '1:1', '_CAT' => '0:1', '_PRTY' => '0:1', 'TYPE' => '0:1', 'NOTE' => '0:M', 'DATA' => '0:1', 'STAT'  => '0:1', '_CDATE' => '0:1', '_RDATE' => '0:1', 'REPO' => '0:1', '_UID' => '0:M']),
            'FAM:_TODO:DATA'                  => new SubmitterText(I18N::translate('The solution')),
            'FAM:_TODO:DATE'                  => new DateValue(I18N::translate('Creation date')),
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
            'INDI:*:_ASSO:NOTE'               => new NoteStructure(I18N::translate('Note')),
            'INDI:*:_ASSO:RELA'               => new RelationIsDescriptor(I18N::translate('Relationship')),
            'INDI:*:_ASSO:SOUR'               => new XrefSource(I18N::translate('Source citation')),
            'INDI:*:_ASSO:SOUR:DATA'          => new SourceData(I18N::translate('Data')),
            'INDI:*:_ASSO:SOUR:DATA:DATE'     => new DateValue(I18N::translate('Date of entry in original source')),
            'INDI:*:_ASSO:SOUR:DATA:TEXT'     => new TextFromSource(I18N::translate('Text')),
            'INDI:*:_ASSO:SOUR:EVEN'          => new EventTypeCitedFrom(I18N::translate('Event')),
            'INDI:*:_ASSO:SOUR:EVEN:ROLE'     => new RoleInEvent(I18N::translate('Role')),
            'INDI:*:_ASSO:SOUR:NOTE'          => new NoteStructure(I18N::translate('Note')),
            'INDI:*:_ASSO:SOUR:OBJE'          => new XrefMedia(I18N::translate('Media object')),
            'INDI:*:_ASSO:SOUR:PAGE'          => new WhereWithinSource(I18N::translate('Citation details')),
            'INDI:*:_ASSO:SOUR:QUAY'          => new CertaintyAssessment(I18N::translate('Quality of data')),
            'INDI:*:_WITN'                    => new CustomElement(I18N::translate('Witnesses')),
            'INDI:BAPM:_GODP'                 => new CustomElement(I18N::translate('Godparents')),
            'INDI:CHR:_GODP'                  => new CustomElement(I18N::translate('Godparents')),
            'INDI:NAME:_RUFNAME'              => new NamePieceGiven(I18N::translate('Rufname')),
            'INDI:OBJE:_PRIM'                 => new CustomElement(I18N::translate('Highlighted image')),
            'INDI:SEX'                        => new SexXValue(I18N::translate('Gender')),
            'INDI:_TODO'                      => new ResearchTask(I18N::translate('Research task')),
            'INDI:_TODO:DATA'                 => new SubmitterText(I18N::translate('The solution')),
            'INDI:_TODO:DATE'                 => new DateValue(I18N::translate('Creation date')),
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
            'SUBM:ADDR:_NAME'                 => new CustomElement(I18N::translate('Name of addressee')),
            'SUBM:_UID'                       => new PafUid(I18N::translate('Unique identifier')),
            'SUBN:_UID'                       => new PafUid(I18N::translate('Unique identifier')),
            '_LOC'                            => new LocationRecord(I18N::translate('Location')),
            '_LOC:CHAN'                       => new Change(I18N::translate('Last change')),
            '_LOC:CHAN:DATE'                  => new ChangeDate(I18N::translate('Date of last change')),
            '_LOC:CHAN:DATE:TIME'             => new TimeValue(I18N::translate('Time of last change')),
            '_LOC:CHAN:NOTE'                  => new NoteStructure(I18N::translate('Note')),
            '_LOC:EVEN'                       => new CustomEvent(I18N::translate('Event')),
            '_LOC:EVEN:DATE'                  => new DateValue(I18N::translate('Date of event')),
            '_LOC:EVEN:PLAC'                  => new PlaceName(I18N::translate('Place of event')),
            '_LOC:EVEN:PLAC:FONE'             => new PlacePhoneticVariation(I18N::translate('Phonetic place')),
            '_LOC:EVEN:PLAC:FONE:TYPE'        => new PhoneticType(I18N::translate('Type')),
            '_LOC:EVEN:PLAC:FORM'             => new PlaceHierarchy(I18N::translate('Format')),
            '_LOC:EVEN:PLAC:MAP'              => new EmptyElement(I18N::translate('Coordinates'), ['LATI' => '1:1', 'LONG' => '1:1']),
            '_LOC:EVEN:PLAC:MAP:LATI'         => new PlaceLatitude(I18N::translate('Latitude')),
            '_LOC:EVEN:PLAC:MAP:LONG'         => new PlaceLongtitude(I18N::translate('Longitude')),
            '_LOC:EVEN:PLAC:NOTE'             => new NoteStructure(I18N::translate('Note')),
            '_LOC:EVEN:PLAC:ROMN'             => new PlaceRomanizedVariation(I18N::translate('Romanized place')),
            '_LOC:EVEN:PLAC:ROMN:TYPE'        => new RomanizedType(I18N::translate('Type')),
            '_LOC:EVEN:PLAC:_LOC'             => new XrefLocation(I18N::translate('Location')),
            '_LOC:EVEN:TYPE'                  => new EventAttributeType(I18N::translate('Type of event')),
            '_LOC:EVEN:AGNC'                  => new ResponsibleAgency(I18N::translate('Agency')),
            '_LOC:EVEN:ADDR'                  => new AddressLine(I18N::translate('Address')),
            '_LOC:EVEN:ADDR:ADR1'             => new AddressLine1(I18N::translate('Address line 1')),
            '_LOC:EVEN:ADDR:ADR2'             => new AddressLine2(I18N::translate('Address line 2')),
            '_LOC:EVEN:ADDR:ADR3'             => new AddressLine3(I18N::translate('Address line 3')),
            '_LOC:EVEN:ADDR:CITY'             => new AddressCity(I18N::translate('City')),
            '_LOC:EVEN:ADDR:CTRY'             => new AddressCountry(I18N::translate('Country')),
            '_LOC:EVEN:ADDR:POST'             => new AddressPostalCode(I18N::translate('Postal code')),
            '_LOC:EVEN:ADDR:STAE'             => new AddressState(I18N::translate('State')),
            '_LOC:EVEN:CAUS'                  => new CauseOfEvent(I18N::translate('Cause')),
            '_LOC:EVEN:RELI'                  => new ReligiousAffiliation(I18N::translate('Religion'), []),
            '_LOC:EVEN:RESN'                  => new RestrictionNotice(I18N::translate('Restriction')),
            '_LOC:EVEN:SOUR'                  => new XrefSource(I18N::translate('Source citation')),
            '_LOC:EVEN:NOTE'                  => new NoteStructure(I18N::translate('Note')),
            '_LOC:EVEN:OBJE'                  => new XrefMedia(I18N::translate('Media object')),
            '_LOC:MAP'                        => new EmptyElement(I18N::translate('Coordinates'), ['LATI' => '1:1', 'LONG' => '1:1']),
            '_LOC:MAP:LATI'                   => new PlaceLatitude(I18N::translate('Latitude')),
            '_LOC:MAP:LONG'                   => new PlaceLongtitude(I18N::translate('Longitude')),
            '_LOC:NAME'                       => new PlaceName(I18N::translate('Place'), ['ABBR' => '0:1', 'DATE' => '0:1', 'LANG' => '0:1', 'SOUR' => '0:M']),
            '_LOC:NAME:ABBR'                  => new CustomElement(I18N::translate('Abbreviation')),
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
            '_LOC:SOUR:NOTE'                  => new NoteStructure(I18N::translate('Note')),
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
            '_LOC:*:SOUR:NOTE'                => new NoteStructure(I18N::translate('Note')),
            '_LOC:*:SOUR:OBJE'                => new XrefMedia(I18N::translate('Media object')),
            '_LOC:*:SOUR:PAGE'                => new WhereWithinSource(I18N::translate('Citation details')),
            '_LOC:*:SOUR:QUAY'                => new CertaintyAssessment(I18N::translate('Quality of data')),
        ];
    }

    /**
     * @return array<string,ElementInterface>
     */
    private function geneatique(): array
    {
        return [
            'INDI:DEAT:DATE:TIME' => new TimeValue(I18N::translate('Time of death')),
            'OBJE:FORM'           => new MultimediaFormat(I18N::translate('Format')),
            'OBJE:TITL'           => new DescriptiveTitle(I18N::translate('Title')),
            'INDI:NAME:_AKA'      => new NamePersonal(I18N::translate('Also known as'), []),
            'INDI:NAME:_MARNM'    => new NamePersonal(I18N::translate('Also known as'), []),

            /*
            Pour déclarer les témoins dans les actes de naissance

            Balise GEDCOM non valide. INDI:BIRT:ASSO
            INDI:BIRT:ASSO:TYPE
            INDI:BIRT:ASSO:RELA
            INDI:DEAT:PLAC:QUAY
            INDI:BIRT:OBJE:QUAY
            INDI:BIRT:SOUR:TEXT

            Dans les mariages

            FAM:MARR:ASSO
            FAM:MARR:ASSO:TYPE
            FAM:MARR:ASSO:RELA
            FAM:MARR:WWW:QUAY
            OBJE:WWW
            OBJE:SOUR:TEXTHTTPS
            OBJE:NOTE:WWW
            SOUR:QUAY
            SOUR:TYPE
            */
        ];
    }

    /**
     * @return array<string,ElementInterface>
     */
    private function genPlusWinTags(): array
    {
        return [
            'FAM:*:ADDR:_NUM'         => new CustomElement(I18N::translate('House number')),
            'FAM:*:ADDR:_STRASSE'     => new CustomElement(I18N::translate('Street name')),
            'FAM:*:DATE:_ZUS'         => new CustomElement(I18N::translate('Additional information')),
            'FAM:*:OBJE:_PRIM'        => new CustomElement(I18N::translate('Highlighted image')),
            'FAM:*:PLAC:_AON'         => new CustomElement(I18N::translate('Alternative place name')),
            // I18N: https://foko.genealogy.net
            'FAM:*:PLAC:_FCTRY'       => new CustomElement(I18N::translate('FOKO country')),
            // I18N: https://foko.genealogy.net
            'FAM:*:PLAC:_FSTAE'       => new CustomElement(I18N::translate('FOKO country')),
            // I18N: https://gov.genealogy.net
            'FAM:*:PLAC:_GOV'         => new GovIdentifier(I18N::translate('GOV identifier')),
            // I18N: https://en.wikipedia.org/wiki/Maidenhead_Locator_System
            'FAM:*:PLAC:_MAIDENHEAD'  => new MaidenheadLocator(I18N::translate('Maidenhead location code')),
            'FAM:*:PLAC:_POST'        => new AddressPostalCode(I18N::translate('Postal code')),
            'FAM:*:PLAC:_SIC'         => new CustomElement(I18N::translate('Reliability of the information')),
            'FAM:*:PLAC:_ZUS'         => new CustomElement(I18N::translate('Additional information')),
            'FAM:*:SOUR:_ORI'         => new TextFromSource(I18N::translate('Original text')),
            'FAM:*:SOUR:_ZUS'         => new CustomElement(I18N::translate('Additional information')),
            'FAM:*:SOUR:PAGE:_ZUS'    => new CustomElement(I18N::translate('Additional information')),
            'FAM:*:_ASSO'             => new XrefAssociate(I18N::translate('Associate')),
            'FAM:*:_CERT'             => new CustomElement(I18N::translate('Certificate number')),
            'FAM:*:_COM'              => new CustomElement(I18N::translate('Comment')),
            'FAM:*:_SITE'             => new CustomElement(I18N::translate('Extra information')),
            'FAM:*:_WITN'             => new CustomElement(I18N::translate('Witnesses')),
            'FAM:OBJE:_PRIM'          => new CustomElement(I18N::translate('Highlighted image')),
            'FAM:SOUR:_ORI'           => new TextFromSource(I18N::translate('Original text')),
            'FAM:SOUR:_ZUS'           => new CustomElement(I18N::translate('Additional information')),
            'FAM:SOUR:PAGE:_ZUS'      => new CustomElement(I18N::translate('Additional information')),
            'FAM:_CREAT'              => new DateValue(I18N::translate('Creation date')),
            'FAM:_LIV'                => new CustomElement(I18N::translate('Cohabitation')),
            'FAM:_NAME'               => new CustomElement(I18N::translate('Joint family name')),
            'FAM:_UID'                => new PafUid(I18N::translate('Unique identifier')),
            'INDI:*:ADDR:_NUM'        => new CustomElement(I18N::translate('House number')),
            'INDI:*:ADDR:_STRASSE'    => new CustomElement(I18N::translate('Street name')),
            'INDI:*:DATE:_ZUS'        => new CustomElement(I18N::translate('Additional information')),
            'INDI:*:OBJE:_PRIM'       => new CustomElement(I18N::translate('Highlighted image')),
            'INDI:*:PLAC:_AON'        => new CustomElement(I18N::translate('Alternative place name')),
            // I18N: https://foko.genealogy.net
            'INDI:*:PLAC:_FCTRY'      => new CustomElement(I18N::translate('FOKO country')),
            // I18N: https://foko.genealogy.net
            'INDI:*:PLAC:_FSTAE'      => new CustomElement(I18N::translate('FOKO country')),
            // I18N: https://gov.genealogy.net
            'INDI:*:PLAC:_GOV'        => new GovIdentifier(I18N::translate('GOV identifier')),
            // I18N: https://en.wikipedia.org/wiki/Maidenhead_Locator_System
            'INDI:*:PLAC:_MAIDENHEAD' => new MaidenheadLocator(I18N::translate('Maidenhead location code')),
            'INDI:*:PLAC:_POST'       => new AddressPostalCode(I18N::translate('Postal code')),
            'INDI:*:PLAC:_SIC'        => new CustomElement(I18N::translate('Reliability of the information')),
            'INDI:*:PLAC:_ZUS'        => new CustomElement(I18N::translate('Additional information')),
            'INDI:*:SOUR:_ORI'        => new TextFromSource(I18N::translate('Original text')),
            'INDI:*:SOUR:_ZUS'        => new CustomElement(I18N::translate('Additional information')),
            'INDI:*:SOUR:PAGE:_ZUS'   => new CustomElement(I18N::translate('Additional information')),
            'INDI:*:_ASSO'            => new XrefAssociate(I18N::translate('Associate')),
            'INDI:*:_CERT'            => new CustomElement(I18N::translate('Certificate number')),
            'INDI:*:_COM'             => new CustomElement(I18N::translate('Comment')),
            'INDI:*:_SITE'            => new CustomElement(I18N::translate('Extra information')),
            'INDI:*:_WITN'            => new CustomElement(I18N::translate('Witnesses')),
            'INDI:BAPM:_GODP'         => new CustomElement(I18N::translate('Godparents')),
            'INDI:CHR:_GODP'          => new CustomElement(I18N::translate('Godparents')),
            'INDI:OBJE:_PRIM'         => new CustomElement(I18N::translate('Highlighted image')),
            'INDI:SOUR:_ORI'          => new TextFromSource(I18N::translate('Original text')),
            'INDI:SOUR:_ZUS'          => new CustomElement(I18N::translate('Additional information')),
            'INDI:SOUR:PAGE:_ZUS'     => new CustomElement(I18N::translate('Additional information')),
            'INDI:NAME:_AKA'          => new CustomElement(I18N::translate('Also known as')),
            // https://en.wikipedia.org/wiki/Rufname
            'INDI:NAME:RUFN'          => new CustomElement(I18N::translate('Rufname')),
            'INDI:_CREAT'             => new CustomElement(I18N::translate('Creation date')),
            'INDI:_HEIM'              => new CustomElement(/* I18N: German Bürgerort */ I18N::translate('Place of citizenship')),
            'INDI:_UID'               => new PafUid(I18N::translate('Unique identifier')),
            'NOTE:_CREAT'             => new DateValue(I18N::translate('Creation date')),
            'NOTE:_UID'               => new PafUid(I18N::translate('Unique identifier')),
            'OBJE:_CREAT'             => new DateValue(I18N::translate('Creation date')),
            'OBJE:_UID'               => new PafUid(I18N::translate('Unique identifier')),
            'REPO:_CREAT'             => new DateValue(I18N::translate('Creation date')),
            'REPO:_UID'               => new PafUid(I18N::translate('Unique identifier')),
            'SOUR:_CREAT'             => new DateValue(I18N::translate('Creation date')),
            'SOUR:_KTIT'              => new SourceFiledByEntry(I18N::translate('Abbreviation')),
            'SOUR:_UID'               => new PafUid(I18N::translate('Unique identifier')),
        ];
    }

    /**
     * @return array<string,ElementInterface>
     */
    private function heredis(): array
    {
        return [
            'INDI:SIGN'                   => new CustomElement(I18N::translate('Signature')),
            /* Reported on the forum - but what do they mean?
            'INDI:_FIL'                   => new CustomElement(I18N::translate('???')),
            'INDI:*:_FNA'                 => new CustomElement(I18N::translate('???')),
            'INDI:????:????:_SUBMAP'      => new EmptyElement(I18N::translate('Coordinates'), ['INDI' => '1:1', 'LONG' => '1:1']),
            'INDI:????:????:_SUBMAP:LATI' => new PlaceLatitude(I18N::translate('Latitude')),
            'INDI:????:????:_SUBMAP:LONG' => new PlaceLongtitude(I18N::translate('Longitude')),
            */
        ];
    }

    /**
     * @see http://support.legacyfamilytree.com/article/AA-00520/0/GEDCOM-Files-custom-tags-in-Legacy.html
     *
     * @return array<string,ElementInterface>
     */
    private function legacyTags(): array
    {
        return [
            'FAM:*:ADDR:_PRIV'             => new CustomElement(I18N::translate('Private')),
            'FAM:*:PLAC:_VERI'             => new CustomElement(I18N::translate('Verified')),
            'FAM:*:SOUR:DATE'              => new DateValue(I18N::translate('Date')),
            'FAM:*:SOUR:_VERI'             => new CustomElement(I18N::translate('Verified')),
            'FAM:*:_PRIV'                  => new CustomElement(I18N::translate('Private')),
            'FAM:CHIL:_FREL'               => new CustomElement(I18N::translate('Relationship to father')),
            'FAM:CHIL:_MREL'               => new CustomElement(I18N::translate('Relationship to mother')),
            'FAM:CHIL:_STAT'               => new CustomElement(I18N::translate('Status')),
            'FAM:EVEN:_OVER'               => new CustomElement('Event sentence override'),
            'FAM:MARR:_HTITL'              => new CustomElement(I18N::translate('Label for husband')),
            'FAM:MARR:_RPT_PHRS'           => /* I18N: GEDCOM gag _RPT_PHRS */ new CustomElement(I18N::translate('Report phrase')),
            'FAM:MARR:_RPT_PHRS2'          => /* I18N: GEDCOM gag _RPT_PHRS */ new CustomElement(I18N::translate('Report phrase')),
            'FAM:MARR:_STAT'               => new CustomElement(I18N::translate('Status')),
            'FAM:MARR:_WTITL'              => new CustomElement(I18N::translate('Label for wife')),
            'FAM:_NONE'                    => new CustomElement(I18N::translate('No children')),
            'FAM:_TAG'                     => new CustomElement('Tag'),
            'FAM:_TAG2'                    => new CustomElement('Tag #2'),
            'FAM:_TAG3'                    => new CustomElement('Tag #3'),
            'FAM:_TAG4'                    => new CustomElement('Tag #4'),
            'FAM:_TAG5'                    => new CustomElement('Tag #5'),
            'FAM:_TAG6'                    => new CustomElement('Tag #6'),
            'FAM:_TAG7'                    => new CustomElement('Tag #7'),
            'FAM:_TAG8'                    => new CustomElement('Tag #8'),
            'FAM:_TAG9'                    => new CustomElement('Tag #9'),
            'FAM:_UID'                     => new PafUid(I18N::translate('Unique identifier')),
            'HEAD:_EVENT_DEFN'             => new CustomElement('Event definition'),
            'HEAD:_EVENT_DEFN:_CONF_FLAG'  => new CustomElement(I18N::translate('Private')),
            'HEAD:_EVENT_DEFN:_DATE_TYPE'  => new CustomElement(I18N::translate('Date')),
            'HEAD:_EVENT_DEFN:_DESC_FLAG'  => new CustomElement(I18N::translate('Description')),
            'HEAD:_EVENT_DEFN:_PLACE_TYPE' => new CustomElement(I18N::translate('Place')),
            'HEAD:_EVENT_DEFN:_PP_EXCLUDE' => new CustomElement('Exclude event from potential problems report'),
            'HEAD:_EVENT_DEFN:_SEN1'       => new CustomElement('Event sentence definition'),
            'HEAD:_EVENT_DEFN:_SEN2'       => new CustomElement('Event sentence definition'),
            'HEAD:_EVENT_DEFN:_SEN3'       => new CustomElement('Event sentence definition'),
            'HEAD:_EVENT_DEFN:_SEN4'       => new CustomElement('Event sentence definition'),
            'HEAD:_EVENT_DEFN:_SEN5'       => new CustomElement('Event sentence definition'),
            'HEAD:_EVENT_DEFN:_SEN6'       => new CustomElement('Event sentence definition'),
            'HEAD:_EVENT_DEFN:_SEN7'       => new CustomElement('Event sentence definition'),
            'HEAD:_EVENT_DEFN:_SEN8'       => new CustomElement('Event sentence definition'),
            'HEAD:_EVENT_DEFN:_SENDOF'     => new CustomElement('Event sentence, female, date only'),
            'HEAD:_EVENT_DEFN:_SENDOM'     => new CustomElement('Event sentence, male, date only'),
            'HEAD:_EVENT_DEFN:_SENDOU'     => new CustomElement('Event sentence, unknown sex, date only'),
            'HEAD:_EVENT_DEFN:_SENDPF'     => new CustomElement('Event sentence, female, date and place'),
            'HEAD:_EVENT_DEFN:_SENDPM'     => new CustomElement('Event sentence, male, date and place'),
            'HEAD:_EVENT_DEFN:_SENDPU'     => new CustomElement('Event sentence, unknown sex, date and place'),
            'HEAD:_EVENT_DEFN:_SENF'       => new CustomElement('Event sentence, female'),
            'HEAD:_EVENT_DEFN:_SENM'       => new CustomElement('Event sentence, male'),
            'HEAD:_EVENT_DEFN:_SENPOF'     => new CustomElement('Event sentence, unknown sex'),
            'HEAD:_EVENT_DEFN:_SENPOM'     => new CustomElement('Event sentence, female, place only'),
            'HEAD:_EVENT_DEFN:_SENPOU'     => new CustomElement('Event sentence, male, place only'),
            'HEAD:_EVENT_DEFN:_SENU'       => new CustomElement('Event sentence, unknown sex, place only'),
            'HEAD:_PLAC_DEFN'              => new CustomElement('Place definition'),
            'HEAD:_PLAC_DEFN:_PREP'        => new CustomElement('Place preposition'),
            'INDI:*:ADDR:_EMAIL'           => new CustomElement(I18N::translate('Email')),
            'INDI:*:ADDR:_LIST1'           => new CustomElement('Include in the “newsletter” group'),
            'INDI:*:ADDR:_LIST2'           => new CustomElement('Include in the “family association” group'),
            'INDI:*:ADDR:_LIST3'           => new CustomElement('Include in the “birthday” group'),
            'INDI:*:ADDR:_LIST4'           => new CustomElement('Include in the “research” group'),
            'INDI:*:ADDR:_LIST5'           => new CustomElement('Include in the “christmas” group'),
            'INDI:*:ADDR:_LIST6'           => new CustomElement('Include in the “holiday” group'),
            'INDI:*:ADDR:_NAME'            => new CustomElement(I18N::translate('Name of addressee')),
            'INDI:*:ADDR:_PRIV'            => new CustomElement(I18N::translate('Private')),
            'INDI:*:ADDR:_SORT'            => new CustomElement('The spelling of a name to be used when sorting addresses for a report'),
            'INDI:*:ADDR:_TAG'             => new CustomElement('Tag'),
            'INDI:*:PLAC:_TAG'             => new CustomElement('Tag'),
            'INDI:*:PLAC:_VERI'            => new CustomElement(I18N::translate('Verified')),
            'INDI:*:SOUR:DATE'             => new DateValue(I18N::translate('Date')),
            'INDI:*:SOUR:_VERI'            => new CustomElement(I18N::translate('Verified')),
            'INDI:*:_PRIV'                 => new CustomElement(I18N::translate('Private')),
            'INDI:EVEN:_OVER'              => new CustomElement('Event sentence override'),
            'INDI:SOUR:_VERI'              => new CustomElement(I18N::translate('Verified')),
            'INDI:_TAG'                    => new CustomElement('Tag'),
            'INDI:_TAG2'                   => new CustomElement('Tag #2'),
            'INDI:_TAG3'                   => new CustomElement('Tag #3'),
            'INDI:_TAG4'                   => new CustomElement('Tag #4'),
            'INDI:_TAG5'                   => new CustomElement('Tag #5'),
            'INDI:_TAG6'                   => new CustomElement('Tag #6'),
            'INDI:_TAG7'                   => new CustomElement('Tag #7'),
            'INDI:_TAG8'                   => new CustomElement('Tag #8'),
            'INDI:_TAG9'                   => new CustomElement('Tag #9'),
            'INDI:_TODO'                   => new CustomElement(I18N::translate('Research task')),
            'INDI:_TODO:PRTY'              => new CustomElement(I18N::translate('Priority')),
            'INDI:_TODO:_CAT'              => new CustomElement(I18N::translate('Category')),
            'INDI:_TODO:_CDATE'            => new CustomElement(I18N::translate('Completion date')),
            'INDI:_TODO:_LOCL'             => new CustomElement(I18N::translate('Location')),
            'INDI:_TODO:_RDATE'            => new CustomElement(I18N::translate('Reminder date')),
            'INDI:_UID'                    => new PafUid(I18N::translate('Unique identifier')),
            'INDI:_URL'                    => new AddressWebPage(I18N::translate('URL')),
            'OBJE:_DATE'                   => new CustomElement(I18N::translate('Date')),
            'OBJE:_PRIM'                   => new CustomElement(I18N::translate('Highlighted image')),
            'OBJE:_SCBK'                   => new CustomElement(I18N::translate('Scrapbook')),
            'OBJE:_SOUND'                  => new CustomElement(I18N::translate('Audio')),
            'OBJE:_TYPE'                   => new CustomElement(I18N::translate('Type')),
            'OBJE:_UID'                    => new PafUid(I18N::translate('Unique identifier')),
            'REPO:_UID'                    => new PafUid(I18N::translate('Unique identifier')),
            'SOUR:_ITALIC'                 => new CustomElement('The source title should be printed in italic on reports'),
            'SOUR:_PAREN'                  => new CustomElement('The source title should be printed within parentheses on reports'),
            'SOUR:_QUOTED'                 => new CustomElement('The source title should be printed within quotes on reports'),
            'SOUR:_TAG'                    => new CustomElement('Exclude the source citation detail on reports'),
            'SOUR:_TAG2'                   => new CustomElement('Exclude the source citation on reports'),
            'SOUR:_TAG3'                   => new CustomElement('Include the source citation detail text on reports'),
            'SOUR:_TAG4'                   => new CustomElement('Include the source citation detail notes on reports'),
            'SOUR:_UID'                    => new PafUid(I18N::translate('Unique identifier')),
        ];
    }

    /**
     * @return array<string,ElementInterface>
     */
    private function myHeritageTags(): array
    {
        return [
            'FAM:*:_UID'                  => new PafUid(I18N::translate('Unique identifier')),
            'FAM:*:RIN'                   => new AutomatedRecordId(I18N::translate('Record ID number')),
            'HEAD:DATE:_TIMEZONE'         => new CustomElement(I18N::translate('Time zone')),
            'HEAD:SOUR:_RTLSAVE'          => new CustomElement(I18N::translate('Text direction')), // ?
            'HEAD:_RINS'                  => new CustomElement(I18N::translate('Record ID number')), // ?
            'HEAD:_UID'                   => new PafUid(I18N::translate('Unique identifier')),
            'HEAD:_PROJECT_GUID'          => new PafUid(I18N::translate('Unique identifier')),
            'HEAD:_EXPORTED_FROM_SITE_ID' => new CustomElement(I18N::translate('Site identification code')),
            'HEAD:_DESCRIPTION_AWARE'     => new CustomElement(I18N::translate('Description')), // ?
            'INDI:*:_UID'                 => new PafUid(I18N::translate('Unique identifier')),
            'INDI:*:RIN'                  => new AutomatedRecordId(I18N::translate('Record ID number')),
            '*:_UPD'                      => new CustomElement(I18N::translate('Updated at')),
        ];
    }

    /**
     * @return array<string,ElementInterface>
     */
    private function personalAncestralFileTags(): array
    {
        return [
            'FAM:_UID'        => new PafUid(I18N::translate('Unique identifier')),
            'INDI:NAME:_ADPN' => new NamePersonal(I18N::translate('Adopted name'), []),
            'INDI:NAME:_AKA'  => new NamePersonal(I18N::translate('Also known as'), []),
            'INDI:NAME:_AKAN' => new NamePersonal(I18N::translate('Also known as'), []),
            'INDI:ADDR'       => new AddressLine(I18N::translate('Address')),
            'INDI:ADDR:ADR1'  => new AddressLine1(I18N::translate('Address line 1')),
            'INDI:ADDR:ADR2'  => new AddressLine2(I18N::translate('Address line 2')),
            'INDI:ADDR:CITY'  => new AddressCity(I18N::translate('City')),
            'INDI:ADDR:CTRY'  => new AddressCountry(I18N::translate('Country')),
            'INDI:ADDR:POST'  => new AddressPostalCode(I18N::translate('Postal code')),
            'INDI:ADDR:STAE'  => new AddressState(I18N::translate('State')),
            'INDI:ADDR:_NAME' => new CustomElement(I18N::translate('Name of addressee')),
            'INDI:EMAIL'      => new AddressEmail(I18N::translate('Email address')),
            'INDI:FAX'        => new AddressFax(I18N::translate('Fax')),
            'INDI:PHON'       => new PhoneNumber(I18N::translate('Phone')),
            'INDI:URL'        => new CustomElement(I18N::translate('URL')),
            'INDI:_UID'       => new PafUid(I18N::translate('Unique identifier')),
            'OBJE:_UID'       => new PafUid(I18N::translate('Unique identifier')),
            'REPO:_UID'       => new PafUid(I18N::translate('Unique identifier')),
            'SOUR:_UID'       => new PafUid(I18N::translate('Unique identifier')),
        ];
    }

    /**
     * @return array<string,ElementInterface>
     */
    private function phpGedViewTags(): array
    {
        return [
            'FAM:CHAN:_PGVU'        => new WebtreesUser(I18N::translate('Author of last change')),
            'FAM:COMM'              => new CustomElement(I18N::translate('Comment')),
            'INDI:*:ASSO'           => new XrefAssociate(I18N::translate('Associate')),
            'INDI:*:ASSO:RELA'      => new RelationIsDescriptor(I18N::translate('Relationship')),
            'INDI:*:PLAC:_HEB'      => new NoteStructure(I18N::translate('Place in Hebrew')),
            'INDI:BURI:CEME'        => new CustomElement(I18N::translate('Cemetery')),
            'INDI:CHAN:_PGVU'       => new WebtreesUser(I18N::translate('Author of last change')),
            'INDI:COMM'             => new CustomElement(I18N::translate('Comment')),
            'INDI:NAME:_HEB'        => new NamePersonal(I18N::translate('Name in Hebrew'), []),
            'INDI:_HOL'             => new CustomIndividualEvent(I18N::translate('Holocaust')),
            'INDI:_MILI'            => new CustomIndividualEvent(I18N::translate('Military')),
            'INDI:_PGV_OBJS'        => new XrefMedia(I18N::translate('Re-order media')),
            'NOTE:CHAN:_PGVU'       => new WebtreesUser(I18N::translate('Author of last change')),
            'OBJE:CHAN:_PGVU'       => new WebtreesUser(I18N::translate('Author of last change')),
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
     * @return array<string,ElementInterface>
     */
    private function reunionTags(): array
    {
        return [
            'FAM:_UID'   => new PafUid(I18N::translate('Unique identifier')),
            'INDI:CITN'  => new CustomElement(I18N::translate('Citizenship')),
            'INDI:EMAL'  => new AddressEmail(I18N::translate('Email address')),
            'INDI:_LEGA' => new CustomElement(I18N::translate('Legatee')),
            'INDI:_MDCL' => new CustomElement(I18N::translate('Medical')),
            'INDI:_PURC' => /* I18N: GEDCOM tag _PURC */ new CustomElement(I18N::translate('Land purchase')),
            'INDI:_SALE' => /* I18N: GEDCOM tag _SALE */ new CustomElement(I18N::translate('Land sale')),
            'INDI:_UID'  => new PafUid(I18N::translate('Unique identifier')),
            'OBJE:_UID'  => new PafUid(I18N::translate('Unique identifier')),
            'REPO:_UID'  => new PafUid(I18N::translate('Unique identifier')),
            'SOUR:_UID'  => new PafUid(I18N::translate('Unique identifier')),
        ];
    }

    /**
     * @return array<string,ElementInterface>
     */
    private function rootsMagicTags(): array
    {
        return [
            'FAM:_UID'          => new PafUid(I18N::translate('Unique identifier')),
            'INDI:_DNA'         => new CustomElement(I18N::translate('DNA markers')),
            'INDI:_UID'         => new PafUid(I18N::translate('Unique identifier')),
            'INDI:_WEBTAG'      => new CustomElement(I18N::translate('External link')),
            'INDI:_WEBTAG:NAME' => new CustomElement(I18N::translate('Text')),
            'INDI:_WEBTAG:URL'  => new AddressWebPage(I18N::translate('URL')),
            'OBJE:_UID'         => new PafUid(I18N::translate('Unique identifier')),
            'REPO:_UID'         => new PafUid(I18N::translate('Unique identifier')),
            'SOUR:_BIBL'        => new CustomElement(I18N::translate('Bibliography')),
            'SOUR:_SUBQ'        => new CustomElement(I18N::translate('Abbreviation')),
            'SOUR:_UID'         => new PafUid(I18N::translate('Unique identifier')),
        ];
    }

    /**
     * @return array<string,ElementInterface>
     */
    private function theMasterGenealogistTags(): array
    {
        return [
            'INDI:*:_SDATE' => new DateValue(I18N::translate('Sort date')),
            'INDI:NAME:_DATE'  => new DateValue(I18N::translate('Date')),
        ];
    }

    /**
     * Custom tags for webtrees.
     *
     * @return array<string,ElementInterface>
     */
    private function webtreesTags(): array
    {
        return [
            'FAM:CHAN:_WT_USER'           => new WebtreesUser(I18N::translate('Author of last change')),
            'FAM:FACT'                    => new FamilyFact(I18N::translate('Fact')),
            'FAM:FACT:TYPE'               => new EventOrFactClassification(I18N::translate('Type of fact')),
            'FAM:*:_ASSO'                 => new XrefAssociate(I18N::translate('Associate')),
            'FAM:*:_ASSO:NOTE'            => new NoteStructure(I18N::translate('Note')),
            'FAM:*:_ASSO:RELA'            => new RelationIsDescriptor(I18N::translate('Relationship')),
            'FAM:*:_ASSO:SOUR'            => new XrefSource(I18N::translate('Source citation')),
            'FAM:*:_ASSO:SOUR:DATA'       => new SourceData(I18N::translate('Data')),
            'FAM:*:_ASSO:SOUR:DATA:DATE'  => new DateValue(I18N::translate('Date of entry in original source')),
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
            'INDI:*:_ASSO:SOUR:DATA:DATE' => new DateValue(I18N::translate('Date of entry in original source')),
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
    private function webtreesSubTags(): array
    {
        return [
            'FAM'              => [['_UID', '0:M']],
            'FAM:*:SOUR:DATA'  => [['TEXT', '0:1']],
            'FAM:ANUL'         => [['_ASSO', '0:M', 'NOTE']],
            'FAM:CENS'         => [['_ASSO', '0:M', 'NOTE']],
            'FAM:CHAN'         => [['_WT_USER', '0:1']],
            'FAM:DIV'          => [['_ASSO', '0:M', 'NOTE']],
            'FAM:DIVF'         => [['_ASSO', '0:M', 'NOTE']],
            'FAM:ENGA'         => [['_ASSO', '0:M', 'NOTE']],
            'FAM:EVEN'         => [['_ASSO', '0:M', 'NOTE']],
            'FAM:MARB'         => [['_ASSO', '0:M', 'NOTE']],
            'FAM:MARC'         => [['_ASSO', '0:M', 'NOTE']],
            'FAM:MARL'         => [['_ASSO', '0:M', 'NOTE']],
            'FAM:MARR'         => [['_ASSO', '2:M', 'NOTE']],
            'FAM:MARS'         => [['_ASSO', '0:M', 'NOTE']],
            'FAM:SLGS'         => [['_ASSO', '0:M', 'NOTE']],
            'FAM:SOUR:DATA'    => [['TEXT', '0:1']],
            'INDI'             => [['_UID', '0:M']],
            'INDI:*:SOUR:DATA' => [['TEXT', '0:1']],
            'INDI:ADOP'        => [['_ASSO', '0:M', 'NOTE']],
            'INDI:BAPL'        => [['_ASSO', '0:M', 'NOTE']],
            'INDI:BAPM'        => [['_ASSO', '2:M', 'NOTE']],
            'INDI:BARM'        => [['_ASSO', '0:M', 'NOTE']],
            'INDI:BASM'        => [['_ASSO', '0:M', 'NOTE']],
            'INDI:BIRT'        => [['_ASSO', '0:M', 'NOTE'], ['FAMC', '0:0']],
            'INDI:BURI'        => [['_ASSO', '0:M', 'NOTE']],
            'INDI:CENS'        => [['_ASSO', '0:M', 'NOTE']],
            'INDI:CHAN'        => [['_WT_USER', '0:1']],
            'INDI:CHR'         => [['_ASSO', '2:M', 'NOTE']],
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
            'INDI:OCCU'        => [['_ASSO', '0:M', 'NOTE']],
            'INDI:ORDN'        => [['_ASSO', '0:M', 'NOTE']],
            'INDI:PROB'        => [['_ASSO', '0:M', 'NOTE']],
            'INDI:PROP'        => [['_ASSO', '0:M', 'NOTE']],
            'INDI:RESI'        => [['_ASSO', '0:M', 'NOTE']],
            'INDI:RETI'        => [['_ASSO', '0:M', 'NOTE']],
            'INDI:SLGC'        => [['_ASSO', '0:M', 'NOTE']],
            'INDI:SOUR:DATA'   => [['TEXT', '0:1']],
            'INDI:TITL'        => [['_ASSO', '0:M', 'NOTE']],
            'INDI:WILL'        => [['_ASSO', '0:M', 'NOTE']],
            'NOTE'             => [['RESN', '0:1', 'CHAN']],
            'NOTE:CHAN'        => [['_WT_USER', '0:1']],
            'NOTE:SOUR:DATA'   => [['TEXT', '0:1']],
            'OBJE'             => [['RESN', '0:1', 'CHAN'], ['_UID', '0:M']],
            'OBJE:CHAN'        => [['_WT_USER', '0:1']],
            'OBJE:SOUR:DATA'   => [['TEXT', '0:1']],
            'REPO'             => [['RESN', '0:1', 'CHAN'], ['_UID', '0:M']],
            'REPO:CHAN'        => [['_WT_USER', '0:1']],
            'SOUR'             => [['RESN', '0:1', 'CHAN'], ['_UID', '0:M']],
            'SOUR:CHAN'        => [['_WT_USER', '0:1']],
            'SUBM'             => [['RESN', '0:1', 'CHAN']],
            'SUBM:CHAN'        => [['_WT_USER', '0:1']],
        ];
    }

    /**
     * @return array<string,array<int,array<int,string>>>
     */
    private function customSubTags(): array
    {
        $custom_family_tags     = array_filter(explode(',', Site::getPreference('CUSTOM_FAMILY_TAGS')));
        $custom_individual_tags = array_filter(explode(',', Site::getPreference('CUSTOM_INDIVIDUAL_TAGS')));

        $subtags = [
            'FAM'  => array_map(static fn (string $tag): array => [$tag, '0:M'], $custom_family_tags),
            'INDI' => array_map(static fn (string $tag): array => [$tag, '0:M'], $custom_individual_tags),
        ];

        if (Site::getPreference('CUSTOM_TIME_TAGS') === '1') {
            $subtags['INDI:BIRT:DATE'][] = ['TIME', '0:1'];
            $subtags['INDI:DEAT:DATE'][] = ['TIME', '0:1'];
        }

        if (Site::getPreference('CUSTOM_GEDCOM_L_TAGS') === '1') {
            $subtags['FAM'][]               = ['_ASSO', '0:M'];
            $subtags['FAM'][]               = ['_STAT', '0:1'];
            $subtags['FAM'][]               = ['_UID', '0:M'];
            $subtags['FAM:*:ADDR']          = [['_NAME', '0:1:?', 'ADR1']];
            $subtags['FAM:*:PLAC']          = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1']];
            $subtags['FAM:ENGA:PLAC']       = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1']];
            $subtags['FAM:MARB:PLAC']       = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1']];
            $subtags['FAM:MARR']            = [['_WITN', '0:1']];
            $subtags['FAM:MARR:PLAC']       = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1']];
            $subtags['FAM:SLGS:PLAC']       = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1']];
            $subtags['INDI'][]              = ['_UID', '0:M'];
            $subtags['INDI:*:ADDR']         = [['_NAME', '0:1:?', 'ADR1']];
            $subtags['INDI:*:PLAC']         = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1']];
            $subtags['INDI:ADOP:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1']];
            $subtags['INDI:BAPL:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1']];
            $subtags['INDI:BAPM']           = [['_GODP', '0:1'], ['_WITN', '0:1']];
            $subtags['INDI:BAPM:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1']];
            $subtags['INDI:BARM:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1']];
            $subtags['INDI:BASM:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1']];
            $subtags['INDI:BIRT:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1']];
            $subtags['INDI:BLES:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1']];
            $subtags['INDI:BURI:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1']];
            $subtags['INDI:CENS:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1']];
            $subtags['INDI:CHR']            = [['_GODP', '0:1'], ['_WITN', '0:1']];
            $subtags['INDI:CHR:PLAC']       = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1']];
            $subtags['INDI:CHRA:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1']];
            $subtags['INDI:CONF:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1']];
            $subtags['INDI:CONL:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1']];
            $subtags['INDI:CREM:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1']];
            $subtags['INDI:DEAT:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1']];
            $subtags['INDI:EMIG:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1']];
            $subtags['INDI:ENDL:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1']];
            $subtags['INDI:EVEN:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1']];
            $subtags['INDI:FCOM:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1']];
            $subtags['INDI:IMMI:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1']];
            $subtags['INDI:NAME']           = [['_RUFN', '0:1']];
            $subtags['INDI:NATU:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1']];
            $subtags['INDI:ORDN:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1']];
            $subtags['INDI:RESI:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1']];
            $subtags['INDI:SLGC:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1']];
            $subtags['NOTE']                = [['_UID', '0:M']];
            $subtags['OBJE']                = [['_PRIM', '0:1:?'], ['_UID', '0:M']];
            $subtags['REPO']                = [['_UID', '0:M']];
            $subtags['REPO:ADDR']           = [['_NAME', '0:1', 'ADR1']];
            $subtags['SOUR']                = [['_UID', '0:M']];
            $subtags['SOUR:DATA:EVEN:PLAC'] = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1']];
            $subtags['SUBM']                = [['_UID', '0:M']];
            $subtags['SUBM:ADDR']           = [['_NAME', '0:1', 'ADR1']];
        }

        return $subtags;
    }

    /**
     * @param ElementFactoryInterface $element_factory
     * @param bool                    $include_custom_tags
     *
     * @return void
     */
    public function registerTags(ElementFactoryInterface $element_factory, bool $include_custom_tags): void
    {
        // Standard GEDCOM.
        $element_factory->registerTags($this->gedcom551Tags());

        // webtrees extensions.
        $element_factory->registerTags($this->webtreesTags());

        if ($include_custom_tags) {
            // webtrees extensions.
            $element_factory->registerSubTags($this->webtreesSubTags());

            // Third-party extensions.
            $element_factory->registerTags($this->aldfaerTags());
            $element_factory->registerTags($this->ancestryTags());
            $element_factory->registerTags($this->brothersKeeperTags());
            $element_factory->registerTags($this->familySearchTags());
            $element_factory->registerTags($this->familyTreeBuilderTags());
            $element_factory->registerTags($this->familyTreeMakerTags());
            $element_factory->registerTags($this->gedcom7Tags());
            $element_factory->registerTags($this->gedcomLTags());
            $element_factory->registerTags($this->geneatique());
            $element_factory->registerTags($this->genPlusWinTags());
            $element_factory->registerTags($this->heredis());
            $element_factory->registerTags($this->legacyTags());
            $element_factory->registerTags($this->myHeritageTags());
            $element_factory->registerTags($this->personalAncestralFileTags());
            $element_factory->registerTags($this->phpGedViewTags());
            $element_factory->registerTags($this->reunionTags());
            $element_factory->registerTags($this->rootsMagicTags());
            $element_factory->registerTags($this->theMasterGenealogistTags());

            // Creating tags from all the above are grouped into one place
            $element_factory->registerSubTags($this->customSubTags());
        }
    }
}
