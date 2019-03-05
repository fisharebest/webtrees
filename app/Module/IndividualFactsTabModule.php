<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Services\ClipboardService;
use Fisharebest\Webtrees\Services\ModuleService;
use Illuminate\Support\Collection;

/**
 * Class IndividualFactsTabModule
 */
class IndividualFactsTabModule extends AbstractModule implements ModuleTabInterface
{
    use ModuleTabTrait;

    /** @var ModuleService */
    private $module_service;

    /** @var ClipboardService */
    private $clipboard_service;

    /**
     * UserWelcomeModule constructor.
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

    /** {@inheritdoc} */
    public function isGrayedOut(Individual $individual): bool
    {
        return false;
    }

    /** {@inheritdoc} */
    public function getTabContent(Individual $individual): string
    {
        // Only include events of close relatives that are between birth and death
        $min_date = $individual->getEstimatedBirthDate();
        $max_date = $individual->getEstimatedDeathDate();

        // Which facts and events are handled by other modules?
        $sidebar_facts = $this->module_service
            ->findByComponent(ModuleSidebarInterface::class, $individual->tree(), Auth::user())
            ->map(function (ModuleSidebarInterface $sidebar): Collection {
                return $sidebar->supportedFacts();
            });

        $tab_facts = $this->module_service
            ->findByComponent(ModuleTabInterface::class, $individual->tree(), Auth::user())
            ->map(function (ModuleTabInterface $sidebar): Collection {
                return $sidebar->supportedFacts();
            });

        $exclude_facts = $sidebar_facts->merge($tab_facts)->flatten();


        // The individual’s own facts
        $indifacts = $individual->facts()
            ->filter(function (Fact $fact) use ($exclude_facts): bool {
                return !$exclude_facts->contains($fact->getTag());
            });

        // Add spouse-family facts
        foreach ($individual->spouseFamilies() as $family) {
            foreach ($family->facts() as $fact) {
                if (!$exclude_facts->contains($fact->getTag()) && $fact->getTag() !== 'CHAN') {
                    $indifacts->push($fact);
                }
            }

            $spouse = $family->spouse($individual);

            if ($spouse instanceof Individual) {
                $spouse_facts = $this->spouseFacts($individual, $spouse, $min_date, $max_date);
                $indifacts    = $indifacts->merge($spouse_facts);
            }

            $child_facts = $this->childFacts($individual, $family, '_CHIL', '', $min_date, $max_date);
            $indifacts   = $indifacts->merge($child_facts);
        }

        $parent_facts     = $this->parentFacts($individual, 1, $min_date, $max_date);
        $associate_facts  = $this->associateFacts($individual);
        $historical_facts = $this->historicalFacts($individual);

        $indifacts = $indifacts
            ->merge($parent_facts)
            ->merge($associate_facts)
            ->merge($historical_facts);

        $indifacts = Fact::sortFacts($indifacts);

        return view('modules/personal_facts/tab', [
            'can_edit'             => $individual->canEdit(),
            'clipboard_facts'      => $this->clipboard_service->pastableFacts($individual, $exclude_facts),
            'has_historical_facts' => !empty($historical_facts),
            'individual'           => $individual,
            'facts'                => $indifacts,
        ]);
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

    /** {@inheritdoc} */
    public function hasTabContent(Individual $individual): bool
    {
        return true;
    }

    /** {@inheritdoc} */
    public function canLoadAjax(): bool
    {
        return false;
    }

    /**
     * Spouse facts that are shown on an individual’s page.
     *
     * @param Individual $individual Show events that occured during the lifetime of this individual
     * @param Individual $spouse     Show events of this individual
     * @param Date       $min_date
     * @param Date       $max_date
     *
     * @return Fact[]
     */
    private function spouseFacts(Individual $individual, Individual $spouse, Date $min_date, Date $max_date): array
    {
        $SHOW_RELATIVES_EVENTS = $individual->tree()->getPreference('SHOW_RELATIVES_EVENTS');

        $facts = [];
        if (strstr($SHOW_RELATIVES_EVENTS, '_DEAT_SPOU')) {
            foreach ($spouse->facts(Gedcom::DEATH_EVENTS) as $fact) {
                if ($this->includeFact($fact, $min_date, $max_date)) {
                    // Convert the event to a close relatives event.
                    $rela_fact = clone($fact);
                    $rela_fact->setTag('_' . $fact->getTag() . '_SPOU');
                    $facts[] = $rela_fact;
                }
            }
        }

        return $facts;
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
     * @return Fact[]
     */
    private function childFacts(Individual $person, Family $family, $option, $relation, Date $min_date, Date $max_date): array
    {
        $SHOW_RELATIVES_EVENTS = $person->tree()->getPreference('SHOW_RELATIVES_EVENTS');

        $facts = [];

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
            if ($child->xref() == $person->xref()) {
                // We are not our own sibling!
                continue;
            }
            // add child’s birth
            if (strpos($SHOW_RELATIVES_EVENTS, '_BIRT' . str_replace('_HSIB', '_SIBL', $option)) !== false) {
                foreach ($child->facts(Gedcom::BIRTH_EVENTS) as $fact) {
                    // Always show _BIRT_CHIL, even if the dates are not known
                    if ($option == '_CHIL' || $this->includeFact($fact, $min_date, $max_date)) {
                        if ($option == '_GCHI' && $relation == 'dau') {
                            // Convert the event to a close relatives event.
                            $rela_fact = clone($fact);
                            $rela_fact->setTag('_' . $fact->getTag() . '_GCH1');
                            $facts[] = $rela_fact;
                        } elseif ($option == '_GCHI' && $relation == 'son') {
                            // Convert the event to a close relatives event.
                            $rela_fact = clone($fact);
                            $rela_fact->setTag('_' . $fact->getTag() . '_GCH2');
                            $facts[] = $rela_fact;
                        } else {
                            // Convert the event to a close relatives event.
                            $rela_fact = clone($fact);
                            $rela_fact->setTag('_' . $fact->getTag() . $option);
                            $facts[] = $rela_fact;
                        }
                    }
                }
            }
            // add child’s death
            if (strpos($SHOW_RELATIVES_EVENTS, '_DEAT' . str_replace('_HSIB', '_SIBL', $option)) !== false) {
                foreach ($child->facts(Gedcom::DEATH_EVENTS) as $fact) {
                    if ($this->includeFact($fact, $min_date, $max_date)) {
                        if ($option == '_GCHI' && $relation == 'dau') {
                            // Convert the event to a close relatives event.
                            $rela_fact = clone($fact);
                            $rela_fact->setTag('_' . $fact->getTag() . '_GCH1');
                            $facts[] = $rela_fact;
                        } elseif ($option == '_GCHI' && $relation == 'son') {
                            // Convert the event to a close relatives event.
                            $rela_fact = clone($fact);
                            $rela_fact->setTag('_' . $fact->getTag() . '_GCH2');
                            $facts[] = $rela_fact;
                        } else {
                            // Convert the event to a close relatives event.
                            $rela_fact = clone($fact);
                            $rela_fact->setTag('_' . $fact->getTag() . $option);
                            $facts[] = $rela_fact;
                        }
                    }
                }
            }
            // add child’s marriage
            if (strstr($SHOW_RELATIVES_EVENTS, '_MARR' . str_replace('_HSIB', '_SIBL', $option))) {
                foreach ($child->spouseFamilies() as $sfamily) {
                    foreach ($sfamily->facts(['MARR']) as $fact) {
                        if ($this->includeFact($fact, $min_date, $max_date)) {
                            if ($option == '_GCHI' && $relation == 'dau') {
                                // Convert the event to a close relatives event.
                                $rela_fact = clone($fact);
                                $rela_fact->setTag('_' . $fact->getTag() . '_GCH1');
                                $facts[] = $rela_fact;
                            } elseif ($option == '_GCHI' && $relation == 'son') {
                                // Convert the event to a close relatives event.
                                $rela_fact = clone($fact);
                                $rela_fact->setTag('_' . $fact->getTag() . '_GCH2');
                                $facts[] = $rela_fact;
                            } else {
                                // Convert the event to a close relatives event.
                                $rela_fact = clone($fact);
                                $rela_fact->setTag('_' . $fact->getTag() . $option);
                                $facts[] = $rela_fact;
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
     * @return Fact[]
     */
    private function parentFacts(Individual $person, $sosa, Date $min_date, Date $max_date): array
    {
        $SHOW_RELATIVES_EVENTS = $person->tree()->getPreference('SHOW_RELATIVES_EVENTS');

        $facts = [];

        if ($sosa == 1) {
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
                    foreach ($this->parentFacts($spouse, $spouse->sex() == 'F' ? 3 : 2, $min_date, $max_date) as $fact) {
                        $facts[] = $fact;
                    }
                }
            }

            if (strstr($SHOW_RELATIVES_EVENTS, '_MARR_PARE')) {
                // add father/mother marriages
                foreach ($person->childFamilies() as $sfamily) {
                    foreach ($sfamily->facts(['MARR']) as $fact) {
                        if ($this->includeFact($fact, $min_date, $max_date)) {
                            // marriage of parents (to each other)
                            $rela_fact = clone($fact);
                            $rela_fact->setTag('_' . $fact->getTag() . '_FAMC');
                            $facts[] = $rela_fact;
                        }
                    }
                }
                foreach ($person->childStepFamilies() as $sfamily) {
                    foreach ($sfamily->facts(['MARR']) as $fact) {
                        if ($this->includeFact($fact, $min_date, $max_date)) {
                            // marriage of a parent (to another spouse)
                            // Convert the event to a close relatives event
                            $rela_fact = clone($fact);
                            $rela_fact->setTag('_' . $fact->getTag() . '_PARE');
                            $facts[] = $rela_fact;
                        }
                    }
                }
            }
        }

        foreach ($person->childFamilies() as $family) {
            foreach ($family->spouses() as $parent) {
                if (strstr($SHOW_RELATIVES_EVENTS, '_DEAT' . ($sosa == 1 ? '_PARE' : '_GPAR'))) {
                    foreach ($parent->facts(Gedcom::DEATH_EVENTS) as $fact) {
                        if ($this->includeFact($fact, $min_date, $max_date)) {
                            switch ($sosa) {
                                case 1:
                                    // Convert the event to a close relatives event.
                                    $rela_fact = clone($fact);
                                    $rela_fact->setTag('_' . $fact->getTag() . '_PARE');
                                    $facts[] = $rela_fact;
                                    break;
                                case 2:
                                    // Convert the event to a close relatives event
                                    $rela_fact = clone($fact);
                                    $rela_fact->setTag('_' . $fact->getTag() . '_GPA1');
                                    $facts[] = $rela_fact;
                                    break;
                                case 3:
                                    // Convert the event to a close relatives event
                                    $rela_fact = clone($fact);
                                    $rela_fact->setTag('_' . $fact->getTag() . '_GPA2');
                                    $facts[] = $rela_fact;
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
     * Get any historical events.
     *
     * @param Individual $individual
     *
     * @return Fact[]
     */
    private function historicalFacts(Individual $individual): array
    {
        return $this->module_service->findByInterface(ModuleHistoricEventsInterface::class)
            ->map(function (ModuleHistoricEventsInterface $module) use ($individual): Collection {
                return $module->historicEventsForIndividual($individual);
            })
            ->flatten()
            ->all();
    }

    /**
     * Get the events of associates.
     *
     * @param Individual $person
     *
     * @return Fact[]
     */
    private function associateFacts(Individual $person): array
    {
        $facts = [];

        /** @var Individual[] $associates */
        $associates = array_merge(
            $person->linkedIndividuals('ASSO'),
            $person->linkedIndividuals('_ASSO'),
            $person->linkedFamilies('ASSO'),
            $person->linkedFamilies('_ASSO')
        );
        foreach ($associates as $associate) {
            foreach ($associate->facts() as $fact) {
                $arec = $fact->attribute('_ASSO');
                if (!$arec) {
                    $arec = $fact->attribute('ASSO');
                }
                if ($arec && trim($arec, '@') === $person->xref()) {
                    // Extract the important details from the fact
                    $factrec = '1 ' . $fact->getTag();
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

        return $facts;
    }

    /**
     * This module handles the following facts - so don't show them on the "Facts and events" tab.
     *
     * @return Collection|string[]
     */
    public function supportedFacts(): Collection
    {
        // We don't actually displaye these facts, but they are displayed
        // outside the tabs/sidebar systems. This just forces them to be excluded here.
        return new Collection(['NAME', 'SEX']);
    }
}
