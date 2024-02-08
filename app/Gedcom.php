<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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
use Fisharebest\Webtrees\CustomTags\Aldfaer;
use Fisharebest\Webtrees\CustomTags\Ancestry;
use Fisharebest\Webtrees\CustomTags\BrothersKeeper;
use Fisharebest\Webtrees\CustomTags\FamilySearch;
use Fisharebest\Webtrees\CustomTags\FamilyTreeBuilder;
use Fisharebest\Webtrees\CustomTags\FamilyTreeMaker;
use Fisharebest\Webtrees\CustomTags\Gedcom7;
use Fisharebest\Webtrees\CustomTags\GedcomL;
use Fisharebest\Webtrees\CustomTags\Geneatique;
use Fisharebest\Webtrees\CustomTags\GenPlusWin;
use Fisharebest\Webtrees\CustomTags\Heredis;
use Fisharebest\Webtrees\CustomTags\Legacy;
use Fisharebest\Webtrees\CustomTags\MyHeritage;
use Fisharebest\Webtrees\CustomTags\PersonalAncestralFile;
use Fisharebest\Webtrees\CustomTags\PhpGedView;
use Fisharebest\Webtrees\CustomTags\ProGen;
use Fisharebest\Webtrees\CustomTags\Reunion;
use Fisharebest\Webtrees\CustomTags\RootsMagic;
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
use Fisharebest\Webtrees\Elements\CountOfChildrenFam;
use Fisharebest\Webtrees\Elements\CountOfMarriages;
use Fisharebest\Webtrees\Elements\Cremation;
use Fisharebest\Webtrees\Elements\CustomElement;
use Fisharebest\Webtrees\Elements\DateLdsOrd;
use Fisharebest\Webtrees\Elements\DateValue;
use Fisharebest\Webtrees\Elements\DateValueExact;
use Fisharebest\Webtrees\Elements\Death;
use Fisharebest\Webtrees\Elements\DescriptiveTitle;
use Fisharebest\Webtrees\Elements\Divorce;
use Fisharebest\Webtrees\Elements\DivorceFiled;
use Fisharebest\Webtrees\Elements\Emigration;
use Fisharebest\Webtrees\Elements\EmptyElement;
use Fisharebest\Webtrees\Elements\Engagement;
use Fisharebest\Webtrees\Elements\EventOrFactClassification;
use Fisharebest\Webtrees\Elements\EventsRecorded;
use Fisharebest\Webtrees\Elements\EventTypeCitedFrom;
use Fisharebest\Webtrees\Elements\FamilyCensus;
use Fisharebest\Webtrees\Elements\FamilyEvent;
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
use Fisharebest\Webtrees\Elements\NationalOrTribalOrigin;
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
use Fisharebest\Webtrees\Elements\ResidenceWithValue;
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
use Fisharebest\Webtrees\Elements\TimeValueNow;
use Fisharebest\Webtrees\Elements\TransmissionDate;
use Fisharebest\Webtrees\Elements\UserReferenceNumber;
use Fisharebest\Webtrees\Elements\UserReferenceType;
use Fisharebest\Webtrees\Elements\VersionNumber;
use Fisharebest\Webtrees\Elements\WebtreesUser;
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
    '_SP_DEAT',  // only used internally, not exported to a gedcom file
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
            'FAM:*:PLAC:NOTE'            => new NoteStructure(I18N::translate('Note on place')),
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
            'FAM:*:SOUR:NOTE'            => new NoteStructure(I18N::translate('Note on source citation')),
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
            'FAM:CHAN:DATE:TIME'         => new TimeValueNow(I18N::translate('Time of last change')),
            'FAM:CHAN:NOTE'              => new NoteStructure(I18N::translate('Note on last change')),
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
            'FAM:NCHI'                   => new CountOfChildrenFam(I18N::translate('Number of children')),
            'FAM:NOTE'                   => new NoteStructure(I18N::translate('Note')),
            'FAM:OBJE'                   => new XrefMedia(I18N::translate('Media object')),
            'FAM:REFN'                   => new UserReferenceNumber(I18N::translate('Reference number')),
            'FAM:REFN:TYPE'              => new UserReferenceType(I18N::translate('Type of reference number')),
            'FAM:RESI'                   => new Residence(I18N::translate('Family residence')),
            'FAM:RESN'                   => new RestrictionNotice(I18N::translate('Restriction')),
            'FAM:RIN'                    => new AutomatedRecordId(I18N::translate('Record ID number')),
            'FAM:SLGS'                   => new LdsSpouseSealing(I18N::translate('LDS spouse sealing')),
            'FAM:SLGS:DATE'              => new DateLdsOrd(I18N::translate('Date of LDS spouse sealing')),
            'FAM:SLGS:PLAC'              => new PlaceLivingOrdinance(I18N::translate('Place of LDS spouse sealing')),
            'FAM:SLGS:STAT'              => new LdsSpouseSealingDateStatus(I18N::translate('Status')),
            'FAM:SLGS:STAT:DATE'         => new DateValueExact(I18N::translate('Status change date')),
            'FAM:SLGS:TEMP'              => new TempleCode(I18N::translate('Temple')),
            'FAM:SOUR'                   => new XrefSource(I18N::translate('Source citation')),
            'FAM:SOUR:DATA'              => new SourceData(I18N::translate('Data')),
            'FAM:SOUR:DATA:DATE'         => new DateValue(I18N::translate('Date of entry in original source')),
            'FAM:SOUR:DATA:TEXT'         => new TextFromSource(I18N::translate('Text')),
            'FAM:SOUR:EVEN'              => new EventTypeCitedFrom(I18N::translate('Event')),
            'FAM:SOUR:EVEN:ROLE'         => new RoleInEvent(I18N::translate('Role')),
            'FAM:SOUR:NOTE'              => new NoteStructure(I18N::translate('Note on source citation')),
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
            'HEAD:DATE:TIME'             => new TimeValueNow(I18N::translate('Time')),
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
            'INDI:*:PLAC:NOTE'           => new NoteStructure(I18N::translate('Note on place')),
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
            'INDI:*:SOUR:NOTE'           => new NoteStructure(I18N::translate('Note on source citation')),
            'INDI:*:SOUR:OBJE'           => new XrefMedia(I18N::translate('Media object')),
            'INDI:*:SOUR:PAGE'           => new WhereWithinSource(I18N::translate('Citation details')),
            'INDI:*:SOUR:QUAY'           => new CertaintyAssessment(I18N::translate('Quality of data')),
            'INDI:*:TYPE'                => new EventOrFactClassification(I18N::translate('Type')),
            'INDI:*:WWW'                 => new AddressWebPage(I18N::translate('URL')),
            'INDI:ADOP'                  => new Adoption(I18N::translate('Adoption')),
            'INDI:ADOP:DATE'             => new DateValue(I18N::translate('Date of adoption')),
            'INDI:ADOP:FAMC'             => new XrefFamily(I18N::translate('Adoptive parents'), ['ADOP' => '0:1']),
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
            'INDI:BAPL:STAT:DATE'        => new DateValueExact(I18N::translate('Status change date')),
            'INDI:BAPL:TEMP'             => new TempleCode(I18N::translate('Temple')),
            'INDI:BAPM'                  => new Baptism(I18N::translate('Baptism')),
            'INDI:BAPM:DATE'             => new DateValue(I18N::translate('Date of baptism')),
            'INDI:BAPM:PLAC'             => new PlaceName(I18N::translate('Place of baptism')),
            'INDI:BARM'                  => new BarMitzvah(I18N::translate('Bar mitzvah')),
            'INDI:BARM:DATE'             => new DateValue(I18N::translate('Date of bar mitzvah')),
            'INDI:BARM:PLAC'             => new PlaceName(I18N::translate('Place of bar mitzvah')),
            'INDI:BASM'                  => new BasMitzvah(I18N::translate('Bat mitzvah')),
            'INDI:BASM:DATE'             => new DateValue(I18N::translate('Date of bat mitzvah')),
            'INDI:BASM:PLAC'             => new PlaceName(I18N::translate('Place of bat mitzvah')),
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
            'INDI:CHAN:DATE:TIME'        => new TimeValueNow(I18N::translate('Time of last change')),
            'INDI:CHAN:NOTE'             => new NoteStructure(I18N::translate('Note on last change')),
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
            'INDI:CONL:STAT:DATE'        => new DateValueExact(I18N::translate('Status change date')),
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
            'INDI:ENDL:STAT:DATE'        => new DateValueExact(I18N::translate('Status change date')),
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
            'INDI:NAME:*:SOUR:NOTE'      => new NoteStructure(I18N::translate('Note on source citation')),
            'INDI:NAME:*:SOUR:OBJE'      => new XrefMedia(I18N::translate('Media object')),
            'INDI:NAME:*:SOUR:PAGE'      => new WhereWithinSource(I18N::translate('Citation details')),
            'INDI:NAME:*:SOUR:QUAY'      => new CertaintyAssessment(I18N::translate('Quality of data')),
            'INDI:NAME:FONE'             => new NamePhoneticVariation(I18N::translate('Phonetic name')),
            'INDI:NAME:FONE:GIVN'        => new NamePieceGiven(I18N::translate('Given names')),
            'INDI:NAME:FONE:NICK'        => new NamePieceNickname(I18N::translate('Nickname')),
            'INDI:NAME:FONE:NOTE'        => new NoteStructure(I18N::translate('Note on phonetic name')),
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
            'INDI:NAME:ROMN:NOTE'        => new NoteStructure(I18N::translate('Note on romanized name')),
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
            'INDI:ORDN:AGNC'             => new ResponsibleAgency(I18N::translate('Religious institution')),
            'INDI:ORDN:DATE'             => new DateValue(I18N::translate('Date of ordination')),
            'INDI:ORDN:PLAC'             => new PlaceName(I18N::translate('Place of ordination')),
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
            'INDI:SLGC:STAT:DATE'        => new DateValueExact(I18N::translate('Status change date')),
            'INDI:SLGC:TEMP'             => new TempleCode(I18N::translate('Temple')),
            'INDI:SOUR'                  => new XrefSource(I18N::translate('Source citation')),
            'INDI:SOUR:DATA'             => new SourceData(I18N::translate('Data')),
            'INDI:SOUR:DATA:DATE'        => new DateValue(I18N::translate('Date of entry in original source')),
            'INDI:SOUR:DATA:TEXT'        => new TextFromSource(I18N::translate('Text')),
            'INDI:SOUR:EVEN'             => new EventTypeCitedFrom(I18N::translate('Event')),
            'INDI:SOUR:EVEN:ROLE'        => new RoleInEvent(I18N::translate('Role')),
            'INDI:SOUR:NOTE'             => new NoteStructure(I18N::translate('Note on source citation')),
            'INDI:SOUR:OBJE'             => new XrefMedia(I18N::translate('Media object')),
            'INDI:SOUR:PAGE'             => new WhereWithinSource(I18N::translate('Citation details')),
            'INDI:SOUR:QUAY'             => new CertaintyAssessment(I18N::translate('Quality of data')),
            'INDI:SSN'                   => new SocialSecurityNumber(I18N::translate('Social security number')),
            'INDI:SUBM'                  => new XrefSubmitter(I18N::translate('Submitter')),
            'INDI:TITL'                  => new NobilityTypeTitle(I18N::translate('Title')),
            'INDI:WILL'                  => new Will(I18N::translate('Will')),
            'INDI:_SP_DEAT'              => new Death(I18N::translate('Death of spouse')),
            'NOTE'                       => new NoteRecord(I18N::translate('Shared note')),
            'NOTE:CHAN'                  => new Change(I18N::translate('Last change')),
            'NOTE:CHAN:DATE'             => new ChangeDate(I18N::translate('Date of last change')),
            'NOTE:CHAN:DATE:TIME'        => new TimeValueNow(I18N::translate('Time of last change')),
            'NOTE:CHAN:NOTE'             => new NoteStructure(I18N::translate('Note on last change')),
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
            'NOTE:SOUR:NOTE'             => new NoteStructure(I18N::translate('Note on source citation')),
            'NOTE:SOUR:OBJE'             => new XrefMedia(I18N::translate('Media object')),
            'NOTE:SOUR:PAGE'             => new WhereWithinSource(I18N::translate('Citation details')),
            'NOTE:SOUR:QUAY'             => new CertaintyAssessment(I18N::translate('Quality of data')),
            'OBJE'                       => new MediaRecord(I18N::translate('Media object')),
            'OBJE:BLOB'                  => new CustomElement(I18N::translate('Binary data object')),
            'OBJE:CHAN'                  => new Change(I18N::translate('Last change')),
            'OBJE:CHAN:DATE'             => new ChangeDate(I18N::translate('Date of last change')),
            'OBJE:CHAN:DATE:TIME'        => new TimeValueNow(I18N::translate('Time of last change')),
            'OBJE:CHAN:NOTE'             => new NoteStructure(I18N::translate('Note on last change')),
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
            'OBJE:SOUR:NOTE'             => new NoteStructure(I18N::translate('Note on source citation')),
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
            'REPO:CHAN:DATE:TIME'        => new TimeValueNow(I18N::translate('Time of last change')),
            'REPO:CHAN:NOTE'             => new NoteStructure(I18N::translate('Note on last change')),
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
            'SOUR:CHAN:DATE:TIME'        => new TimeValueNow(I18N::translate('Time of last change')),
            'SOUR:CHAN:NOTE'             => new NoteStructure(I18N::translate('Note on last change')),
            'SOUR:DATA'                  => new EmptyElement(I18N::translate('Data'), ['EVEN' => '0:M', 'AGNC' => '0:1', 'NOTE' => '0:M']),
            'SOUR:DATA:AGNC'             => new ResponsibleAgency(I18N::translate('Agency')),
            'SOUR:DATA:EVEN'             => new EventsRecorded(I18N::translate('Events')),
            'SOUR:DATA:EVEN:DATE'        => new DateValue(I18N::translate('Date range')),
            'SOUR:DATA:EVEN:PLAC'        => new SourceJurisdictionPlace(I18N::translate('Place'), []),
            'SOUR:DATA:NOTE'             => new NoteStructure(I18N::translate('Note on source data')),
            'SOUR:NOTE'                  => new NoteStructure(I18N::translate('Note on source')),
            'SOUR:OBJE'                  => new XrefMedia(I18N::translate('Media object')),
            'SOUR:PUBL'                  => new SourcePublicationFacts(I18N::translate('Publication')),
            'SOUR:REFN'                  => new UserReferenceNumber(I18N::translate('Reference number')),
            'SOUR:REFN:TYPE'             => new UserReferenceType(I18N::translate('Type of reference number')),
            'SOUR:REPO'                  => new XrefRepository(I18N::translate('Repository')),
            'SOUR:REPO:CALN'             => new SourceCallNumber(I18N::translate('Call number')),
            'SOUR:REPO:CALN:MEDI'        => new SourceMediaType(I18N::translate('Media type')),
            'SOUR:REPO:NOTE'             => new NoteStructure(I18N::translate('Note on repository reference')),
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
            'SUBM:CHAN:DATE:TIME'        => new TimeValueNow(I18N::translate('Time of last change')),
            'SUBM:CHAN:NOTE'             => new NoteStructure(I18N::translate('Note on last change')),
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
            'SUBN:CHAN:DATE:TIME'        => new TimeValueNow(I18N::translate('Time of last change')),
            'SUBN:CHAN:NOTE'             => new NoteStructure(I18N::translate('Note on last change')),
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
     * Custom tags for webtrees.
     *
     * @return array<string,ElementInterface>
     */
    private function webtreesTags(): array
    {
        return [
            'FAM:CHAN:_WT_USER'           => new WebtreesUser(I18N::translate('Author of last change')),
            'FAM:*:_ASSO'                 => new XrefAssociate(I18N::translate('Associate')),
            'FAM:*:_ASSO:NOTE'            => new NoteStructure(I18N::translate('Note on association')),
            'FAM:*:_ASSO:RELA'            => new RelationIsDescriptor(I18N::translate('Relationship')),
            'FAM:*:_ASSO:SOUR'            => new XrefSource(I18N::translate('Source citation')),
            'FAM:*:_ASSO:SOUR:DATA'       => new SourceData(I18N::translate('Data')),
            'FAM:*:_ASSO:SOUR:DATA:DATE'  => new DateValue(I18N::translate('Date of entry in original source')),
            'FAM:*:_ASSO:SOUR:DATA:TEXT'  => new TextFromSource(I18N::translate('Text')),
            'FAM:*:_ASSO:SOUR:EVEN'       => new EventTypeCitedFrom(I18N::translate('Event')),
            'FAM:*:_ASSO:SOUR:EVEN:ROLE'  => new RoleInEvent(I18N::translate('Role')),
            'FAM:*:_ASSO:SOUR:NOTE'       => new NoteStructure(I18N::translate('Note on source citation')),
            'FAM:*:_ASSO:SOUR:OBJE'       => new XrefMedia(I18N::translate('Media object')),
            'FAM:*:_ASSO:SOUR:PAGE'       => new WhereWithinSource(I18N::translate('Citation details')),
            'FAM:*:_ASSO:SOUR:QUAY'       => new CertaintyAssessment(I18N::translate('Quality of data')),
            'INDI:CHAN:_WT_USER'          => new WebtreesUser(I18N::translate('Author of last change')),
            'INDI:*:_ASSO'                => new XrefAssociate(I18N::translate('Associate')),
            'INDI:*:_ASSO:NOTE'           => new NoteStructure(I18N::translate('Note on association')),
            'INDI:*:_ASSO:RELA'           => new RelationIsDescriptor(I18N::translate('Relationship')),
            'INDI:*:_ASSO:SOUR'           => new XrefSource(I18N::translate('Source citation')),
            'INDI:*:_ASSO:SOUR:DATA'      => new SourceData(I18N::translate('Data')),
            'INDI:*:_ASSO:SOUR:DATA:DATE' => new DateValue(I18N::translate('Date of entry in original source')),
            'INDI:*:_ASSO:SOUR:DATA:TEXT' => new TextFromSource(I18N::translate('Text')),
            'INDI:*:_ASSO:SOUR:EVEN'      => new EventTypeCitedFrom(I18N::translate('Event')),
            'INDI:*:_ASSO:SOUR:EVEN:ROLE' => new RoleInEvent(I18N::translate('Role')),
            'INDI:*:_ASSO:SOUR:NOTE'      => new NoteStructure(I18N::translate('Note on source citation')),
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

        // GEDCOM 7 tags
        if (Site::getPreference('CUSTOM_FAM_FACT') === '1') {
            $subtags['FAM'][] = ['FACT', '0:M'];
        }
        if (Site::getPreference('CUSTOM_FAM_NCHI') === '1') {
            $subtags['FAM:NCHI'] = [
                ['TYPE', '0:1:?'],
                ['DATE', '0:1'],
                ['PLAC', '0:1:?'],
                ['ADDR', '0:1:?'],
                ['EMAIL', '0:1:?'],
                ['WWW', '0:1:?'],
                ['PHON', '0:1:?'],
                ['FAX', '0:1:?'],
                ['CAUS', '0:1:?'],
                ['AGNC', '0:1:?'],
                ['RELI', '0:1:?'],
                ['NOTE', '0:M'],
                ['OBJE', '0:M'],
                ['SOUR', '0:M'],
                ['RESN', '0:1'],
            ];
        }

        if (Site::getPreference('CUSTOM_TIME_TAGS') === '1') {
            $subtags['INDI:BIRT:DATE'][] = ['TIME', '0:1'];
            $subtags['INDI:DEAT:DATE'][] = ['TIME', '0:1'];
        }

        if (Site::getPreference('CUSTOM_GEDCOM_L_TAGS') === '1') {
            $subtags['FAM'][]               = ['_ASSO', '0:M'];
            $subtags['FAM'][]               = ['_STAT', '0:1'];
            $subtags['FAM'][]               = ['_UID', '0:M'];
            $subtags['FAM:*:ADDR']          = [['_NAME', '0:1:?', 'ADR1']];
            $subtags['FAM:*:PLAC']          = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1'], ['_GOV', '0:1']];
            $subtags['FAM:ENGA:PLAC']       = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1'], ['_GOV', '0:1']];
            $subtags['FAM:MARB:PLAC']       = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1'], ['_GOV', '0:1']];
            $subtags['FAM:MARR']            = [['_WITN', '0:1']];
            $subtags['FAM:MARR:PLAC']       = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1'], ['_GOV', '0:1']];
            $subtags['FAM:SLGS:PLAC']       = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1'], ['_GOV', '0:1']];
            $subtags['INDI'][]              = ['_UID', '0:M'];
            $subtags['INDI:*:ADDR']         = [['_NAME', '0:1:?', 'ADR1']];
            $subtags['INDI:*:PLAC']         = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1'], ['_GOV', '0:1']];
            $subtags['INDI:ADOP:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1'], ['_GOV', '0:1']];
            $subtags['INDI:BAPL:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1'], ['_GOV', '0:1']];
            $subtags['INDI:BAPM']           = [['_GODP', '0:1'], ['_WITN', '0:1']];
            $subtags['INDI:BAPM:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1'], ['_GOV', '0:1']];
            $subtags['INDI:BARM:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1'], ['_GOV', '0:1']];
            $subtags['INDI:BASM:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1'], ['_GOV', '0:1']];
            $subtags['INDI:BIRT:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1'], ['_GOV', '0:1']];
            $subtags['INDI:BLES:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1'], ['_GOV', '0:1']];
            $subtags['INDI:BURI:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1'], ['_GOV', '0:1']];
            $subtags['INDI:CENS:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1'], ['_GOV', '0:1']];
            $subtags['INDI:CHR']            = [['_GODP', '0:1'], ['_WITN', '0:1']];
            $subtags['INDI:CHR:PLAC']       = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1'], ['_GOV', '0:1']];
            $subtags['INDI:CHRA:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1'], ['_GOV', '0:1']];
            $subtags['INDI:CONF:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1'], ['_GOV', '0:1']];
            $subtags['INDI:CONL:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1'], ['_GOV', '0:1']];
            $subtags['INDI:CREM:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1'], ['_GOV', '0:1']];
            $subtags['INDI:DEAT:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1'], ['_GOV', '0:1']];
            $subtags['INDI:EMIG:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1'], ['_GOV', '0:1']];
            $subtags['INDI:ENDL:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1'], ['_GOV', '0:1']];
            $subtags['INDI:EVEN:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1'], ['_GOV', '0:1']];
            $subtags['INDI:FCOM:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1'], ['_GOV', '0:1']];
            $subtags['INDI:IMMI:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1'], ['_GOV', '0:1']];
            $subtags['INDI:NAME']           = [['_RUFNAME', '0:1', 'SPFX']];
            $subtags['INDI:NATU:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1'], ['_GOV', '0:1']];
            $subtags['INDI:ORDN:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1'], ['_GOV', '0:1']];
            $subtags['INDI:RESI:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1'], ['_GOV', '0:1']];
            $subtags['INDI:SLGC:PLAC']      = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1'], ['_GOV', '0:1']];
            $subtags['NOTE']                = [['_UID', '0:M']];
            $subtags['OBJE']                = [['_PRIM', '0:1:?'], ['_UID', '0:M']];
            $subtags['REPO']                = [['_UID', '0:M']];
            $subtags['REPO:ADDR']           = [['_NAME', '0:1', 'ADR1']];
            $subtags['SOUR']                = [['_UID', '0:M']];
            $subtags['SOUR:DATA:EVEN:PLAC'] = [['_POST', '0:1'], ['_MAIDENHEAD', '0:1:?'], ['_LOC', '0:1'], ['_GOV', '0:1']];
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

            $custom_tags = [
                new Aldfaer(),
                new Ancestry(),
                new BrothersKeeper(),
                new FamilySearch(),
                new FamilyTreeBuilder(),
                new FamilyTreeMaker(),
                new Gedcom7(),
                new GedcomL(),
                new Geneatique(),
                new GenPlusWin(),
                new Heredis(),
                new Legacy(),
                new MyHeritage(),
                new PersonalAncestralFile(),
                new PhpGedView(),
                new ProGen(),
                new Reunion(),
                new RootsMagic(),
            ];

            foreach ($custom_tags as $custom_tag) {
                $element_factory->registerTags($custom_tag->tags());
            }

            // Creating tags from all the above are grouped into one place
            $element_factory->registerSubTags($this->customSubTags());
        }
    }
}
