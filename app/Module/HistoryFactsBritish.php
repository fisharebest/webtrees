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

use Illuminate\Support\Collection;

/**
 * Class HistoryFactsBritish
 */
class HistoryFactsBritish extends AbstractModule implements ModuleHistoricEventsInterface
{
    use ModuleHistoricEventsTrait;

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        return 'British social history 🇬🇧';
    }

    /**
     * Should this module be enabled when it is first installed?
     *
     * @return bool
     */
    public function isEnabledByDefault(): bool
    {
        return false;
    }

    /**
     * All events provided by this module.
     *
     * @return Collection<string>
     */
    public function historicEventsAll(): Collection
    {
        return new Collection([
            "1 EVEN The Black Death kills about half the population\n2 TYPE Bubonic Plague\n2 DATE FROM @#DJULIAN@ JUN 1348 TO @#DJULIAN@ DEC 1349\n2 SOUR [Wikipedia: Black Death in England](https://en.wikipedia.org/wiki/Black_Death_in_England)",
            "1 EVEN The Black Death returns and kills about 20% the population\n2 TYPE Bubonic Plague\n2 DATE FROM @#DJULIAN@ 1361 TO @#DJULIAN@ 1362\n2 SOUR [Wikipedia: Black Death in England](https://en.wikipedia.org/wiki/Black_Death_in_England)",
            "1 EVEN The Peasants Revolt aka. The Great Rising was a uprising against serfdom and taxation, led by Wat Tyler\n2 TYPE Peasants Revolt\n2 DATE FROM @#DJULIAN@ 30 MAY 1381 TO @#DJULIAN@ NOV 1381\n2 SOUR [Wikipedia: Peasants' Revolt](https://en.wikipedia.org/wiki/Peasants'_Revolt)",
            "1 EVEN First English Civil War, royalists and parliamentarians battle for control of the country\n2 TYPE Civil War\n2 DATE FROM 22 AUG 1642 TO 21 MAR 1646\n2 SOUR [Wikipedia: English Civil War](https://en.wikipedia.org/wiki/First_English_Civil_War)",
            "1 EVEN Second English Civil War, a series of connected conflicts in the kingdoms of England, incorporating Wales, Scotland, and Ireland\n2 TYPE Civil War\n2 DATE FROM FEB 1648 TO AUG 1648\n2 SOUR [Wikipedia: English Civil War](https://en.wikipedia.org/wiki/Second_English_Civil_War)",
            "1 EVEN Third English Civil War, invasion of Scotland by an English army and a subsequent invasion of England by a Scottish army\n2 TYPE Civil War\n2 DATE FROM 20 JUN 1650 TO 03 SEP 1651\n2 SOUR [Wikipedia: English Civil War](https://en.wikipedia.org/wiki/Third_English_Civil_War)",
            "1 EVEN The Great Plague of London, the last major epidemic of the bubonic plague in England and killed a quarter of London's population\n2 TYPE Bubonic Plague\n2 DATE FROM FEB 1665 TO FEB 1666\n2 PLAC London, England\n2 SOUR [Wikipedia: Great Plague of London](https://en.wikipedia.org/wiki/Great_Plague_of_London)",
            "1 EVEN The Great Fire of London, whereby most of London was destroyed by the fires\n2 TYPE Great Fire\n2 DATE FROM 02 SEP 1666 TO 06 SEP 1666\n2 PLAC London, England\n2 SOUR [Wikipedia: Great Fire of London](https://en.wikipedia.org/wiki/Great_Fire_of_London)",
            "1 EVEN England and Scotland combine to form Great Britain (Union with Scotland Act 1706 by the Parliament of England)\n2 TYPE Act of Parlement\n2 DATE 04 NOV 1706\n2 SOUR [Wikipedia: Acts of Union 1707](https://en.wikipedia.org/wiki/Acts_of_Union_1707)",
            "1 EVEN England and Scotland combine to form Great Britain (Union with England Act by the Parliament of Scotland)\n2 TYPE Act of Parlement\n2 DATE 16 JAN 1707\n2 SOUR [Wikipedia: Acts of Union 1707](https://en.wikipedia.org/wiki/Acts_of_Union_1707)",
            "1 EVEN The Battle of Waterloo in which the Duke of Wellington defeated Napoleon Bonaparte thereby ending the Napoleonic wars\n2 TYPE Military Battle\n2 DATE 18 JUN 1815\n2 PLAC Waterloo, Belgium\n2 SOUR [Wikipedia: Battle of Waterloo](https://en.wikipedia.org/wiki/Battle_of_Waterloo)",
            "1 EVEN The Peterloo Massacre, where cavalry was used to disperse a large crowd who were demanding electoral reform in which  15 were killed and hundreds injured\n2 TYPE Massacre\n2 DATE 16 AUG 1819\n2 PLAC Manchester, England\n2 SOUR [Wikipedia: Peterloo Massacre](https://en.wikipedia.org/wiki/Peterloo_Massacre)",
            "1 EVEN The Crimean War in which Russia lost to an alliance made up of France, the Ottoman Empire, the United Kingdom and Sardinia\n2 TYPE War\n2 DATE FROM 16 OCT 1853 TO 30 MAR 1856\n2 SOUR [Wikipedia: Crimean War](https://en.wikipedia.org/wiki/Crimean_War)",
            "1 EVEN The First Boer War aka. The Transvaal Rebellion, Transvaal declared independence from the United Kingdom\n2 TYPE Freedom War\n2 DATE FROM 16 DEC 1880 TO 23 MAR 1881\n2 SOUR [Wikipedia: Boer War (disambiguation)](https://en.wikipedia.org/wiki/First_Boer_War)",
            "1 EVEN The Second Boer War aka. the South African War,  Battle for control of the British Empire over southern Africa\n2 TYPE Freedom War\n2 DATE FROM 11 OCT 1899 TO 31 MAY 1902\n2 SOUR [Wikipedia: Boer War (disambiguation)](https://en.wikipedia.org/wiki/Second_Boer_War)",
            "1 EVEN Games of the IV Olympiad (1908 Summer Olympics)\n2 TYPE Olympic Games\n2 DATE FROM 27 APR 1908 TO 31 OCT 1908\n2 PLAC London, England\n2 SOUR [Wikipedia: 1908 Summer Olympics](https://en.wikipedia.org/wiki/1908_Summer_Olympics)",
            "1 EVEN The National Insurance Act 1911 created National Insurance, originally a system of health insurance for industrial workers in Great Britain\n2 TYPE Universal Health Care\n2 DATE FROM 16 DEC 1911\n2 SOUR [Wikipedia: National Insurance Act 1911](https://en.wikipedia.org/wiki/National_Insurance_Act_1911)",
            "1 EVEN First World War (WWI), contemporaneously known as „the Great War‟ or „the war to end all wars‟\n2 TYPE World War\n2 DATE FROM 28 JUL 1914 TO 11 NOV 1918\n2 SOUR [Wikipedia: World War I](https://en.wikipedia.org/wiki/World_War_I)",
            "1 EVEN Second World War (WWII)\n2 TYPE World War\n2 DATE FROM 01 SEP 1939 TO 02 SEP 1945\n2 SOUR [Wikipedia:  World War II](https://en.wikipedia.org/wiki/World_War_II)",
            "1 EVEN The National Health Service Act 1946 provided free healthcare for all and created the National Health Service\n2 TYPE Universal Health Care\n2 DATE FROM 05 JUL 1948\n2 SOUR [Wikipedia: National Health Service Act 1946](https://en.wikipedia.org/wiki/National_Health_Service_Act_1946)",
            "1 EVEN Games of the XIV Olympiad (1948 Summer Olympics)\n2 TYPE Olympic Games\n2 DATE FROM 29 JUL 1948 TO 14 AUG 1948\n2 PLAC London, England\n2 SOUR [Wikipedia: 1948 Summer Olympics](https://en.wikipedia.org/wiki/1948_Summer_Olympics)",
            "1 EVEN Enlargement to Delors, the United Kingdom (with Gibraltar) eventually joined the European Communities\n2 TYPE Global Politics\n2 DATE 01 JAN 1793\n2 SOUR [Wikipedia: History of the European Union](https://en.wikipedia.org/wiki/History_of_the_European_Union#1973-1993:_Enlargement_to_Delors)",
            "1 EVEN Winter of Discontent, social unrest resulting in mass industrial action, power cuts and a three-day working week\n2 TYPE Social Unrest\n2 DATE FROM OCT 1978 TO FEB 1979\n2 SOUR [Wikipedia: Winter of Discontent](https://en.wikipedia.org/wiki/Winter_of_Discontent)",
            "1 EVEN Games of the XXX Olympiad (2012 Summer Olympics)\n2 TYPE Olympic Games\n2 DATE FROM 27 JUL 2012 TO 12 AUG 2012\n2 PLAC London, England\n2 SOUR [Wikipedia: 2012 Summer Olympics](https://en.wikipedia.org/wiki/2012_Summer_Olympics)",
            "1 EVEN Brexit, the withdrawal of the United Kingdom from the European Union and the European Atomic Energy Community\n2 TYPE Global Politics\n2 DATE 31 JAN 2020\n2 SOUR [Wikipedia: Brexit](https://en.wikipedia.org/wiki/Brexit)",
        ]);
    }
}
