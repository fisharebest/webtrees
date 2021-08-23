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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Services\ClipboardService;
use Fisharebest\Webtrees\Services\ModuleService;
use Illuminate\Support\Collection;

use function explode;
use function preg_match;
use function preg_replace;
use function str_contains;
use function str_replace;
use function view;

/**
 * Class IndividualFactsTabModule
 */
class IndividualFactsTabModule extends AbstractModule implements ModuleTabInterface
{
    use ModuleTabTrait;

    private ModuleService $module_service;

    private ClipboardService $clipboard_service;

    /**
     * IndividualFactsTabModule constructor.
     *
     * @param ModuleService    $module_service
     * @param ClipboardService $clipboard_service
     */
    public function __construct(ModuleService $module_service, ClipboardService $clipboard_service)
    {
        $this->module_service    = $module_service;
        $this->clipboard_service = $clipboard_service;
    }

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module/tab on the individual page. */
        return I18N::translate('Facts and events');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “Facts and events” module */
        return I18N::translate('A tab showing the facts and events of an individual.');
    }

    /**
     * The default position for this tab.  It can be changed in the control panel.
     *
     * @return int
     */
    public function defaultTabOrder(): int
    {
        return 1;
    }

    /**
     * A greyed out tab has no actual content, but may perhaps have
     * options to create content.
     *
     * @param Individual $individual
     *
     * @return bool
     */
    public function isGrayedOut(Individual $individual): bool
    {
        return false;
    }

    /**
     * Generate the HTML content of this tab.
     *
     * @param Individual $individual
     *
     * @return string
     */
    public function getTabContent(Individual $individual): string
    {
        // Only include events of close relatives that are between birth and death
        $min_date = $individual->getEstimatedBirthDate();
        $max_date = $individual->getEstimatedDeathDate();

        // Which facts and events are handled by other modules?
        $sidebar_facts = $this->module_service
            ->findByComponent(ModuleSidebarInterface::class, $individual->tree(), Auth::user())
            ->map(fn (ModuleSidebarInterface $sidebar): Collection => $sidebar->supportedFacts());

        $tab_facts = $this->module_service
            ->findByComponent(ModuleTabInterface::class, $individual->tree(), Auth::user())
            ->map(fn (ModuleTabInterface $tab): Collection => $tab->supportedFacts());

        $exclude_facts = $sidebar_facts->merge($tab_facts)->flatten();

        // The individual’s own facts
        $individual_facts = $individual->facts()
            ->filter(fn (Fact $fact): bool => !$exclude_facts->contains($fact->tag()));

        $relative_facts = new Collection();

        // Add spouse-family facts
        foreach ($individual->spouseFamilies() as $family) {
            foreach ($family->facts() as $fact) {
                if (!$exclude_facts->contains($fact->tag()) && $fact->tag() !== 'FAM:CHAN') {
                    $relative_facts->push($fact);
                }
            }

            $spouse = $family->spouse($individual);

            if ($spouse instanceof Individual) {
                $spouse_facts   = $this->spouseFacts($individual, $spouse, $min_date, $max_date);
                $relative_facts = $relative_facts->merge($spouse_facts);
            }

            $child_facts    = $this->childFacts($individual, $family, '_CHIL', '', $min_date, $max_date);
            $relative_facts = $relative_facts->merge($child_facts);
        }

        $parent_facts    = $this->parentFacts($individual, 1, $min_date, $max_date);
        $relative_facts  = $relative_facts->merge($parent_facts);
        $associate_facts = $this->associateFacts($individual);
        $historic_facts  = $this->historicFacts($individual);

        $individual_facts = $individual_facts
            ->merge($associate_facts)
            ->merge($historic_facts)
            ->merge($relative_facts);

        $individual_facts = Fact::sortFacts($individual_facts);

        return view('modules/personal_facts/tab', [
            'can_edit'            => $individual->canEdit(),
            'clipboard_facts'     => $this->clipboard_service->pastableFacts($individual),
            'has_associate_facts' => $associate_facts->isNotEmpty(),
            'has_historic_facts'  => $historic_facts->isNotEmpty(),
            'has_relative_facts'  => $relative_facts->isNotEmpty(),
            'individual'          => $individual,
            'facts'               => $individual_facts,
        ]);
    }

    /**
     * Spouse facts that are shown on an individual’s page.
     *
     * @param Individual $individual Show events that occured during the lifetime of this individual
     * @param Individual $spouse     Show events of this individual
     * @param Date       $min_date
     * @param Date       $max_date
     *
     * @return Collection<Fact>
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
                    $facts[] = $this->convertEvent($fact, $death_of_a_spouse[$fact->tag()][$fact->record()->sex()]);
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
     * @param Fact   $fact
     * @param string $type
     *
     * @return Fact
     */
    private function convertEvent(Fact $fact, string $type): Fact
    {
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
     * @return Collection<Fact>
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
        switch ($option) {
            case '_CHIL':
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
                break;
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
                                        $facts[] = $this->convertEvent($fact, $birth_of_a_grandchild1[$fact->tag()][$fact->record()->sex()]);
                                        break;
                                    case 'son':
                                        $facts[] = $this->convertEvent($fact, $birth_of_a_grandchild2[$fact->tag()][$fact->record()->sex()]);
                                        break;
                                    case 'chil':
                                        $facts[] = $this->convertEvent($fact, $birth_of_a_grandchild[$fact->tag()][$fact->record()->sex()]);
                                        break;
                                }
                                break;
                            case '_SIBL':
                                $facts[] = $this->convertEvent($fact, $birth_of_a_sibling[$fact->tag()][$fact->record()->sex()]);
                                break;
                            case '_HSIB':
                                $facts[] = $this->convertEvent($fact, $birth_of_a_half_sibling[$fact->tag()][$fact->record()->sex()]);
                                break;
                            case '_CHIL':
                                $facts[] = $this->convertEvent($fact, $birth_of_a_child[$fact->tag()][$fact->record()->sex()]);
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
                                        $facts[] = $this->convertEvent($fact, $death_of_a_grandchild1[$fact->tag()][$fact->record()->sex()]);
                                        break;
                                    case 'son':
                                        $facts[] = $this->convertEvent($fact, $death_of_a_grandchild2[$fact->tag()][$fact->record()->sex()]);
                                        break;
                                    case 'chi':
                                        $facts[] = $this->convertEvent($fact, $death_of_a_grandchild[$fact->tag()][$fact->record()->sex()]);
                                        break;
                                }
                                break;
                            case '_SIBL':
                                $facts[] = $this->convertEvent($fact, $death_of_a_sibling[$fact->tag()][$fact->record()->sex()]);
                                break;
                            case '_HSIB':
                                $facts[] = $this->convertEvent($fact, $death_of_a_half_sibling[$fact->tag()][$fact->record()->sex()]);
                                break;
                            case '_CHIL':
                                $facts[] = $this->convertEvent($fact, $death_of_a_child[$fact->tag()][$fact->record()->sex()]);
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
                                            $facts[] = $this->convertEvent($fact, $marriage_of_a_grandchild1[$child->sex()]);
                                            break;
                                        case 'son':
                                            $facts[] = $this->convertEvent($fact, $marriage_of_a_grandchild2[$child->sex()]);
                                            break;
                                        case 'chi':
                                            $facts[] = $this->convertEvent($fact, $marriage_of_a_grandchild[$child->sex()]);
                                            break;
                                    }
                                    break;
                                case '_SIBL':
                                    $facts[] = $this->convertEvent($fact, $marriage_of_a_sibling[$child->sex()]);
                                    break;
                                case '_HSIB':
                                    $facts[] = $this->convertEvent($fact, $marriage_of_a_half_sibling[$child->sex()]);
                                    break;
                                case '_CHIL':
                                    $facts[] = $this->convertEvent($fact, $marriage_of_a_child[$child->sex()]);
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
     * @return Collection<Fact>
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
                            $facts[] = $this->convertEvent($fact, I18N::translate('Marriage of parents'));
                        }
                    }
                }
                foreach ($person->childStepFamilies() as $sfamily) {
                    foreach ($sfamily->facts(['MARR']) as $fact) {
                        if ($this->includeFact($fact, $min_date, $max_date)) {
                            // marriage of a parent (to another spouse)
                            $facts[] = $this->convertEvent($fact, $marriage_of_a_parent['U']);
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
                                    $facts[] = $this->convertEvent($fact, $death_of_a_parent[$fact->tag()][$fact->record()->sex()]);
                                    break;
                                case 2:
                                case 3:
                                    switch ($person->sex()) {
                                        case 'M':
                                            $facts[] = $this->convertEvent($fact, $death_of_a_paternal_grandparent[$fact->tag()][$fact->record()->sex()]);
                                            break;
                                        case 'F':
                                            $facts[] = $this->convertEvent($fact, $death_of_a_maternal_grandparent[$fact->tag()][$fact->record()->sex()]);
                                            break;
                                        default:
                                            $facts[] = $this->convertEvent($fact, $death_of_a_grandparent[$fact->tag()][$fact->record()->sex()]);
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
     * Get the events of associates.
     *
     * @param Individual $person
     *
     * @return Collection<Fact>
     */
    private function associateFacts(Individual $person): Collection
    {
        $facts = [];

        $asso1 = $person->linkedIndividuals('ASSO');
        $asso2 = $person->linkedIndividuals('_ASSO');
        $asso3 = $person->linkedFamilies('ASSO');
        $asso4 = $person->linkedFamilies('_ASSO');

        $associates = $asso1->merge($asso2)->merge($asso3)->merge($asso4);

        foreach ($associates as $associate) {
            foreach ($associate->facts() as $fact) {
                if (preg_match('/\n\d _?ASSO @' . $person->xref() . '@/', $fact->gedcom())) {
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
     * Get any historical events.
     *
     * @param Individual $individual
     *
     * @return Collection<Fact>
     */
    private function historicFacts(Individual $individual): Collection
    {
        return $this->module_service->findByInterface(ModuleHistoricEventsInterface::class)
            ->map(static function (ModuleHistoricEventsInterface $module) use ($individual): Collection {
                return $module->historicEventsForIndividual($individual);
            })
            ->flatten();
    }

    /**
     * Is this tab empty? If so, we don't always need to display it.
     *
     * @param Individual $individual
     *
     * @return bool
     */
    public function hasTabContent(Individual $individual): bool
    {
        return true;
    }

    /**
     * Can this tab load asynchronously?
     *
     * @return bool
     */
    public function canLoadAjax(): bool
    {
        return false;
    }

    /**
     * This module handles the following facts - so don't show them on the "Facts and events" tab.
     *
     * @return Collection<string>
     */
    public function supportedFacts(): Collection
    {
        // We don't actually displaye these facts, but they are displayed
        // outside the tabs/sidebar systems. This just forces them to be excluded here.
        return new Collection(['INDI:NAME', 'INDI:SEX', 'INDI:OBJE']);
    }
}
