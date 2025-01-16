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

namespace Fisharebest\Webtrees\Services;

use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Module\ModuleHistoricEventsInterface;
use Illuminate\Support\Collection;

use function explode;
use function preg_match;
use function preg_replace;
use function str_replace;

/**
 * Provide lists of facts for IndividualFactsTabModule.
 */
class IndividualFactsService
{
    private LinkedRecordService $linked_record_service;

    private ModuleService $module_service;

    /**
     * @param LinkedRecordService $linked_record_service
     * @param ModuleService       $module_service
     */
    public function __construct(
        LinkedRecordService $linked_record_service,
        ModuleService $module_service
    ) {
        $this->linked_record_service = $linked_record_service;
        $this->module_service        = $module_service;
    }

    /**
     * The individuals own facts, such as birth and death.
     *
     * @param Individual             $individual
     * @param Collection<int,string> $exclude_facts
     *
     * @return Collection<int,Fact>
     */
    public function individualFacts(Individual $individual, Collection $exclude_facts): Collection
    {
        return $individual->facts()
            ->filter(fn (Fact $fact): bool => !$exclude_facts->contains($fact->tag()));
    }

    /**
     * The individuals own family facts, such as marriage and divorce.
     *
     * @param Individual             $individual
     * @param Collection<int,string> $exclude_facts
     *
     * @return Collection<int,Fact>
     */
    public function familyFacts(Individual $individual, Collection $exclude_facts): Collection
    {
        return $individual->spouseFamilies()
            ->map(fn (Family $family): Collection => $family->facts())
            ->flatten()
            ->filter(fn (Fact $fact): bool => !$exclude_facts->contains($fact->tag()));
    }

    /**
     * Get the events of associates.
     *
     * @param Individual $individual
     *
     * @return Collection<int,Fact>
     */
    public function associateFacts(Individual $individual): Collection
    {
        $facts = [];

        $asso1 = $this->linked_record_service->linkedIndividuals($individual, 'ASSO');
        $asso2 = $this->linked_record_service->linkedIndividuals($individual, '_ASSO');
        $asso3 = $this->linked_record_service->linkedFamilies($individual, 'ASSO');
        $asso4 = $this->linked_record_service->linkedFamilies($individual, '_ASSO');

        $associates = $asso1->merge($asso2)->merge($asso3)->merge($asso4);

        foreach ($associates as $associate) {
            foreach ($associate->facts() as $fact) {
                if (preg_match('/\n\d _?ASSO @' . $individual->xref() . '@/', $fact->gedcom())) {
                    // Extract the important details from the fact
                    $factrec = explode("\n", $fact->gedcom(), 2)[0];
                    if (preg_match('/\n2 DATE .*/', $fact->gedcom(), $match)) {
                        $factrec .= $match[0];
                    }
                    if (preg_match('/\n2 PLAC .*/', $fact->gedcom(), $match)) {
                        $factrec .= $match[0];
                    }
                    if ($associate instanceof Family) {
                        foreach ($associate->spouses() as $spouse) {
                            $factrec .= "\n2 _ASSO @" . $spouse->xref() . '@';
                        }
                    } else {
                        $factrec .= "\n2 _ASSO @" . $associate->xref() . '@';
                    }
                    $facts[] = new Fact($factrec, $associate, 'asso');
                }
            }
        }

        return new Collection($facts);
    }

    /**
     * Get the events of close relatives.
     *
     * @param Individual $individual
     *
     * @return Collection<int,Fact>
     */
    public function relativeFacts(Individual $individual): Collection
    {
        // Only include events of close relatives that are between birth and death
        $min_date = $individual->getEstimatedBirthDate();
        $max_date = $individual->getEstimatedDeathDate();

        $parent_facts = $this->parentFacts($individual, 1, $min_date, $max_date);

        $spouse_facts = $individual->spouseFamilies()
            ->filter(fn (Family $family): bool => $family->spouse($individual) instanceof Individual)
            ->map(fn (Family $family): Collection => $this->spouseFacts($individual, $family->spouse($individual), $min_date, $max_date))
            ->flatten();

        $child_facts = $individual->spouseFamilies()
            ->map(fn (Family $family): Collection => $this->childFacts($individual, $family, '_CHIL', '', $min_date, $max_date))
            ->flatten();

        return $parent_facts
            ->merge($child_facts)
            ->merge($spouse_facts)
            ->unique();
    }

