<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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

use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Module;
use Fisharebest\Webtrees\Site;

/**
 * Class IndividualFactsTabModule
 */
class IndividualFactsTabModule extends AbstractModule implements ModuleTabInterface
{
    /** {@inheritdoc} */
    public function getTitle(): string
    {
        /* I18N: Name of a module/tab on the individual page. */
        return I18N::translate('Facts and events');
    }

    /** {@inheritdoc} */
    public function getDescription(): string
    {
        /* I18N: Description of the “Facts and events” module */
        return I18N::translate('A tab showing the facts and events of an individual.');
    }

    /** {@inheritdoc} */
    public function defaultTabOrder(): int
    {
        return 10;
    }

    /** {@inheritdoc} */
    public function isGrayedOut(Individual $individual): bool
    {
        return false;
    }

    /** {@inheritdoc} */
    public function getTabContent(Individual $individual): string
    {
        $indifacts = [];
        // The individual’s own facts
        foreach ($individual->getFacts() as $fact) {
            switch ($fact->getTag()) {
                case 'SEX':
                case 'NAME':
                case 'SOUR':
                case 'OBJE':
                case 'NOTE':
                case 'FAMC':
                case 'FAMS':
                    break;
                default:
                    if (!array_key_exists('extra_info', Module::getActiveSidebars($individual->getTree())) || !ExtraInformationModule::showFact($fact)) {
                        $indifacts[] = $fact;
                    }
                    break;
            }
        }

        // Add spouse-family facts
        foreach ($individual->getSpouseFamilies() as $family) {
            foreach ($family->getFacts() as $fact) {
                switch ($fact->getTag()) {
                    case 'SOUR':
                    case 'NOTE':
                    case 'OBJE':
                    case 'CHAN':
                    case '_UID':
                    case 'RIN':
                    case 'HUSB':
                    case 'WIFE':
                    case 'CHIL':
                        break;
                    default:
                        $indifacts[] = $fact;
                        break;
                }
            }
            $spouse = $family->getSpouse($individual);
            if ($spouse) {
                foreach (self::spouseFacts($individual, $spouse) as $fact) {
                    $indifacts[] = $fact;
                }
            }
            foreach (self::childFacts($individual, $family, '_CHIL', '') as $fact) {
                $indifacts[] = $fact;
            }
        }

        foreach (self::parentFacts($individual, 1) as $fact) {
            $indifacts[] = $fact;
        }

        foreach (self::associateFacts($individual) as $fact) {
            $indifacts[] = $fact;
        }

        $has_historical_facts = false;
        foreach (self::historicalFacts($individual) as $fact) {
            $has_historical_facts = true;
            $indifacts[]          = $fact;
        }

        Functions::sortFacts($indifacts);

        return view('modules/personal_facts/tab', [
            'can_edit'             => $individual->canEdit(),
            'has_historical_facts' => $has_historical_facts,
            'individual'           => $individual,
            'facts'                => $indifacts,
        ]);
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
     *
     * @return Fact[]
     */
    private static function spouseFacts(Individual $individual, Individual $spouse): array
    {
        $SHOW_RELATIVES_EVENTS = $individual->getTree()->getPreference('SHOW_RELATIVES_EVENTS');

        $facts = [];
        if (strstr($SHOW_RELATIVES_EVENTS, '_DEAT_SPOU')) {
            // Only include events between birth and death
            $birt_date = $individual->getEstimatedBirthDate();
            $deat_date = $individual->getEstimatedDeathDate();

            foreach ($spouse->getFacts(WT_EVENTS_DEAT) as $fact) {
                $fact_date = $fact->getDate();
                if ($fact_date->isOK() && Date::compare($birt_date, $fact_date) <= 0 && Date::compare($fact_date, $deat_date) <= 0) {
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
     *
     * @return Fact[]
     */
    private static function childFacts(Individual $person, Family $family, $option, $relation): array
    {
        $SHOW_RELATIVES_EVENTS = $person->getTree()->getPreference('SHOW_RELATIVES_EVENTS');

        $facts = [];

        // Only include events between birth and death
        $birt_date = $person->getEstimatedBirthDate();
        $deat_date = $person->getEstimatedDeathDate();

        // Deal with recursion.
        switch ($option) {
            case '_CHIL':
                // Add grandchildren
                foreach ($family->getChildren() as $child) {
                    foreach ($child->getSpouseFamilies() as $cfamily) {
                        switch ($child->getSex()) {
                            case 'M':
                                foreach (self::childFacts($person, $cfamily, '_GCHI', 'son') as $fact) {
                                    $facts[] = $fact;
                                }
                                break;
                            case 'F':
                                foreach (self::childFacts($person, $cfamily, '_GCHI', 'dau') as $fact) {
                                    $facts[] = $fact;
                                }
                                break;
                            default:
                                foreach (self::childFacts($person, $cfamily, '_GCHI', 'chi') as $fact) {
                                    $facts[] = $fact;
                                }
                                break;
                        }
                    }
                }
                break;
        }

        // For each child in the family
        foreach ($family->getChildren() as $child) {
            if ($child->getXref() == $person->getXref()) {
                // We are not our own sibling!
                continue;
            }
            // add child’s birth
            if (strpos($SHOW_RELATIVES_EVENTS, '_BIRT' . str_replace('_HSIB', '_SIBL', $option)) !== false) {
                foreach ($child->getFacts(WT_EVENTS_BIRT) as $fact) {
                    $sgdate = $fact->getDate();
                    // Always show _BIRT_CHIL, even if the dates are not known
                    if ($option == '_CHIL' || $sgdate->isOK() && Date::compare($birt_date, $sgdate) <= 0 && Date::compare($sgdate, $deat_date) <= 0) {
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
                foreach ($child->getFacts(WT_EVENTS_DEAT) as $fact) {
                    $sgdate = $fact->getDate();
                    if ($sgdate->isOK() && Date::compare($birt_date, $sgdate) <= 0 && Date::compare($sgdate, $deat_date) <= 0) {
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
                foreach ($child->getSpouseFamilies() as $sfamily) {
                    foreach ($sfamily->getFacts(WT_EVENTS_MARR) as $fact) {
                        $sgdate = $fact->getDate();
                        if ($sgdate->isOK() && Date::compare($birt_date, $sgdate) <= 0 && Date::compare($sgdate, $deat_date) <= 0) {
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
     *
     * @return Fact[]
     */
    private static function parentFacts(Individual $person, $sosa): array
    {
        $SHOW_RELATIVES_EVENTS = $person->getTree()->getPreference('SHOW_RELATIVES_EVENTS');

        $facts = [];

        // Only include events between birth and death
        $birt_date = $person->getEstimatedBirthDate();
        $deat_date = $person->getEstimatedDeathDate();

        if ($sosa == 1) {
            foreach ($person->getChildFamilies() as $family) {
                // Add siblings
                foreach (self::childFacts($person, $family, '_SIBL', '') as $fact) {
                    $facts[] = $fact;
                }
                foreach ($family->getSpouses() as $spouse) {
                    foreach ($spouse->getSpouseFamilies() as $sfamily) {
                        if ($family !== $sfamily) {
                            // Add half-siblings
                            foreach (self::childFacts($person, $sfamily, '_HSIB', '') as $fact) {
                                $facts[] = $fact;
                            }
                        }
                    }
                    // Add grandparents
                    foreach (self::parentFacts($spouse, $spouse->getSex() == 'F' ? 3 : 2) as $fact) {
                        $facts[] = $fact;
                    }
                }
            }

            if (strstr($SHOW_RELATIVES_EVENTS, '_MARR_PARE')) {
                // add father/mother marriages
                foreach ($person->getChildFamilies() as $sfamily) {
                    foreach ($sfamily->getFacts(WT_EVENTS_MARR) as $fact) {
                        if ($fact->getDate()->isOK() && Date::compare($birt_date, $fact->getDate()) <= 0 && Date::compare($fact->getDate(), $deat_date) <= 0) {
                            // marriage of parents (to each other)
                            $rela_fact = clone($fact);
                            $rela_fact->setTag('_' . $fact->getTag() . '_FAMC');
                            $facts[] = $rela_fact;
                        }
                    }
                }
                foreach ($person->getChildStepFamilies() as $sfamily) {
                    foreach ($sfamily->getFacts(WT_EVENTS_MARR) as $fact) {
                        if ($fact->getDate()->isOK() && Date::compare($birt_date, $fact->getDate()) <= 0 && Date::compare($fact->getDate(), $deat_date) <= 0) {
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

        foreach ($person->getChildFamilies() as $family) {
            foreach ($family->getSpouses() as $parent) {
                if (strstr($SHOW_RELATIVES_EVENTS, '_DEAT' . ($sosa == 1 ? '_PARE' : '_GPAR'))) {
                    foreach ($parent->getFacts(WT_EVENTS_DEAT) as $fact) {
                        if ($fact->getDate()->isOK() && Date::compare($birt_date, $fact->getDate()) <= 0 && Date::compare($fact->getDate(), $deat_date) <= 0) {
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
     * @param Individual $person
     *
     * @return Fact[]
     */
    private static function historicalFacts(Individual $person): array
    {
        $SHOW_RELATIVES_EVENTS = $person->getTree()->getPreference('SHOW_RELATIVES_EVENTS');

        $facts = [];

        if ($SHOW_RELATIVES_EVENTS) {
            // Only include events between birth and death
            $birt_date = $person->getEstimatedBirthDate();
            $deat_date = $person->getEstimatedDeathDate();

            if (file_exists(Site::getPreference('INDEX_DIRECTORY') . 'histo.' . WT_LOCALE . '.php')) {
                $histo = [];
                require Site::getPreference('INDEX_DIRECTORY') . 'histo.' . WT_LOCALE . '.php';
                foreach ($histo as $hist) {
                    $fact  = new Fact($hist, $person, 'histo');
                    $sdate = $fact->getDate();
                    if ($sdate->isOK() && Date::compare($birt_date, $sdate) <= 0 && Date::compare($sdate, $deat_date) <= 0) {
                        $facts[] = $fact;
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
     * @return Fact[]
     */
    private static function associateFacts(Individual $person): array
    {
        $facts = [];

        $associates = array_merge(
            $person->linkedIndividuals('ASSO'),
            $person->linkedIndividuals('_ASSO'),
            $person->linkedFamilies('ASSO'),
            $person->linkedFamilies('_ASSO')
        );
        foreach ($associates as $associate) {
            foreach ($associate->getFacts() as $fact) {
                $arec = $fact->getAttribute('_ASSO');
                if (!$arec) {
                    $arec = $fact->getAttribute('ASSO');
                }
                if ($arec && trim($arec, '@') === $person->getXref()) {
                    // Extract the important details from the fact
                    $factrec = '1 ' . $fact->getTag();
                    if (preg_match('/\n2 DATE .*/', $fact->getGedcom(), $match)) {
                        $factrec .= $match[0];
                    }
                    if (preg_match('/\n2 PLAC .*/', $fact->getGedcom(), $match)) {
                        $factrec .= $match[0];
                    }
                    if ($associate instanceof Family) {
                        foreach ($associate->getSpouses() as $spouse) {
                            $factrec .= "\n2 _ASSO @" . $spouse->getXref() . '@';
                        }
                    } else {
                        $factrec .= "\n2 _ASSO @" . $associate->getXref() . '@';
                    }
                    $facts[] = new Fact($factrec, $associate, 'asso');
                }
            }
        }

        return $facts;
    }
}
