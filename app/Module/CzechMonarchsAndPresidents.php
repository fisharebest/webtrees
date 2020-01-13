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

use Illuminate\Support\Collection;

/**
 * Class CzechLeaders
 */
class CzechLeaders extends AbstractModule implements ModuleHistoricEventsInterface
{
    use ModuleHistoricEventsTrait;

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        return 'British prime ministers';
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
            // Panovníci:
            "1 EVEN Ferdinand I.\n2 TYPE Král\n2 DATE FROM 1526 TO 1564\n2 NOTE současně král uherský a římský, od r. 1556 císař",
            "1 EVEN Maxmilián I./II.\n2 TYPE Král\n2 DATE FROM 1564 TO 1576\n2 NOTE současně král uherský a císař římský, jako císař Maxmilián II.",
            "1 EVEN Rudolf II.\n2 TYPE Král\n2 DATE FROM 1576 TO 1611\n2 NOTE zároveň král uherský a císař římský. Od r. 1608 vládl jen v Čechách a ve Slezsku. Císař do své smrti v roce 1612,",
            "1 EVEN Matyáš I./II.\n2 TYPE Král\n2 DATE FROM 1611 TO 1619\n2 NOTE reálně vládl do r. 1618. Současně král uherský a císař římský od r. 1612. Jako král uherský Matyáš II.",
            "1 EVEN Fridrich Falcký\n2 TYPE Král\n2 DATE FROM 1619 TO 1620",
            "1 EVEN Ferdinand II. Štýrský\n2 TYPE Král\n2 DATE FROM 1620 TO 1637\n2 NOTE Současně král uherský a císař římský. Reálně vládl od r. 1620.",
            "1 EVEN Ferdinand III.\n2 TYPE Král\n2 DATE FROM 1637 TO 1657\n2 NOTE současně král uherský a císař římský",
            "1 EVEN Ferdinand IV.\n2 TYPE Král\n2 DATE FROM 1646 TO 1654\n2 NOTE spoluvladař Ferdinanda III., svého otce. Od r. 1647 také král uherský. Reálně nevládl, zemřel za života svého otce.",
            "1 EVEN Leopold I.\n2 TYPE Král\n2 DATE FROM 1657 TO 1705\n2 NOTE současně král uherský a císař římský",
            "1 EVEN Josef I.\n2 TYPE Král\n2 DATE FROM 1705 TO 1711\n2 NOTE současně král uherský a císař římský",
            "1 EVEN Karel II./VI.\n2 TYPE Král\n2 DATE FROM 1711 TO 1740\n2 NOTE jako císař římský Karel VI. Současně král uherský",
            "1 EVEN Vymření habsburské dynastie po meči\n2 TYPE Dynastie\n2 DATE 1740",
            "1 EVEN Marie Terezie\n2 TYPE Královna\n2 DATE FROM 1740 TO 1780\n2 NOTE současně královna uherská. Ženská linie habsburské dynastie.",
            "1 EVEN Karel Albrecht / Karel III.\n2 TYPE Panovník\n2 DATE FROM 1741 TO 1742\n2 NOTE kurfiřt bavorský, císař 1742 – 1745. Vzdorokrál.",
            "1 EVEN Josef II.\n2 TYPE Král\n2 DATE FROM 1780 TO 1790\n2 NOTE současně král uherský, císař od r. 1765",
            "1 EVEN Leopold II.\n2 TYPE Král\n2 DATE FROM 1790 TO 1792\n2 NOTE současně král uherský a císař římský",
            "1 EVEN František II.\n2 TYPE Král\n2 DATE FROM 1792 TO 1835\n2 NOTE současně král uherský, císař římský do r. 1806, od r. 1804 císař rakouský jako František I.",
            "1 EVEN Ferdinand V.\n2 TYPE Král\n2 DATE FROM 1835 TO 1848\n2 NOTE současně král uherský a císař rakouský jako Ferdinand I.",
            "1 EVEN František Josef I.\n2 TYPE Král\n2 DATE FROM 1848 TO 1916\n2 NOTE současně král uherský a císař rakouský",
            "1 EVEN Karel\n2 TYPE Král\n2 DATE FROM 1916 TO 1918\n2 NOTE jako císař rakouský Karel I., jako král český a král uherský Karel/Karoly IV.",
            // Prezidenti:
            "1 EVEN Tomáš Garrigue Masaryk\n2 TYPE Prezident ČSR\n2 DATE FROM 14 NOV 1918 TO 14 DEC 1935",
            "1 EVEN Edvard Beneš\n2 TYPE Prezident ČSR\n2 DATE FROM 18 DEC 1935 TO 5 OCT 1938",
            "1 EVEN Emil Hácha\n2 TYPE Státní prezident\n2 DATE FROM 30 NOV 1938 TO 9 MAY 1945\n2 NOTE Wikipedie [Emil Hácha](https://cs.wikipedia.org/wiki/Emil_H%C3%A1cha)",
            "1 EVEN Edvard Beneš\n2 TYPE Prezident ČSR\n2 DATE FROM 02 APR 1945 TO 7 JUN 1948",
            "1 EVEN Klement Gottwald\n2 TYPE Prezident ČSR\n2 DATE FROM 14 JUN 1948 TO 14 MAR 1953\n2 NOTE První dělnický prezident, jak říkali komunisti",
            "1 EVEN Antonín Zápotocký\n2 TYPE Prezident ČSR\n2 DATE FROM 21 MAR 1953 TO 13 NOV 1957",
            "1 EVEN Antonín Novotný\n2 TYPE Prezident ČSR/ČSSR\n2 DATE FROM 19 NOV 1957 TO 28 MAR 1968",
            "1 EVEN Ludvík Svoboda\n2 TYPE Prezident ČSSR\n2 DATE FROM 30 MAR 1968 TO 28 MAY 1975",
            "1 EVEN Gustáv Husák\n2 TYPE Prezident ČSSR\n2 DATE FROM 29 MAY 1975 TO 10 DEC 1989",
            "1 EVEN Václav Havel\n2 TYPE Prezident ČR\n2 DATE FROM 29 DEC 1989 TO 2 FEB 2003",
            "1 EVEN Václav Klaus\n2 TYPE Prezident ČR\n2 DATE FROM 07 MAR 2003 TO 4 MAR 2013",
            "1 EVEN Miloš Zeman\n2 TYPE Prezident ČR\n2 DATE 08 MAR 2013",
        ]);
    }
}