    /**
     * Get any historical events.
     *
     * @param Individual $individual
     *
     * @return Collection<int,Fact>
     */
    public function historicFacts(Individual $individual): Collection
    {
        return $this->module_service->findByInterface(ModuleHistoricEventsInterface::class)
            ->map(static function (ModuleHistoricEventsInterface $module) use ($individual): Collection {
                return $module->historicEventsForIndividual($individual);
            })
            ->flatten();
    }

    /**
     * Get the events of children and grandchildren.
     *
     * @param Individual $person
     * @param Family     $family
     * @param string     $option
     * @param string     $relation
     * @param Date       $min_date
     * @param Date       $max_date
     *
     * @return Collection<int,Fact>
     */
    private function childFacts(Individual $person, Family $family, string $option, string $relation, Date $min_date, Date $max_date): Collection
    {
        $SHOW_RELATIVES_EVENTS = $person->tree()->getPreference('SHOW_RELATIVES_EVENTS');

        $birth_of_a_child = [
            'INDI:BIRT' => [
                'M' => I18N::translate('Birth of a son'),
                'F' => I18N::translate('Birth of a daughter'),
                'U' => I18N::translate('Birth of a child'),
            ],
            'INDI:CHR'  => [
                'M' => I18N::translate('Christening of a son'),
                'F' => I18N::translate('Christening of a daughter'),
                'U' => I18N::translate('Christening of a child'),
            ],
            'INDI:BAPM' => [
                'M' => I18N::translate('Baptism of a son'),
                'F' => I18N::translate('Baptism of a daughter'),
                'U' => I18N::translate('Baptism of a child'),
            ],
            'INDI:ADOP' => [
                'M' => I18N::translate('Adoption of a son'),
                'F' => I18N::translate('Adoption of a daughter'),
                'U' => I18N::translate('Adoption of a child'),
            ],
        ];

        $birth_of_a_sibling = [
            'INDI:BIRT' => [
                'M' => I18N::translate('Birth of a brother'),
                'F' => I18N::translate('Birth of a sister'),
                'U' => I18N::translate('Birth of a sibling'),
            ],
            'INDI:CHR'  => [
                'M' => I18N::translate('Christening of a brother'),
                'F' => I18N::translate('Christening of a sister'),
                'U' => I18N::translate('Christening of a sibling'),
            ],
            'INDI:BAPM' => [
                'M' => I18N::translate('Baptism of a brother'),
                'F' => I18N::translate('Baptism of a sister'),
                'U' => I18N::translate('Baptism of a sibling'),
            ],
            'INDI:ADOP' => [
                'M' => I18N::translate('Adoption of a brother'),
                'F' => I18N::translate('Adoption of a sister'),
                'U' => I18N::translate('Adoption of a sibling'),
            ],
        ];

        $birth_of_a_half_sibling = [
            'INDI:BIRT' => [
                'M' => I18N::translate('Birth of a half-brother'),
                'F' => I18N::translate('Birth of a half-sister'),
                'U' => I18N::translate('Birth of a half-sibling'),
            ],
            'INDI:CHR'  => [
                'M' => I18N::translate('Christening of a half-brother'),
                'F' => I18N::translate('Christening of a half-sister'),
                'U' => I18N::translate('Christening of a half-sibling'),
            ],
            'INDI:BAPM' => [
                'M' => I18N::translate('Baptism of a half-brother'),
                'F' => I18N::translate('Baptism of a half-sister'),
                'U' => I18N::translate('Baptism of a half-sibling'),
            ],
            'INDI:ADOP' => [
                'M' => I18N::translate('Adoption of a half-brother'),
                'F' => I18N::translate('Adoption of a half-sister'),
                'U' => I18N::translate('Adoption of a half-sibling'),
            ],
        ];

        $birth_of_a_grandchild = [
            'INDI:BIRT' => [
                'M' => I18N::translate('Birth of a grandson'),
                'F' => I18N::translate('Birth of a granddaughter'),
                'U' => I18N::translate('Birth of a grandchild'),
            ],
            'INDI:CHR'  => [
                'M' => I18N::translate('Christening of a grandson'),
                'F' => I18N::translate('Christening of a granddaughter'),
                'U' => I18N::translate('Christening of a grandchild'),
            ],
            'INDI:BAPM' => [
                'M' => I18N::translate('Baptism of a grandson'),
                'F' => I18N::translate('Baptism of a granddaughter'),
                'U' => I18N::translate('Baptism of a grandchild'),
            ],
            'INDI:ADOP' => [
                'M' => I18N::translate('Adoption of a grandson'),
                'F' => I18N::translate('Adoption of a granddaughter'),
                'U' => I18N::translate('Adoption of a grandchild'),
            ],
        ];

        $birth_of_a_grandchild1 = [
            'INDI:BIRT' => [
                'M' => I18N::translateContext('daughter’s son', 'Birth of a grandson'),
                'F' => I18N::translateContext('daughter’s daughter', 'Birth of a granddaughter'),
                'U' => I18N::translate('Birth of a grandchild'),
            ],
            'INDI:CHR'  => [
                'M' => I18N::translateContext('daughter’s son', 'Christening of a grandson'),
                'F' => I18N::translateContext('daughter’s daughter', 'Christening of a granddaughter'),
                'U' => I18N::translate('Christening of a grandchild'),
            ],
            'INDI:BAPM' => [
                'M' => I18N::translateContext('daughter’s son', 'Baptism of a grandson'),
                'F' => I18N::translateContext('daughter’s daughter', 'Baptism of a granddaughter'),
                'U' => I18N::translate('Baptism of a grandchild'),
            ],
            'INDI:ADOP' => [
                'M' => I18N::translateContext('daughter’s son', 'Adoption of a grandson'),
                'F' => I18N::translateContext('daughter’s daughter', 'Adoption of a granddaughter'),
                'U' => I18N::translate('Adoption of a grandchild'),
            ],
        ];

        $birth_of_a_grandchild2 = [
            'INDI:BIRT' => [
                'M' => I18N::translateContext('son’s son', 'Birth of a grandson'),
                'F' => I18N::translateContext('son’s daughter', 'Birth of a granddaughter'),
                'U' => I18N::translate('Birth of a grandchild'),
            ],
            'INDI:CHR'  => [
                'M' => I18N::translateContext('son’s son', 'Christening of a grandson'),
                'F' => I18N::translateContext('son’s daughter', 'Christening of a granddaughter'),
                'U' => I18N::translate('Christening of a grandchild'),
            ],
            'INDI:BAPM' => [
                'M' => I18N::translateContext('son’s son', 'Baptism of a grandson'),
                'F' => I18N::translateContext('son’s daughter', 'Baptism of a granddaughter'),
                'U' => I18N::translate('Baptism of a grandchild'),
            ],
            'INDI:ADOP' => [
                'M' => I18N::translateContext('son’s son', 'Adoption of a grandson'),
                'F' => I18N::translateContext('son’s daughter', 'Adoption of a granddaughter'),
                'U' => I18N::translate('Adoption of a grandchild'),
            ],
        ];

        $death_of_a_child = [
            'INDI:DEAT' => [
                'M' => I18N::translate('Death of a son'),
                'F' => I18N::translate('Death of a daughter'),
                'U' => I18N::translate('Death of a child'),
            ],
            'INDI:BURI' => [
                'M' => I18N::translate('Burial of a son'),
                'F' => I18N::translate('Burial of a daughter'),
                'U' => I18N::translate('Burial of a child'),
            ],
            'INDI:CREM' => [
                'M' => I18N::translate('Cremation of a son'),
                'F' => I18N::translate('Cremation of a daughter'),
                'U' => I18N::translate('Cremation of a child'),
            ],
        ];

        $death_of_a_sibling = [
            'INDI:DEAT' => [
                'M' => I18N::translate('Death of a brother'),
                'F' => I18N::translate('Death of a sister'),
                'U' => I18N::translate('Death of a sibling'),
            ],
            'INDI:BURI' => [
                'M' => I18N::translate('Burial of a brother'),
                'F' => I18N::translate('Burial of a sister'),
                'U' => I18N::translate('Burial of a sibling'),
            ],
            'INDI:CREM' => [
                'M' => I18N::translate('Cremation of a brother'),
                'F' => I18N::translate('Cremation of a sister'),
                'U' => I18N::translate('Cremation of a sibling'),
            ],
        ];

        $death_of_a_half_sibling = [
            'INDI:DEAT' => [
                'M' => I18N::translate('Death of a half-brother'),
                'F' => I18N::translate('Death of a half-sister'),
                'U' => I18N::translate('Death of a half-sibling'),
            ],
            'INDI:BURI' => [
                'M' => I18N::translate('Burial of a half-brother'),
                'F' => I18N::translate('Burial of a half-sister'),
                'U' => I18N::translate('Burial of a half-sibling'),
            ],
            'INDI:CREM' => [
                'M' => I18N::translate('Cremation of a half-brother'),
                'F' => I18N::translate('Cremation of a half-sister'),
                'U' => I18N::translate('Cremation of a half-sibling'),
            ],
        ];

        $death_of_a_grandchild = [
            'INDI:DEAT' => [
                'M' => I18N::translate('Death of a grandson'),
                'F' => I18N::translate('Death of a granddaughter'),
                'U' => I18N::translate('Death of a grandchild'),
            ],
            'INDI:BURI' => [
                'M' => I18N::translate('Burial of a grandson'),
                'F' => I18N::translate('Burial of a granddaughter'),
                'U' => I18N::translate('Burial of a grandchild'),
            ],
            'INDI:CREM' => [
                'M' => I18N::translate('Cremation of a grandson'),
                'F' => I18N::translate('Cremation of a granddaughter'),
                'U' => I18N::translate('Baptism of a grandchild'),
            ],
        ];

        $death_of_a_grandchild1 = [
            'INDI:DEAT' => [
                'M' => I18N::translateContext('daughter’s son', 'Death of a grandson'),
                'F' => I18N::translateContext('daughter’s daughter', 'Death of a granddaughter'),
                'U' => I18N::translate('Death of a grandchild'),
            ],
            'INDI:BURI' => [
                'M' => I18N::translateContext('daughter’s son', 'Burial of a grandson'),
                'F' => I18N::translateContext('daughter’s daughter', 'Burial of a granddaughter'),
                'U' => I18N::translate('Burial of a grandchild'),
            ],
            'INDI:CREM' => [
                'M' => I18N::translateContext('daughter’s son', 'Cremation of a grandson'),
                'F' => I18N::translateContext('daughter’s daughter', 'Cremation of a granddaughter'),
                'U' => I18N::translate('Baptism of a grandchild'),
            ],
        ];

        $death_of_a_grandchild2 = [
            'INDI:DEAT' => [
                'M' => I18N::translateContext('son’s son', 'Death of a grandson'),
                'F' => I18N::translateContext('son’s daughter', 'Death of a granddaughter'),
                'U' => I18N::translate('Death of a grandchild'),
            ],
            'INDI:BURI' => [
                'M' => I18N::translateContext('son’s son', 'Burial of a grandson'),
                'F' => I18N::translateContext('son’s daughter', 'Burial of a granddaughter'),
                'U' => I18N::translate('Burial of a grandchild'),
            ],
            'INDI:CREM' => [
                'M' => I18N::translateContext('son’s son', 'Cremation of a grandson'),
                'F' => I18N::translateContext('son’s daughter', 'Cremation of a granddaughter'),
                'U' => I18N::translate('Cremation of a grandchild'),
            ],
        ];

        $marriage_of_a_child = [
            'M' => I18N::translate('Marriage of a son'),
            'F' => I18N::translate('Marriage of a daughter'),
            'U' => I18N::translate('Marriage of a child'),
        ];

        $marriage_of_a_grandchild = [
            'M' => I18N::translate('Marriage of a grandson'),
            'F' => I18N::translate('Marriage of a granddaughter'),
            'U' => I18N::translate('Marriage of a grandchild'),
        ];

        $marriage_of_a_grandchild1 = [
            'M' => I18N::translateContext('daughter’s son', 'Marriage of a grandson'),
            'F' => I18N::translateContext('daughter’s daughter', 'Marriage of a granddaughter'),
            'U' => I18N::translate('Marriage of a grandchild'),
        ];

        $marriage_of_a_grandchild2 = [
            'M' => I18N::translateContext('son’s son', 'Marriage of a grandson'),
            'F' => I18N::translateContext('son’s daughter', 'Marriage of a granddaughter'),
            'U' => I18N::translate('Marriage of a grandchild'),
        ];

        $marriage_of_a_sibling = [
            'M' => I18N::translate('Marriage of a brother'),
            'F' => I18N::translate('Marriage of a sister'),
            'U' => I18N::translate('Marriage of a sibling'),
        ];

        $marriage_of_a_half_sibling = [
            'M' => I18N::translate('Marriage of a half-brother'),
            'F' => I18N::translate('Marriage of a half-sister'),
            'U' => I18N::translate('Marriage of a half-sibling'),
        ];

        $facts = new Collection();

        // Deal with recursion.
        if ($option === '_CHIL') {
            // Add grandchildren
            foreach ($family->children() as $child) {
                foreach ($child->spouseFamilies() as $cfamily) {
                    switch ($child->sex()) {
                        case 'M':
                            foreach ($this->childFacts($person, $cfamily, '_GCHI', 'son', $min_date, $max_date) as $fact) {
                                $facts[] = $fact;
                            }
                            break;
                        case 'F':
                            foreach ($this->childFacts($person, $cfamily, '_GCHI', 'dau', $min_date, $max_date) as $fact) {
                                $facts[] = $fact;
                            }
                            break;
                        default:
                            foreach ($this->childFacts($person, $cfamily, '_GCHI', 'chi', $min_date, $max_date) as $fact) {
                                $facts[] = $fact;
                            }
                            break;
                    }
                }
            }
        }

        // For each child in the family
        foreach ($family->children() as $child) {
            if ($child->xref() === $person->xref()) {
                // We are not our own sibling!
                continue;
            }
            // add child’s birth
            if (str_contains($SHOW_RELATIVES_EVENTS, '_BIRT' . str_replace('_HSIB', '_SIBL', $option))) {
                foreach ($child->facts(['BIRT', 'CHR', 'BAPM', 'ADOP']) as $fact) {
                    // Always show _BIRT_CHIL, even if the dates are not known
                    if ($option === '_CHIL' || $this->includeFact($fact, $min_date, $max_date)) {
                        switch ($option) {
                            case '_GCHI':
                                switch ($relation) {
                                    case 'dau':
                                        $facts[] = $this->convertEvent($fact, $birth_of_a_grandchild1[$fact->tag()], $fact->record()->sex());
                                        break;
                                    case 'son':
                                        $facts[] = $this->convertEvent($fact, $birth_of_a_grandchild2[$fact->tag()], $fact->record()->sex());
                                        break;
                                    case 'chil':
                                        $facts[] = $this->convertEvent($fact, $birth_of_a_grandchild[$fact->tag()], $fact->record()->sex());
                                        break;
                                }
                                break;
                            case '_SIBL':
                                $facts[] = $this->convertEvent($fact, $birth_of_a_sibling[$fact->tag()], $fact->record()->sex());
                                break;
                            case '_HSIB':
                                $facts[] = $this->convertEvent($fact, $birth_of_a_half_sibling[$fact->tag()], $fact->record()->sex());
                                break;
                            case '_CHIL':
                                $facts[] = $this->convertEvent($fact, $birth_of_a_child[$fact->tag()], $fact->record()->sex());
                                break;
                        }
                    }
                }
            }
            // add child’s death
            if (str_contains($SHOW_RELATIVES_EVENTS, '_DEAT' . str_replace('_HSIB', '_SIBL', $option))) {
                foreach ($child->facts(['DEAT', 'BURI', 'CREM']) as $fact) {
                    if ($this->includeFact($fact, $min_date, $max_date)) {
                        switch ($option) {
                            case '_GCHI':
                                switch ($relation) {
                                    case 'dau':
                                        $facts[] = $this->convertEvent($fact, $death_of_a_grandchild1[$fact->tag()], $fact->record()->sex());
                                        break;
                                    case 'son':
                                        $facts[] = $this->convertEvent($fact, $death_of_a_grandchild2[$fact->tag()], $fact->record()->sex());
                                        break;
                                    case 'chi':
                                        $facts[] = $this->convertEvent($fact, $death_of_a_grandchild[$fact->tag()], $fact->record()->sex());
                                        break;
                                }
                                break;
                            case '_SIBL':
                                $facts[] = $this->convertEvent($fact, $death_of_a_sibling[$fact->tag()], $fact->record()->sex());
                                break;
                            case '_HSIB':
                                $facts[] = $this->convertEvent($fact, $death_of_a_half_sibling[$fact->tag()], $fact->record()->sex());
                                break;
                            case '_CHIL':
                                $facts[] = $this->convertEvent($fact, $death_of_a_child[$fact->tag()], $fact->record()->sex());
                                break;
                        }
                    }
                }
            }

            // add child’s marriage
            if (str_contains($SHOW_RELATIVES_EVENTS, '_MARR' . str_replace('_HSIB', '_SIBL', $option))) {
                foreach ($child->spouseFamilies() as $sfamily) {
                    foreach ($sfamily->facts(['MARR']) as $fact) {
                        if ($this->includeFact($fact, $min_date, $max_date)) {
                            switch ($option) {
                                case '_GCHI':
                                    switch ($relation) {
                                        case 'dau':
                                            $facts[] = $this->convertEvent($fact, $marriage_of_a_grandchild1, $child->sex());
                                            break;
                                        case 'son':
                                            $facts[] = $this->convertEvent($fact, $marriage_of_a_grandchild2, $child->sex());
                                            break;
                                        case 'chi':
                                            $facts[] = $this->convertEvent($fact, $marriage_of_a_grandchild, $child->sex());
                                            break;
                                    }
                                    break;
                                case '_SIBL':
                                    $facts[] = $this->convertEvent($fact, $marriage_of_a_sibling, $child->sex());
                                    break;
                                case '_HSIB':
                                    $facts[] = $this->convertEvent($fact, $marriage_of_a_half_sibling, $child->sex());
                                    break;
                                case '_CHIL':
                                    $facts[] = $this->convertEvent($fact, $marriage_of_a_child, $child->sex());
                                    break;
                            }
                        }
                    }
                }
            }
        }

        return $facts;
    }

