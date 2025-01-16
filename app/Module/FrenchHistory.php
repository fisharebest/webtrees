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
 *
 * Original source : https://www.lorand.org/spip.php?article194
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\I18N;
use Illuminate\Support\Collection;

class FrenchHistory extends AbstractModule implements ModuleHistoricEventsInterface
{
    use ModuleHistoricEventsTrait;

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        return 'Faits historiques de France 🇫🇷';
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
     * @return Collection<int,string>
     */
    public function historicEventsAll(string $language_tag): Collection
    {
        switch ($language_tag) {
            case 'fr':
            case 'fr-CA':
                return new Collection([
                    # Famines
                    "1 EVEN Grands froids suivis de famine et d’épidémies en France\n2 TYPE Famine\n2 DATE FROM 974 TO 975\n2 NOTE un tiers de la population française",
                    "1 EVEN Famine et peste en France\n2 TYPE Famine\n2 DATE 1097\n2 NOTE 100 000 morts",
                    "1 EVEN Famine au Languedoc\n2 TYPE Famine\n2 DATE 1528",
                    "1 EVEN Famine dans l’est de la France\n2 TYPE Famine\n2 DATE FROM 1650 TO 1652",
                    "1 EVEN Famine en France\n2 TYPE Famine\n2 DATE FROM 1693 TO 1694\n2 NOTE  2 millions de morts",
                    "1 EVEN Famines entrainant la révolution française\n2 TYPE Famine\n2 DATE 1788",
                    # Révolutions
                    "1 EVEN Révolution française\n2 TYPE Révolution\n2 DATE FROM 5 MAY 1789 TO 9 NOV 1799",
                    # Guerres civiles
                    "1 EVEN Guerre civile d’Espagne\n2 TYPE Guerre civile\n2 DATE 17 JUL 1936\n2 NOTE https://fr.wikipedia.org/wiki/Guerre_d%27Espagne",
                    # Guerres
                    "1 EVEN Guerre franco-allemande\n2 TYPE Guerre\n2 DATE FROM 19 JUL 1870 TO 29 JAN 1871\n2 NOTE https://fr.wikipedia.org/wiki/Guerre_franco-allemande_de_1870",
                    "1 EVEN Première Guerre mondiale\n2 TYPE Guerre\n2 DATE FROM 1 AUG 1914 TO 11 NOV 1918\n2 NOTE https://fr.wikipedia.org/wiki/Premi%C3%A8re_Guerre_mondiale",
                    "1 EVEN Seconde Guerre mondiale\n2 TYPE Guerre\n2 DATE FROM 3 SEP 1939 TO 8 MAY 1945\n2 NOTE https://fr.wikipedia.org/wiki/Seconde_Guerre_mondiale",
                    # Présidents de France
                    "1 EVEN Louis-Napoléon Bonaparte\n2 TYPE 1er président de la République Française\n2 DATE 10 DEC 1848",
                    "1 EVEN Adolphe Thiers\n2 TYPE 2ème président de la République Française\n2 DATE 31 AUG 1871",
                    "1 EVEN Patrice de Mac-Mahon\n2 TYPE 3ème président de la République Française\n2 DATE 24 MAY 1873",
                    "1 EVEN Jules Grévy\n2 TYPE 4ème président de la République Française\n2 DATE 30 JAN 1879",
                    "1 EVEN Sadi Carnot\n2 TYPE 5ème président de la République Française\n2 DATE 3 DEC 1887",
                    "1 EVEN Jean Casimir-Perier\n2 TYPE 6ème président de la République Française\n2 DATE 27 JUN 1894",
                    "1 EVEN Félix Faure\n2 TYPE 7ème président de la République Française\n2 DATE 17 JAN 1895",
                    "1 EVEN Emile Loubet\n2 TYPE 8ème président de la République Française\n2 DATE 18 FEB 1899",
                    "1 EVEN Armand Fallières\n2 TYPE 9ème président de la République Française\n2 DATE 18 FEB 1906",
                    "1 EVEN Raymond Poincaré\n2 TYPE 10ème président de la République Française\n2 DATE 18 FEB 1913",
                    "1 EVEN Paul Deschanel\n2 TYPE 11ème président de la République Française\n2 DATE 18 FEB 1920",
                    "1 EVEN Alexandre Millerand\n2 TYPE 12ème président de la République Française\n2 DATE 23 SEP 1920",
                    "1 EVEN Gaston Doumergue\n2 TYPE 13ème président de la République Française\n2 DATE 13 JUN 1924",
                    "1 EVEN Paul Doumer\n2 TYPE 14ème président de la République Française\n2 DATE 13 JUN 1931",
                    "1 EVEN Albert Lebrun\n2 TYPE 15ème président de la République Française\n2 DATE 10 MAY 1932",
                    "1 EVEN Vincent Auriol\n2 TYPE 16ème président de la République Française\n2 DATE 16 JAN 1947",
                    "1 EVEN René Coty\n2 TYPE 17ème président de la République Française\n2 DATE 16 JAN 1954",
                    "1 EVEN Charles de Gaulle\n2 TYPE 18ème président de la République Française\n2 DATE 8 JAN 1959",
                    "1 EVEN Georges Pompidou\n2 TYPE 19ème président de la République Française\n2 DATE 20 JUN 1969",
                    "1 EVEN Valéry Giscard d’Estaing\n2 TYPE 20ème président de la République Française\n2 DATE 27 MAY 1974",
                    "1 EVEN François Mitterrand\n2 TYPE 21ème président de la République Française\n2 DATE 21 MAY 1981",
                    "1 EVEN Jacques Chirac\n2 TYPE 22ème président de la République Française\n2 DATE 17 MAY 1995",
                    "1 EVEN Nicolas Sarkozy\n2 TYPE 23ème président de la République Française\n2 DATE 16 MAY 2007",
                    "1 EVEN François Hollande\n2 TYPE 24ème président de la République Française\n2 DATE 15 MAY 2012",
                    "1 EVEN Emmanuel Macron\n2 TYPE 25ème président de la République Française\n2 DATE 14 MAY 2017",
                    # Rois de France
                    "1 EVEN Hugues Ier Capet\n2 TYPE Roi de France\n2 DATE 3 JUL 987",
                    "1 EVEN Robert II le Pieux\n2 TYPE Roi de France\n2 DATE 24 OCT 996",
                    "1 EVEN Henri Ier\n2 TYPE Roi de France\n2 DATE 20 JUL 1031",
                    "1 EVEN Philippe Ier\n2 TYPE Roi de France\n2 DATE 4 AUG 1060",
                    "1 EVEN Louis VI le Gros\n2 TYPE Roi de France\n2 DATE 22 JUL 1108",
                    "1 EVEN Louis VII le Jeune\n2 TYPE Roi de France\n2 DATE 1 AUG 1137",
                    "1 EVEN Philippe II Auguste\n2 TYPE Roi de France\n2 DATE 18 SEP 1180",
                    "1 EVEN Louis VIII le Lion\n2 TYPE Roi de France\n2 DATE 14 JUL 1223",
                    "1 EVEN Louis IX Saint Louis\n2 TYPE Roi de France\n2 DATE 8 NOV 1226",
                    "1 EVEN Philippe III le Hardi\n2 TYPE Roi de France\n2 DATE 25 AUG 1270",
                    "1 EVEN Philippe IV le Bel\n2 TYPE Roi de France\n2 DATE 5 OCT 1285",
                    "1 EVEN Louis X le Hutin\n2 TYPE Roi de France\n2 DATE 29 NOV 1314",
                    "1 EVEN Jean Ier le Posthume\n2 TYPE Roi de France\n2 DATE 5 JUN 1315\n2 NOTE né le 15 novembre 1316",
                    "1 EVEN Philippe V le Long\n2 TYPE Roi de France\n2 DATE 19 NOV 1316",
                    "1 EVEN Charles IV le Bel\n2 TYPE Roi de France\n2 DATE 3 JAN 1322",
                    "1 EVEN Philippe VI de Valois\n2 TYPE Roi de France\n2 DATE 1 FEB 1328",
                    "1 EVEN Jean II le Bon\n2 TYPE Roi de France\n2 DATE 22 AUG 1350",
                    "1 EVEN Charles V le Sage\n2 TYPE Roi de France\n2 DATE 8 APR 1364",
                    "1 EVEN Charles VI le Fol\n2 TYPE Roi de France\n2 DATE 16 SEP 1380",
                    "1 EVEN Charles VII le Victorieux\n2 TYPE Roi de France\n2 DATE 21 OCT 1422",
                    "1 EVEN Louis XI le Prudent\n2 TYPE Roi de France\n2 DATE 22 JUL 1461",
                    "1 EVEN Charles VIII l’Affable\n2 TYPE Roi de France\n2 DATE 30 AUG 1483",
                    "1 EVEN Louis XII le Père du Peuple\n2 TYPE Roi de France\n2 DATE 7 APR 1498",
                    "1 EVEN François Ier\n2 TYPE Roi de France\n2 DATE 1 JAN 1515",
                    "1 EVEN Henri II\n2 TYPE Roi de France\n2 DATE 1 MAR 1547",
                    "1 EVEN François II\n2 TYPE Roi de France\n2 DATE 10 JUL 1559",
                    "1 EVEN Charles IX\n2 TYPE Roi de France\n2 DATE 5 DEC 1560",
                    "1 EVEN Henri III\n2 TYPE Roi de France\n2 DATE 30 MAY 1574",
                    "1 EVEN Henri IV le Grand\n2 TYPE Roi de France\n2 DATE 1 AUG 1589",
                    "1 EVEN Louis XIII le Juste\n2 TYPE Roi de France\n2 DATE 14 MAY 1610",
                    "1 EVEN Louis XIV le Grand\n2 TYPE Roi de France\n2 DATE 14 MAY 1643",
                    "1 EVEN Louis XV le Bien-Aimé\n2 TYPE Roi de France\n2 DATE 1 SEP 1715",
                    "1 EVEN Louis XVI\n2 TYPE Roi de France\n2 DATE 10 MAY 1774",
                    "1 EVEN Louis XVII\n2 TYPE Roi de France\n2 DATE 6 APR 1814",
                    "1 EVEN Louis XVIII\n2 TYPE Roi de France\n2 DATE 22 JUN 1815",
                    "1 EVEN Charles X\n2 TYPE Roi de France\n2 DATE 16 SEP 1824",
                    "1 EVEN Louis-Philippe Ier\n2 TYPE Roi des Français\n2 DATE 9 AUG 1830\n2 NOTE dernier roi de France",
                    # Empereurs de France
                    "1 EVEN Napoléon Ier\n2 TYPE Empereur des Français\n2 DATE 18 MAY 1804",
                    "1 EVEN Napoléon III\n2 TYPE Empereur des Français\n2 DATE 4 DEC 1852",
                    # Régimes politiques
                    "1 EVEN Monarchie Constitutionnelle\n2 TYPE Régime politique\n2 DATE 4 SEP 1791",
                    "1 EVEN Ire République\n2 TYPE Régime politique\n2 DATE 22 SEP 1792",
                    "1 EVEN Directoire\n2 TYPE Régime politique\n2 DATE 26 OCT 1795",
                    "1 EVEN Consulat\n2 TYPE Régime politique\n2 DATE 9 NOV 1799",
                    "1 EVEN Cent-Jours\n2 TYPE Régime politique\n2 DATE 22 MAR 1815",
                    "1 EVEN Gouvernement Provisoire de 1848\n2 TYPE Régime politique\n2 DATE 25 FEB 1848",
                    "1 EVEN IIe République\n2 TYPE Régime politique\n2 DATE 4 NOV 1848",
                    "1 EVEN IIIe République\n2 TYPE Régime politique\n2 DATE 4 SEP 1870",
                    "1 EVEN Gouvernement de Vichy\n2 TYPE Régime politique\n2 DATE 11 JUL 1940",
                    "1 EVEN Gouvernement Provisoire de la République Française\n2 TYPE Régime politique\n2 DATE 2 JUN 1944",
                    "1 EVEN IVe République\n2 TYPE Régime politique\n2 DATE 24 OCT 1946",
                    "1 EVEN Ve République\n2 TYPE Régime politique\n2 DATE 5 OCT 1958",
                    # Événements politiques
                    "1 EVEN Naissance du Parti Communiste Français\n2 TYPE Parti Communiste Français\n2 DATE 25 DEC 1920\n2 NOTE Congrès de Tours",
                    "1 EVEN Victoire du Peuple\n2 TYPE Front populaire\n2 DATE 03 MAY 1936\n2 NOTE https://fr.wikipedia.org/wiki/Front_populaire_%28France%29",
                    # Religieux
                    "1 EVEN Loi du 9 décembre 1905 relative à la séparation des Églises et de l’État\n2 TYPE Séparation des Églises et de l’État Française\n2 DATE 09 DEC 1905",
                    # Événements en Quercy
                    "1 EVEN Épidémie de peste en Quercy\n2 TYPE Histoire du Quercy\n2 DATE 1593",
                    "1 EVEN Les actes publics doivent être rédigés en français\n2 TYPE Histoire du Quercy\n2 DATE 1541",
                    "1 EVEN Décès de Clément Marot, poète et écrivain lotois, à Turin\n2 TYPE Histoire du Quercy\n2 DATE 1544",
                ]);

            default:
                return new Collection();
        }
    }
}
