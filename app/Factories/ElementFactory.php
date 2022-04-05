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
use Fisharebest\Webtrees\Elements\EntryRecordingDate;
use Fisharebest\Webtrees\Elements\EventAttributeType;
use Fisharebest\Webtrees\Elements\EventOrFactClassification;
use Fisharebest\Webtrees\Elements\EventsRecorded;
use Fisharebest\Webtrees\Elements\EventTypeCitedFrom;
use Fisharebest\Webtrees\Elements\FamilyCensus;
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
use Fisharebest\Webtrees\Elements\XrefAssociate;
use Fisharebest\Webtrees\Elements\XrefFamily;
use Fisharebest\Webtrees\Elements\XrefIndividual;
use Fisharebest\Webtrees\Elements\XrefLocation;
use Fisharebest\Webtrees\Elements\XrefMedia;
use Fisharebest\Webtrees\Elements\XrefRepository;
use Fisharebest\Webtrees\Elements\XrefSource;
use Fisharebest\Webtrees\Elements\XrefSubmission;
use Fisharebest\Webtrees\Elements\XrefSubmitter;
use Fisharebest\Webtrees\I18N;

use function array_merge;
use function preg_match;
use function var_dump;

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
        return $this->elements[$tag] ?? $this->findElementByWildcard($tag) ?? new UnknownElement($tag);
    }

    /**
     * Register GEDCOM tags.
     *
     * @param array<string,ElementInterface> $elements
     */
    public function registerTags(array $elements): void
    {
        $this->elements = $elements + $this->elements;
    }

    /**
     * Register more subtags.
     *
     * @param array<string,array<int,array<int,string>>> $subtags
     */
    public function registerSubTags(array $subtags): void
    {
        foreach ($subtags as $tag => $children) {
            foreach ($children as $child) {
                $this->make($tag)->subtag(...$child);
            }
        }
    }

    /**
     * @param string $tag
     *
     * @return ElementInterface|null
     */
    private function findElementByWildcard(string $tag): ?ElementInterface
    {
        foreach ($this->elements as $tags => $element) {
            if (str_contains($tags, '*')) {
                $regex = '/^' . strtr($tags, ['*' => '[^:]+']) . '$/';

                if (preg_match($regex, $tag)) {
                    return $element;
                }
            }
        }

        return null;
    }
}