    /**
     * Get the events of parents and grandparents.
     *
     * @param Individual $person
     * @param int        $sosa
     * @param Date       $min_date
     * @param Date       $max_date
     *
     * @return Collection<int,Fact>
     */
    private function parentFacts(Individual $person, int $sosa, Date $min_date, Date $max_date): Collection
    {
        $SHOW_RELATIVES_EVENTS = $person->tree()->getPreference('SHOW_RELATIVES_EVENTS');

        $death_of_a_parent = [
            'INDI:DEAT' => [
                'M' => I18N::translate('Death of a father'),
                'F' => I18N::translate('Death of a mother'),
                'U' => I18N::translate('Death of a parent'),
            ],
            'INDI:BURI' => [
                'M' => I18N::translate('Burial of a father'),
                'F' => I18N::translate('Burial of a mother'),
                'U' => I18N::translate('Burial of a parent'),
            ],
            'INDI:CREM' => [
                'M' => I18N::translate('Cremation of a father'),
                'F' => I18N::translate('Cremation of a mother'),
                'U' => I18N::translate('Cremation of a parent'),
            ],
        ];

        $death_of_a_grandparent = [
            'INDI:DEAT' => [
                'M' => I18N::translate('Death of a grandfather'),
                'F' => I18N::translate('Death of a grandmother'),
                'U' => I18N::translate('Death of a grandparent'),
            ],
            'INDI:BURI' => [
                'M' => I18N::translate('Burial of a grandfather'),
                'F' => I18N::translate('Burial of a grandmother'),
                'U' => I18N::translate('Burial of a grandparent'),
            ],
            'INDI:CREM' => [
                'M' => I18N::translate('Cremation of a grandfather'),
                'F' => I18N::translate('Cremation of a grandmother'),
                'U' => I18N::translate('Cremation of a grandparent'),
            ],
        ];

        $death_of_a_maternal_grandparent = [
            'INDI:DEAT' => [
                'M' => I18N::translate('Death of a maternal grandfather'),
                'F' => I18N::translate('Death of a maternal grandmother'),
                'U' => I18N::translate('Death of a grandparent'),
            ],
            'INDI:BURI' => [
                'M' => I18N::translate('Burial of a maternal grandfather'),
                'F' => I18N::translate('Burial of a maternal grandmother'),
                'U' => I18N::translate('Burial of a grandparent'),
            ],
            'INDI:CREM' => [
                'M' => I18N::translate('Cremation of a maternal grandfather'),
                'F' => I18N::translate('Cremation of a maternal grandmother'),
                'U' => I18N::translate('Cremation of a grandparent'),
            ],
        ];

        $death_of_a_paternal_grandparent = [
            'INDI:DEAT' => [
                'M' => I18N::translate('Death of a paternal grandfather'),
                'F' => I18N::translate('Death of a paternal grandmother'),
                'U' => I18N::translate('Death of a grandparent'),
            ],
            'INDI:BURI' => [
                'M' => I18N::translate('Burial of a paternal grandfather'),
                'F' => I18N::translate('Burial of a paternal grandmother'),
                'U' => I18N::translate('Burial of a grandparent'),
            ],
            'INDI:CREM' => [
                'M' => I18N::translate('Cremation of a paternal grandfather'),
                'F' => I18N::translate('Cremation of a paternal grandmother'),
                'U' => I18N::translate('Cremation of a grandparent'),
            ],
        ];

        $marriage_of_a_parent = [
            'M' => I18N::translate('Marriage of a father'),
            'F' => I18N::translate('Marriage of a mother'),
            'U' => I18N::translate('Marriage of a parent'),
        ];

        $facts = new Collection();

        if ($sosa === 1) {
            foreach ($person->childFamilies() as $family) {
                // Add siblings
                foreach ($this->childFacts($person, $family, '_SIBL', '', $min_date, $max_date) as $fact) {
                    $facts[] = $fact;
                }
                foreach ($family->spouses() as $spouse) {
                    foreach ($spouse->spouseFamilies() as $sfamily) {
                        if ($family !== $sfamily) {
                            // Add half-siblings
                            foreach ($this->childFacts($person, $sfamily, '_HSIB', '', $min_date, $max_date) as $fact) {
                                $facts[] = $fact;
                            }
                        }
                    }
                    // Add grandparents
                    foreach ($this->parentFacts($spouse, $spouse->sex() === 'F' ? 3 : 2, $min_date, $max_date) as $fact) {
                        $facts[] = $fact;
                    }
                }
            }

            if (str_contains($SHOW_RELATIVES_EVENTS, '_MARR_PARE')) {
                // add father/mother marriages
                foreach ($person->childFamilies() as $sfamily) {
                    foreach ($sfamily->facts(['MARR']) as $fact) {
                        if ($this->includeFact($fact, $min_date, $max_date)) {
                            // marriage of parents (to each other)
                            $facts[] = $this->convertEvent($fact, ['U' => I18N::translate('Marriage of parents')], 'U');
                        }
                    }
                }
                foreach ($person->childStepFamilies() as $sfamily) {
                    foreach ($sfamily->facts(['MARR']) as $fact) {
                        if ($this->includeFact($fact, $min_date, $max_date)) {
                            // marriage of a parent (to another spouse)
                            $facts[] = $this->convertEvent($fact, $marriage_of_a_parent, 'U');
                        }
                    }
                }
            }
        }

        foreach ($person->childFamilies() as $family) {
            foreach ($family->spouses() as $parent) {
                if (str_contains($SHOW_RELATIVES_EVENTS, '_DEAT' . ($sosa === 1 ? '_PARE' : '_GPAR'))) {
                    foreach ($parent->facts(['DEAT', 'BURI', 'CREM']) as $fact) {
                        // Show death of parent when it happened prior to birth
                        if ($sosa === 1 && Date::compare($fact->date(), $min_date) < 0 || $this->includeFact($fact, $min_date, $max_date)) {
                            switch ($sosa) {
                                case 1:
                                    $facts[] = $this->convertEvent($fact, $death_of_a_parent[$fact->tag()], $fact->record()->sex());
                                    break;
                                case 2:
                                case 3:
                                    switch ($person->sex()) {
                                        case 'M':
                                            $facts[] = $this->convertEvent($fact, $death_of_a_paternal_grandparent[$fact->tag()], $fact->record()->sex());
                                            break;
                                        case 'F':
                                            $facts[] = $this->convertEvent($fact, $death_of_a_maternal_grandparent[$fact->tag()], $fact->record()->sex());
                                            break;
                                        default:
                                            $facts[] = $this->convertEvent($fact, $death_of_a_grandparent[$fact->tag()], $fact->record()->sex());
                                            break;
                                    }
                            }
                        }
                    }
                }
            }
        }

        return $facts;
    }

