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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\I18N;
use Illuminate\Support\Collection;

/**
 * Class LowCountriesRulers
 */
class LowCountriesRulers extends AbstractModule implements ModuleHistoricEventsInterface
{
    use ModuleHistoricEventsTrait;

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        return 'Machthebbers van de Lage Landen 🇳🇱';
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
     * @return Collection<int,string>
     */
    public function historicEventsAll(): Collection
    {
        switch (I18N::languageTag()) {
            case 'nl':
                return new Collection([
                    "1 EVEN Koningen, graven en potestaten van Friesland\n2 TYPE vorstendom, graafschap, potestaat\n2 DATE FROM 600 TO 1515",
                    "1 EVEN Vorst-abt van Stavelot\n2 TYPE vorstendom\n2 DATE FROM 648 TO 1795",
                    "1 EVEN Graaf-bisschoppen van Utrecht\n2 TYPE bisdom\n2 DATE FROM 695 TO 1528",
                    "1 EVEN Koningen van Lotharingen\n2 TYPE vorstendom\n2 DATE FROM 843 TO 900",
                    "1 EVEN Graven van Vlaanderen\n2 TYPE graafschap\n2 DATE FROM 862 TO 1792",
                    "1 EVEN Graven van Henegouwen\n2 TYPE graafschap\n2 DATE FROM 875 TO 1792",
                    "1 EVEN Graven van Holland\n2 TYPE graafschap\n2 DATE FROM 885 TO 1428",
                    "1 EVEN Hertogen van Lotharingen\n2 TYPE hertogdom\n2 DATE FROM 900 TO 973",
                    "1 EVEN Graven van Namen\n2 TYPE graafschap\n2 DATE FROM 907 TO 1792",
                    "1 EVEN Graven van Loon\n2 TYPE graafschap\n2 DATE FROM 944 TO 1363",
                    "1 EVEN Graven van Chiny\n2 TYPE graafschap\n2 DATE FROM 950 TO 1364",
                    "1 EVEN Hertogen van Neder-Lotharingen\n2 TYPE hertogdom\n2 DATE FROM 959 TO 1792",
                    "1 EVEN Prins-bisschoppen van Luik\n2 TYPE prinsbisdom\n2 DATE FROM 972 TO 1794",
                    "1 EVEN Heren en hertogen van Bouillon\n2 TYPE Heerlijkheid, hertogdom\n2 DATE FROM 988 TO 1974",
                    "1 EVEN Graven, hertogen en groothertogen van Luxemburg\n2 TYPE graafschap, hertogdom, groothertogdom\n2 DATE FROM 988",
                    "1 EVEN Heren van Herpen en Ravenstein\n2 TYPE Heerlijkheid\n2 DATE FROM ABT 1000 TO 1794",
                    "1 EVEN Graven van Brussel en landgraven van Brabant\n2 TYPE graafschap\n2 DATE FROM 1003 TO 1190",
                    "1 EVEN Graven en hertogen van Gulik\n2 TYPE graafschap, hertogdom\n2 DATE FROM 1003 TO 1511",
                    "1 EVEN Graven van Leuven en hertogen van Brabant\n2 TYPE graafschap, hertogdom\n2 DATE FROM 1003 TO 1792",
                    "1 EVEN Graaf-bisschoppen van Kamerijk\n2 TYPE bisdom\n2 DATE FROM 1007 TO 1559",
                    "1 EVEN Heren en graven van Zutphen\n2 TYPE graafschap\n2 DATE FROM 1018 TO 1182",
                    "1 EVEN Graven en hertogen van Limburg\n2 TYPE hertogdom\n2 DATE FROM 1033 TO 1792",
                    "1 EVEN Graven en hertogen van Gelre\n2 TYPE graafschap, hertogdom\n2 DATE FROM 1046 TO 1543",
                    "1 EVEN Rijksheren, rijksbaronnen en rijksgraven van Rekem\n2 TYPE ministaat\n2 DATE FROM ABT 1200 TO 1795",
                    "1 EVEN Heren en vrouwen van Boxmeer\n2 TYPE heerlijkheid\n2 DATE FROM 1269 TO 1797",
                    "1 EVEN Hertogen van Bourgondië\n2 TYPE vorstendom\n2 DATE FROM 1363 TO 1506",
                    "1 EVEN Stadhouders van de Nederlanden\n2 TYPE republiek\n2 DATE FROM 1433 TO 1795",
                    "1 EVEN Staten-Generaal van de Nederlanden\n2 TYPE republiek\n2 DATE FROM 1464 TO 1795",
                    "1 EVEN Landvoogden van de Zeventien Provinciën\n2 TYPE politieke unie\n2 DATE FROM 1507 TO 1588",
                    "1 EVEN Landvoogden van de Zuidelijke Nederlanden\n2 TYPE vorstendom\n2 DATE FROM 1588 TO 1794",
                    "1 EVEN Gewestelijke Staten van de Republiek der Zeven Verenigde Nederlanden\n2 TYPE republiek\n2 DATE FROM 1588 TO 1795",
                    "1 EVEN Nationale Conventie, Directoire en Frans Consulaat van de Eerste Franse Republiek\n2 TYPE republiek\n2 DATE FROM 1795 TO 1804",
                    "1 EVEN Nationale Vergadering van de Bataafse Republiek\n2 TYPE republiek\n2 DATE FROM 1796 TO 1798",
                    "1 EVEN Uitvoerend Bewind van de Bataafse Republiek\n2 TYPE republiek\n2 DATE FROM 1798 TO 1801",
                    "1 EVEN Staatsbewind van het Bataafs Gemenebest\n2 TYPE republiek\n2 DATE FROM 1801 TO 1805",
                    "1 EVEN Keizer van het Keizerrijk Frankrijk\n2 TYPE keizerrijk\n2 DATE FROM 1804 TO 1813",
                    "1 EVEN Raadpensionarissen van het Bataafs Gemenebest\n2 TYPE republiek\n2 DATE FROM 1805 TO 1806",
                    "1 EVEN Koningen van het Koninkrijk Holland\n2 TYPE koninkrijk\n2 DATE FROM 1806 TO 1810",
                    "1 EVEN Driemanschap van 1813\n2 TYPE voorlopig bewind\n2 DATE FROM 1813 TO 1813",
                    "1 EVEN Soeverein der Verenigde Nederlanden\n2 TYPE vorstendom\n2 DATE FROM 1813 TO 1815",
                    "1 EVEN Koning van het Verenigd Koninkrijk der Nederlanden\n2 TYPE koninkrijk\n2 DATE FROM 1815 TO 1831",
                    "1 EVEN Staatshoofden van Neutraal Moresnet\n2 TYPE dwergstaatje\n2 DATE FROM 1816 TO 1920",
                    "1 EVEN Baron-regent van België\n2 TYPE koninkrijk\n2 DATE FROM 1830 TO 1831",
                    "1 EVEN Koningen der Belgen\n2 TYPE koninkrijk\n2 DATE FROM 1831",
                    "1 EVEN Koningen der Nederlanden\n2 TYPE koninkrijk\n2 DATE FROM 1831",
                    "1 EVEN Staten-Generaal van het Koninkrijk der Nederlanden\n2 TYPE koninkrijk\n2 DATE FROM 1848",
                    "1 EVEN Raad van State\n2 TYPE koninkrijk\n2 DATE FROM 1889 TO 1890",
                    "1 EVEN Koningin-regentes van Nederland\n2 TYPE koninkrijk\n2 DATE FROM 1890 TO 1898",
                    "1 EVEN Prins-regent van België\n2 TYPE koninkrijk\n2 DATE FROM 1944 TO 1950",
                ]);

            default:
                return new Collection();
        }
    }
}
