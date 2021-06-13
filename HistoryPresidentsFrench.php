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
 * Class HistoryPresidentsFrench
 */
class HistoryPresidentsFrench extends AbstractModule implements ModuleHistoricEventsInterface
{
    use ModuleHistoricEventsTrait;

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        return 'Les présidents de la République Française 🇫🇷';
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
            "1 EVEN Louis-Napoléon Bonaparte (1808 — 1873)\n2 TYPE 1er président de la République Française\n2 DATE FROM 20 DEC 1848 TO 02 DEC 1852\n2 NOTE Bonapartiste\n2 SOUR [Wikipedia: Liste des présidents de la République française](https://fr.wikipedia.org/wiki/Napoléon_III)",
            "1 EVEN Adolphe Thiers (1797 — 1877)\n2 TYPE 2ème président de la République Française\n2 DATE FROM 31 AUG 1871 TO 24 MAY 1873\n2 NOTE Orléaniste puis républicain\n2 SOUR [Wikipedia: Liste des présidents de la République française](https://fr.wikipedia.org/wiki/Adolphe_Thiers)",
            "1 EVEN Patrice de Mac-Mahon (1808 — 1893)\n2 TYPE 3ème président de la République Française\n2 DATE FROM 24 MAY 1873 TO 30 JAN 1879\n2 NOTE Légitimiste\n2 SOUR [Wikipedia: Liste des présidents de la République française](https://fr.wikipedia.org/wiki/Patrice_de_Mac_Mahon)",
            "1 EVEN Jules Grévy (1807 — 1891)\n2 TYPE 4ème président de la République Française\n2 DATE FROM 30 JAN 1879 TO 02 DEC 1887\n2 NOTE Républicain modéré\n2 SOUR [Wikipedia: Liste des présidents de la République française](https://fr.wikipedia.org/wiki/Jules_Grévy)",
            "1 EVEN Sadi Carnot (1837 — 1894)\n2 TYPE 5ème président de la République Française\n2 DATE FROM 3 DEC 1887 TO 25 JUN 1894\n2 NOTE Républicain modéré\n2 SOUR [Wikipedia: Liste des présidents de la République française](https://fr.wikipedia.org/wiki/Sadi_Carnot_(homme_d'État))",
            "1 EVEN Jean Casimir-Perier (1847 — 1907)\n2 TYPE 6ème président de la République Française\n2 DATE FROM 27 JUN 1894 TO 16 JAN 1895\n2 NOTE Républicain modéré\n2 SOUR [Wikipedia: Liste des présidents de la République française](https://fr.wikipedia.org/wiki/Jean_Casimir-Perier)",
            "1 EVEN Félix Faure (1841 — 1899)\n2 TYPE 7ème président de la République Française\n2 DATE FROM 17 JAN 1895 TO 16 FEB 1899\n2 NOTE Républicain modéré\n2 SOUR [Wikipedia: Liste des présidents de la République française](https://fr.wikipedia.org/wiki/Félix_Faure)",
            "1 EVEN Emile Loubet (1838 — 1929)\n2 TYPE 8ème président de la République Française\n2 DATE FROM 18 FEB 1899 TO 18 FEB 1906\n2 NOTE Républicain modéré (1899-1901), ARD (1901-1906)\n2 SOUR [Wikipedia: Liste des présidents de la République française](https://fr.wikipedia.org/wiki/Émile_Loubet)",
            "1 EVEN Armand Fallières (1841 — 1931)\n2 TYPE 9ème président de la République Française\n2 DATE FROM 18 FEB 1906 TO 18 FEB 1913\n2 NOTE ARD (1906-1910), PRD (1910-1913)\n2 SOUR [Wikipedia: Liste des présidents de la République française](https://fr.wikipedia.org/wiki/Armand_Fallières)",
            "1 EVEN Raymond Poincaré (1860 — 1934)\n2 TYPE 10ème président de la République Française\n2 DATE FROM 18 FEB 1913 TO 18 FEB 1920\n2 NOTE PRD (1913-1917), ARD (1917-1920)\n2 SOUR [Wikipedia: Liste des présidents de la République française](https://fr.wikipedia.org/wiki/Raymond_Poincaré)",
            "1 EVEN Paul Deschanel (1855 — 1922)\n2 TYPE 11ème président de la République Française\n2 DATE FROM 18 FEB 1920 TO 21 SEP 1920\n2 NOTE ARD, PRDS\n2 SOUR [Wikipedia: Liste des présidents de la République française](https://fr.wikipedia.org/wiki/Paul_Deschanel)",
            "1 EVEN Alexandre Millerand (1859 — 1943)\n2 TYPE 12ème président de la République Française\n2 DATE FROM 23 SEP 1920 TO 11 JUN 1924\n2 NOTE Indépendant\n2 SOUR [Wikipedia: Liste des présidents de la République française](https://fr.wikipedia.org/wiki/Alexandre_Millerand)",
            "1 EVEN Gaston Doumergue (1863 — 1937)\n2 TYPE 13ème président de la République Française\n2 DATE FROM 13 JUN 1924 TO 13 JUN 1931\n2 NOTE RAD\n2 SOUR [Wikipedia: Liste des présidents de la République française](https://fr.wikipedia.org/wiki/Gaston_Doumergue)",
            "1 EVEN Paul Doumer (1857 — 1932)\n2 TYPE 14ème président de la République Française\n2 DATE FROM 13 JUN 1931 TO 07 MAY 1932\n2 NOTE Indépendant\n2 SOUR [Wikipedia: Liste des présidents de la République française](https://fr.wikipedia.org/wiki/Paul_Doumer)",
            "1 EVEN Albert Lebrun (1871 — 1950)\n2 TYPE 15ème président de la République Française\n2 DATE FROM 10 MAY 1932 TO 11 JUN 1940\n2 NOTE AD\n2 SOUR [Wikipedia: Liste des présidents de la République française](https://fr.wikipedia.org/wiki/Albert_Lebrun)",
            "1 EVEN Seconde Guerre mondiale\n2 DATE FROM 11 JUN 1940 TO 16 JAN 1947\n2 Après l'invasion du pays par l'armée allemande, le président Albert Lebrun nomme à la présidence du Conseil le maréchal Pétain. Après la libération de Paris, en août 1944, le général de Gaulle exerce à son tour les fonctions de président du Gouvernement provisoire de la République française. Il démissionne en 1946. Lui succèdent Félix Gouin et Georges Bidault.",
            "1 EVEN Vincent Auriol (1884 — 1966)\n2 TYPE 16ème président de la République Française\n2 DATE FROM 16 JAN 1947 TO 16 JAN 1954\n2 NOTE SFIO\n2 SOUR [Wikipedia: Liste des présidents de la République française](https://fr.wikipedia.org/wiki/Vincent_Auriol)",
            "1 EVEN René Coty (1882 — 1962)\n2 TYPE 17ème président de la République Française\n2 DATE FROM 16 JAN 1954 TO 08 JAN 1959\n2 NOTE CNIP\n2 SOUR [Wikipedia: Liste des présidents de la République française](https://fr.wikipedia.org/wiki/René_Coty)",
            "1 EVEN Charles de Gaulle (1890 — 1970)\n2 TYPE 18ème président de la République Française\n2 DATE FROM 08 JAN 1959 TO 28 APR 1969\n2 NOTE UNR-DU (1962-1967), DU (1967-1968), UDR (1968-1969)\n2 SOUR [Wikipedia: Liste des présidents de la République française](https://fr.wikipedia.org/wiki/Charles_de_Gaulle)",
            "1 EVEN Alain Poher (1909 — 1996)\n2 TYPE Président de la République par intérim\n2 DATE FROM 28 APR 1969 TO 20 JUN 1969\n2 NOTE CD\n2 SOUR [Wikipedia: Liste des présidents de la République française](https://fr.wikipedia.org/wiki/Alain_Poher)",
            "1 EVEN Georges Pompidou (1911 — 1974)\n2 TYPE 19ème président de la République Française\n2 DATE FROM 02 APR 1974 TO 02 APR 1974\n2 NOTE UDR\n2 SOUR [Wikipedia: Liste des présidents de la République française](https://fr.wikipedia.org/wiki/Georges_Pompidou)",
            "1 EVEN Alain Poher (1909 — 1996)\n2 TYPE Président de la République par intérim\n2 DATE FROM 28 APR 1969 TO 27 MAY 1974\n2 NOTE CD\n2 SOUR [Wikipedia: Liste des présidents de la République française](https://fr.wikipedia.org/wiki/Alain_Poher)",
            "1 EVEN Valéry Giscard d’Estaing (1926 — 2020)\n2 TYPE 20ème président de la République Française\n2 DATE FROM 27 MAY 1974 TO 21 MAY 1981\n2 NOTE FNRI (1974-1977), UDF-PR (1977-1981)\n2 SOUR [Wikipedia: Liste des présidents de la République française](https://fr.wikipedia.org/wiki/Valéry_Giscard_d'Estaing)",
            "1 EVEN François Mitterrand (1916 — 1996)\n2 TYPE 21ème président de la République Française\n2 DATE FROM 21 MAY 1981 TO 17 MAY 1995\n2 NOTE PS\n2 SOUR [Wikipedia: Liste des présidents de la République française](https://fr.wikipedia.org/wiki/François_Mitterrand)",
            "1 EVEN Jacques Chirac (1932 — 2019)\n2 TYPE 22ème président de la République Française\n2 DATE FROM 17 MAY 1995 TO 16 MAY 2007\n2 NOTE RPR (1995-2002), UMP (2002-2007)\n2 SOUR [Wikipedia: Liste des présidents de la République française](https://fr.wikipedia.org/wiki/Jacques_Chirac)",
            "1 EVEN Nicolas Sarkozy (* 1955)\n2 TYPE 23ème président de la République Française\n2 DATE FROM 16 MAY 2007 TO 15 MAY 2012\n2 NOTE UMP\n2 SOUR [Wikipedia: Liste des présidents de la République française](https://fr.wikipedia.org/wiki/Nicolas_Sarkozy)",
            "1 EVEN François Hollande (* 1954)\n2 TYPE 24ème président de la République Française\n2 DATE FROM 15 MAY 2012 TO 14 MAY 2017\n2 NOTE PS\n2 SOUR [Wikipedia: Liste des présidents de la République française](https://fr.wikipedia.org/wiki/François_Hollande)",
            "1 EVEN Emmanuel Macron (* 1977)\n2 TYPE 25ème président de la République Française\n2 DATE FROM 14 MAY 2017\n2 NOTE LREM\n2 SOUR [Wikipedia: Liste des présidents de la République française](https://fr.wikipedia.org/wiki/Emmanuel_Macron)",
        ]);
    }
}