    /**
     * Spouse facts that are shown on an individual’s page.
     *
     * @param Individual $individual Show events that occurred during the lifetime of this individual
     * @param Individual $spouse     Show events of this individual
     * @param Date       $min_date
     * @param Date       $max_date
     *
     * @return Collection<int,Fact>
     */
    private function spouseFacts(Individual $individual, Individual $spouse, Date $min_date, Date $max_date): Collection
    {
        $SHOW_RELATIVES_EVENTS = $individual->tree()->getPreference('SHOW_RELATIVES_EVENTS');

        $death_of_a_spouse = [
            'INDI:DEAT' => [
                'M' => I18N::translate('Death of a husband'),
                'F' => I18N::translate('Death of a wife'),
                'U' => I18N::translate('Death of a spouse'),
            ],
            'INDI:BURI' => [
                'M' => I18N::translate('Burial of a husband'),
                'F' => I18N::translate('Burial of a wife'),
                'U' => I18N::translate('Burial of a spouse'),
            ],
            'INDI:CREM' => [
                'M' => I18N::translate('Cremation of a husband'),
                'F' => I18N::translate('Cremation of a wife'),
                'U' => I18N::translate('Cremation of a spouse'),
            ],
        ];

        $facts = new Collection();

        if (str_contains($SHOW_RELATIVES_EVENTS, '_DEAT_SPOU')) {
            foreach ($spouse->facts(['DEAT', 'BURI', 'CREM']) as $fact) {
                if ($this->includeFact($fact, $min_date, $max_date)) {
                    $facts[] = $this->convertEvent($fact, $death_of_a_spouse[$fact->tag()], $fact->record()->sex());
                }
            }
        }

        return $facts;
    }

    /**
     * Does a relative event occur within a date range (i.e. the individual's lifetime)?
     *
     * @param Fact $fact
     * @param Date $min_date
     * @param Date $max_date
     *
     * @return bool
     */
    private function includeFact(Fact $fact, Date $min_date, Date $max_date): bool
    {
        $fact_date = $fact->date();

        return $fact_date->isOK() && Date::compare($min_date, $fact_date) <= 0 && Date::compare($fact_date, $max_date) <= 0;
    }

    /**
     * Convert an event into a special "event of a close relative".
     *
     * @param Fact          $fact
     * @param array<string> $types
     * @param string        $sex
     *
     * @return Fact
     */
    private function convertEvent(Fact $fact, array $types, string $sex): Fact
    {
        $type = $types[$sex] ?? $types['U'];

        $gedcom = $fact->gedcom();
        $gedcom = preg_replace('/\n2 TYPE .*/', '', $gedcom);
        $gedcom = preg_replace('/^1 .*/', "1 EVEN CLOSE_RELATIVE\n2 TYPE " . $type, $gedcom);

        $converted = new Fact($gedcom, $fact->record(), $fact->id());

        if ($fact->isPendingAddition()) {
            $converted->setPendingAddition();
        }

        if ($fact->isPendingDeletion()) {
            $converted->setPendingDeletion();
        }

        return $converted;
    }
}
