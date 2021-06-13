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
 * Class HistoryFactsAustrian
 */
class HistoryFactsAustrian extends AbstractModule implements ModuleHistoricEventsInterface
{
    use ModuleHistoricEventsTrait;

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        return 'Historische Ereignisse Österreich 🇦🇹';
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
            "1 EVEN Feldzug Karls des Großen gegen die Awaren\n2 DATE @#DJULIAN@ 791\n2 TYPE Österreichische Geschichte\n2 NOTE Im Jahr 791 führte Karl der Große einen ersten misslungenen Feldzug gegen die Awaren, konnte sie aber dennoch bis zum Wienerwald zurückdrängen und fränkische Stützpunkte in Comagena (Tulln) und Aelium Cetium (St. Pölten) errichten.\n2 SOUR [Wikipedia: Geschichte Österreichs](https://de.wikipedia.org/wiki/Geschichte_Österreichs#mittelalter_(bis_976))",
            "1 EVEN Belehnung Luitpolds aus dem Geschlecht der Babenberger mit der Marcha orientalis\n2 DATE @#DJULIAN@ 976\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia: Geschichte Österreichs](https://de.wikipedia.org/wiki/Geschichte_Österreichs#Markgrafschaft_Österreich_(976-1156))",
            "1 EVEN Älteste bekannte schriftliche Nennung des Namens Ostarrichi\n2 DATE @#DJULIAN@ 996\n2 TYPE Österreichische Geschichte\n2 NOTE Die älteste bekannte schriftliche Nennung des Namens „Ostarrichi“, aus dem der spätere Name für Österreich entstanden ist, stammt vom 1. November 996 aus der in Bruchsal ausgefertigten Schenkungs-Urkunde des römisch-deutschen Kaisers Ottos III. gerichtet an den Bischof von Freising Gottschalk von Hagenau, der Ostarrichi-Urkunde.\n2 SOUR [Wikipedia: Ostarrichi](https://de.wikipedia.org/wiki/Ostarrichi)",
            "1 EVEN Erhebung Österreichs zum Herzogtum\n2 DATE @#DJULIAN@ 1156\n2 TYPE Österreichische Geschichte\n2 NOTE Im Zuge des Konfliktes zwischen den Staufern und den Welfen kam 1139 das Herzogtum Bayern an die Babenberger. Als Friedrich I. Barbarossa diesen Streit beenden wollte, gab er den Welfen das Herzogtum Bayern zurück - gleichsam als Entschädigung wurde Österreich mit dem Privilegium minus von 1156 zum Herzogtum des Heiligen Römischen Reiches erhoben.\n2 SOUR [Wikipedia: Geschichte Österreichs](https://de.wikipedia.org/wiki/Geschichte_Österreichs#Herzogtum_Österreich_unter_den_Babenbergern_(1156-1246))",
            "1 EVEN Tod des letzten Babenbergers\n2 DATE @#DJULIAN@ 1246\n2 TYPE Österreichische Geschichte\n2 NOTE Im Kampf gegen Ungarn starb Friedrich II. in der Schlacht an der Leitha. Mit ihm starben die Babenberger in männlicher Linie aus. Es begann die als „österreichisches Interregnum“ bezeichnete Periode, während der die Länder Friedrichs II. in ein länger andauerndes Kräftespiel rivalisierender Mächte gerieten.\n2 SOUR [Wikipedia: Geschichte Österreichs](https://de.wikipedia.org/wiki/Geschichte_Österreichs#Herzogtum_Österreich_unter_den_Babenbergern_(1156-1246))",
            "1 EVEN Habsburger werden Herzöge von Österreich und der Steiermark\n2 DATE @#DJULIAN@ 1278\n2 TYPE Österreichische Geschichte\n2 NOTE Rudolf von Habsburg besiegte den König von Böhmen, Ottokar II. Přemysl, in der Schlacht auf dem Marchfeld. Die Habsburger konnten sich daraufhin als Herzöge von Österreich und der Steiermark etablieren und sollten hier bis 1918, also 640 Jahre lang herrschen.\n2 SOUR [Wikipedia: Geschichte Österreichs](https://de.wikipedia.org/wiki/Geschichte_Österreichs#Herzogtum_Österreich_als_Streitobjekt_(1246-1282))",
            "1 EVEN Kärnten und Krain fällt an die Habsburger\n2 DATE @#DJULIAN@ 1335\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia: Geschichte Österreichs](hhttps://de.wikipedia.org/wiki/Geschichte_Österreichs#Herzogtum_Österreich_unter_den_Habsburgern_(1282-1452))",
            "1 EVEN Tirol fällt an die Habsburger\n2 DATE @#DJULIAN@ 1363\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia: Geschichte Österreichs](hhttps://de.wikipedia.org/wiki/Geschichte_Österreichs#Herzogtum_Österreich_unter_den_Habsburgern_(1282-1452))",
            "1 EVEN Friedrich III aus dem Hause Habsburg wird zum Kaiser des Heiligen Römischen Reiches gekrönt\n2 DATE @#DJULIAN@ 1452\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia: Geschichte Österreichs](hhttps://de.wikipedia.org/wiki/Geschichte_Österreichs#Herzogtum_Österreich_unter_den_Habsburgern_(1282-1452))",
            "1 EVEN Erste Wiener Türkenbelagerung\n2 DATE 27 SEP @#DJULIAN@ 1529\n2 TYPE Österreichische Geschichte\n2 NOTE Die Erste Wiener Türkenbelagerung war ein Höhepunkt der Türkenkriege zwischen dem Osmanischen Reich und den christlichen Staaten Europas. Nur die Tatsache, dass die Angreifer wegen der späten Jahreszeit zum Abbruch der Belagerung gezwungen waren, konnte die Stadt damals retten.\n2 SOUR [Wikipedia: Geschichte Österreichs](https://de.wikipedia.org/wiki/Geschichte_Österreichs#Das_Habsburgerreich_und_die_osmanische_Bedrohung), [Wikipedia: Erste Wiener Türkenbelagerung](https://de.wikipedia.org/wiki/Erste_Wiener_Türkenbelagerung)",
            "1 EVEN Prager Fenstersturz\n2 DATE 23 MAY 1618\n2 TYPE Österreichische Geschichte\n2 NOTE Als Auslöser des 30-jährigen Krieges gilt der Prager Fenstersturz vom 23. Mai 1618, mit dem der Aufstand der protestantischen böhmischen Stände offen ausbrach.\n2 SOUR [Wikipedia: Zweiter Prager Fenstersturz](https://de.wikipedia.org/wiki/Zweiter_Prager_Fenstersturz), [Wikipedia: Dreißigjähriger Krieg](https://de.wikipedia.org/wiki/Dreißjähriger_Krieg)",
            "1 EVEN Westfälischer Friede\n2 DATE 24 OCT 1648\n2 TYPE Österreichische Geschichte\n2 NOTE Die Unterzeichnung zweier Friedensverträge am 24. Oktober 1648 zu Münster markiert das Ende des 30-jährigen Krieges.\n2 SOUR [Wikipedia: Westfälischer Friede](https://de.wikipedia.org/wiki/Westfälischer_Friede), [Wikipedia: Dreißigjähriger Krieg](https://de.wikipedia.org/wiki/Dreißjähriger_Krieg)",
            "1 EVEN Zweite Wiener Türkenbelagerung und Beginn des Großen Türkenkrieges\n2 DATE 14 JUL 1683\n2 TYPE Österreichische Geschichte\n2 NOTE Unter dem Stadtkommandanten Ernst Rüdiger von Starhemberg wurde Wien, damals Residenzstadt des römisch-deutschen Kaisers, zwei Monate lang gegen ein rund 120.000 Mann starkes Belagerungsheer verteidigt. Zum Entsatz der Stadt verbündeten sich erstmals Truppen des Heiligen Römischen Reiches mit solchen aus Polen-Litauen. Weitere Unterstützung leisteten die Republik Venedig und der Kirchenstaat.\n2 SOUR [Wikipedia: Zweite Wiener Türkenbelagerung](https://de.wikipedia.org/wiki/Zweite_Wiener_Türkenbelagerung)",
            "1 EVEN Der Friede von Karlowitz beendet den Großen Türkenkrieg\n2 DATE 26 JAN 1699\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia: Friede von Karlowitz](https://de.wikipedia.org/wiki/Friede_von_Karlowitz)",
            "1 EVEN Tod Karls I des letzten spanischen Habsburgers;\n2 DATE 1 NOV 1700\n2 TYPE Österreichische Geschichte\n2 NOTE Nach dem Aussterben der spanischen Habsburger 1700 kämpften die österreichischen Habsburger gegen Ludwig XIV. im Spanischen Erbfolgekrieg um das dortige Erbe an der Monarchie. Im Frieden von Utrecht 1713 wurden die französischen Bourbonen als spanische Herrscher eingesetzt; den Habsburgern blieben die Spanischen Niederlande, Neapel und die Lombardei.\n2 SOUR [Wikipedia: Geschichte Österreichs](https://de.wikipedia.org/wiki/Geschichte_Österreichs#Erbfolgekriege)",
            "1 EVEN Kaiser Karl VI erlässt die Pragmatische Sanktion\n2 DATE 19 APR 1713\n2 TYPE Österreichische Geschichte\n2 NOTE Die Pragmatische Sanktion ist eine Urkunde, die die Unteilbarkeit und Untrennbarkeit aller habsburgischen Erbkönigreiche und Länder festlegte und zu diesem Zweck eine einheitliche Erbfolgeordnung vorsah. Diese ermöglichte später Maria Theresia die Thronfolge in den habsburgischen Ländern.\n2 SOUR [Wikipedia: Pragmatische Sanktion](https://de.wikipedia.org/wiki/Pragmatische_Sanktion)",
            "1 EVEN Der Venezianisch-Österreichische Türkenkrieg beginnt\n2 DATE 1714\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia: Venezianisch-Österreichischer Türkenkrieg](https://de.wikipedia.org/wiki/Venezianisch-Österreichischer_Türkenkrieg)",
            "1 EVEN Der Friede von Passarowitz beendet den Venezianisch-Österreichischen Türkenkrieg\n2 DATE 21 JUL 1718\n2 TYPE Österreichische Geschichte\n2 NOTE Die Habsburger erhalten Nordbosnien, Nordserbien, das Banat und die kleine Walachei. Durch die sogenannten Schwabenzüge erfolgte die organisierte An- und Besiedlung dieser infolge der Türkenkriege fast menschenleeren Gebiete mit vornehmlich deutschstämmigen katholischen Untertanen\n2 SOUR [Wikipedia: Friede von Passarowitz](https://de.wikipedia.org/wiki/Friede_von_Passarowitz)",
            "1 EVEN Der Russisch-Österreichischer Türkenkrieg beginnt\n2 DATE 1736\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia: Russisch-Österreichischer Türkenkrieg](https://de.wikipedia.org/wiki/Russisch-Österreichischer_Türkenkrieg_(1736-1739))",
            "1 EVEN Der Frieden von Belgrad beendet den Russisch-Österreichischen Türkenkrieg\n2 DATE 18 SEP 1739\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia: Frieden von Belgrad](https://de.wikipedia.org/wiki/Frieden_von_Belgrad)",
            "1 EVEN Tod Karls IV; Maria Theresia kommt an die Macht; Beginn des Österreichischen Erbfolgekriegs\n2 DATE 1740\n2 TYPE Österreichische Geschichte\n2 NOTE Mit dem Tod Karls VI. 1740 waren die Habsburger im Mannesstamm ausgestorben. Daher trat auf Grund der Pragmatischen Sanktion seine Tochter Maria Theresia die Herrschaft in den österreichischen Ländern an. Mit ihrem Ehemann Franz Stephan von Lothringen wurde sie Begründerin der neuen Dynastie Habsburg-Lothringen. Ihr Erbe konnte sie im Österreichischen Erbfolgekrieg (1740â€“1748) weitgehend verteidigen.\n2 SOUR [Wikipedia: Geschichte Österreichs](https://de.wikipedia.org/wiki/Geschichte_Österreichs#Erbfolgekriege)",
            "1 EVEN Beginn der Französischen Revolution\n2 DATE 1789\n2 TYPE Österreichische Geschichte\n2 NOTE Die Französische Revolution von 1789 bis 1799 gehört zu den folgenreichsten Ereignissen der neuzeitlichen europäischen Geschichte. Die Abschaffung des feudal-absolutistischen Ständestaats sowie die Propagierung und Umsetzung grundlegender Werte und Ideen der Aufklärung als Ziele der Französischen Revolution waren mitursächlich für tiefgreifende macht- und gesellschaftspolitische Veränderungen in ganz Europa.\n2 SOUR [Wikipedia: Französische Revolution](https://de.wikipedia.org/wiki/Französische_Revolution)",
            "1 EVEN Ausrufung des Kaisertums Österreich durch Franz II.\n2 DATE 11 AUG 1804\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia: Kaisertum Österreich](https://de.wikipedia.org/wiki/Kaisertum_Österreich)",
            "1 EVEN Beginn des Wiener Kongresses\n2 DATE 18 SEP 1814\n2 TYPE Österreichische Geschichte\n2 NOTE Der Wiener Kongress ordnete nach der Niederlage Napoleon Bonapartes in den Koalitionskriegen Europa neu. Nachdem sich die politische Landkarte des Kontinentes als Nachwirkung der Französischen Revolution erheblich verändert hatte, legte der Kongress wiederum zahlreiche Grenzen neu fest und schuf neue Staaten.\n2 SOUR [Wikipedia: Wiener Kongress](https://de.wikipedia.org/wiki/https://de.wikipedia.org/wiki/Wiener_Kongress)",
            "1 EVEN Revolution von 1848\n2 DATE 1848\n2 TYPE Österreichische Geschichte\n2 NOTE In der ersten Hälfte des 19. Jahrhunderts kam es zu einem Erstarken nationalistischer Bewegungen. Verschiedene Nationalitäten im Vielvölkerstaat Österreich arbeiteten vehement gegeneinander und konnten vom Kaiserhaus gegeneinander ausgespielt werden. Diese Uneinigkeit der Nationalitäten und die Hilfe Russlands retteten in der Revolution von 1848 das Kaisertum vor dem Auseinanderfallen.\n2 SOUR [Wikipedia: Geschichte Österreichs](https://de.wikipedia.org/wiki/Geschichte_Österreichs#Kaisertum_Österreich_(1804-1866))",
            "1 EVEN Niederlage von Königgrätz; Verlust Venetiens\n2 DATE 1866\n2 TYPE Österreichische Geschichte\n2 NOTE Hintergrund des „Deutschen Krieges“ war, dass Bismarck ein deutsches Bündnissystem unter der Hegemonie Preußens anstrebte. Eine solche Hegemonie war nach Einschätzung Bismarcks nur ohne Österreich im Rahmen der „kleindeutschen Lösung“ möglich, da Österreich als bisherige Hegemonialmacht des Deutschen Bundes wirtschaftlich und militärisch zu bedeutend war.\n2 SOUR [Wikipedia: Geschichte Österreichs](https://de.wikipedia.org/wiki/Geschichte_Österreichs#Kaisertum_Österreich_(1804-1866))",
            "1 EVEN Gründung der österreichisch-ungarischen Monarchie\n2 DATE 1867\n2 TYPE Österreichische Geschichte\n2 NOTE Die österreichisch-ungarischen Monarchie wurde als Resultat des sogenannten Ausgleichs mit dem Königreich Ungarn gegründet. Ungarn schied damit aus dem bisherigen Einheitsstaat aus und erhielt eine eigene königliche Regierung.\n2 SOUR [Wikipedia: Geschichte Österreichs](https://de.wikipedia.org/wiki/Geschichte_Österreichs#Österreich_in_der_Doppelmonarchie_Österreich-Ungarn_(1867-1918))",
            "1 EVEN Attentat von Sarajevo\n2 DATE 28 JUN 1914\n2 TYPE Österreichische Geschichte\n2 NOTE Beim Attentat von Sarajevo wurden der Thronfolger Österreich-Ungarns Erzherzog Franz Ferdinand und seine Gemahlin Sophie Chotek, von dem serbischen Nationalisten Gavrilo Princip ermordet. Das Attentat löste die Julikrise aus, die schließlich zum Ersten Weltkrieg führte.\n2 SOUR [Wikipedia: Attentat von Sarajevo](https://de.wikipedia.org/wiki/Attentat_von_Sarajevo)",
            "1 EVEN Tod Franz Josephs I und Thronbesteigung von Karl I.\n2 DATE NOV 1916\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia: Geschichte Österreichs](https://de.wikipedia.org/wiki/Geschichte_Österreichs#Österreich_im_Ersten_Weltkrieg)",
            "1 EVEN Ende des ersten Weltkriegs\n2 DATE 1918\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia: Geschichte Österreichs](https://de.wikipedia.org/wiki/Geschichte_Österreichs#Österreich_im_Ersten_Weltkrieg)",
            "1 EVEN Dollfuß verkündet die „Selbstausschaltung des Parlaments“\n2 DATE 4 MAR 1933\n2 TYPE Österreichische Geschichte\n2 NOTE Eine patt ausgegangene Abstimmung über die Eisenbahnergehälter und taktisch bedingte Rücktritte der drei Parlamentspräsidenten nutzte der christlichsoziale Bundeskanzler Engelbert Dollfuß, um die „Selbstausschaltung des Parlaments“ zu verkünden. Den Wiederzusammentritt des Nationalrates am 15. März verhinderte Polizei, die das Parlamentsgebäude umstellt hatte.\n2 SOUR [Wikipedia: Selbstausschaltung des Parlaments](https://de.wikipedia.org/wiki/Selbstausschaltung_des_Parlaments)",
            "1 EVEN „Anschluss“ an das Großdeutsche Reich\n2 DATE 12 MAR 1938\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia: Anschluss Österreichs](https://de.wikipedia.org/wiki/Anschluss_Österreichs)",
            "1 EVEN Beginn von Luftangriffen in Österreich\n2 DATE AUG 1943\n2 TYPE Österreichische Geschichte\n2 NOTE Luftangriffe fanden in Österreich erst ab August 1943 statt, da es bis dahin teilweise außerhalb der Reichweite alliierter Bomber beziehungsweise deren Begleitjäger lag. Im Vergleich zum Altreich wurden in Österreich durch Luftangriffe weit weniger zivile Ziele, sondern Rüstungsindustrie und Verkehrsknotenpunkte getroffen, womit die alte Bausubstanz weitgehend erhalten blieb.\n2 SOUR [Wikipedia: Geschichte Österreichs](https://de.wikipedia.org/wiki/Geschichte_Österreichs#Österreich_im_Deutschen_Reich_(1938-1945))",
            "1 EVEN Schlacht um Wien\n2 DATE 13 APR 1945\n2 TYPE Österreichische Geschichte\n2 NOTE Der Zweite Weltkrieg war in Wien nach der Schlacht um Wien am 13. April 1945 zu Ende; tags darauf trafen sich Politiker der Zweiten Republik zu ersten Besprechungen, während im Umland der Stadt noch gekämpft wurde. Am 27. April wurde Österreichs Unabhängigkeit verkündet.\n2 SOUR [Wikipedia: Geschichte Österreichs](https://de.wikipedia.org/wiki/Geschichte_Österreichs#Österreich_im_Deutschen_Reich_(1938-1945))",
            "1 EVEN Ende des zweiten Weltkriegs\n2 DATE 8 MAY 1945\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia: Geschichte Österreichs](https://de.wikipedia.org/wiki/Geschichte_Österreichs#Österreich_unter_alliierter_Besatzung_(1945-1955))",
            "1 EVEN Unterzeichnung des Staatsvertrags\n2 DATE 15 MAY 1955\n2 TYPE Österreichische Geschichte\n2 NOTE 1955 erhielt die Republik Österreich durch den Staatsvertrag mit den vier Besatzungsmächten ihre volle staatliche Souveränität zurück. Als Gegenleistung dafür musste die Zweite Republik ihre „immerwährende Neutralität“ erklären und per Verfassungsgesetz festschreiben.\n2 SOUR [Wikipedia: Geschichte Österreichs](https://de.wikipedia.org/wiki/Geschichte_Österreichs#Österreich_vom_Staatsvertrag_bis_zum_EU-Beitritt_(1955-1995))",
            "1 EVEN Gipfeltreffen in Wien zwischen Kennedy und Chruschtschow\n2 DATE 3 JUN 1961\n2 TYPE Österreichische Geschichte\n2 NOTE Das in Wien im neutralen Österreich abgehaltete Treffen sollte dazu dienen, aktuelle Spannungen zwischen den beiden einander im Kalten Krieg gegenüberstehenden Supermächten zu verringern.\n2 SOUR [Wikipedia: Gipfeltreffen in Wien](https://de.wikipedia.org/wiki/Gipfeltreffen_in_Wien)",
            "1 EVEN Fall des Eisernen Vorhangs\n2 DATE 1989\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia: Geschichte Österreichs](https://de.wikipedia.org/wiki/Geschichte_Österreichs#Österreich_vom_Staatsvertrag_bis_zum_EU-Beitritt_(1955-1995))",
            "1 EVEN Volksabstimmung über das Kernkraftwerk Zwentendorf\n2 DATE 1978\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia: Volksabstimmung in Österreich über die Inbetriebnahme des Kernkraftwerkes Zwentendorf](https://de.wikipedia.org/wiki/Volksabstimmung_in_Österreich_über_die_Inbetriebnahme_des_Kernkraftwerkes_Zwentendorf)",
            "1 EVEN Beitritt zur Europäischen Union\n2 DATE 1995\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia: Erweiterung der Europäischen Union](https://de.wikipedia.org/wiki/Erweiterung_der_Europäischen_Union#Vierte_Erweiterung_(EFTA-Erweiterung)_EU_1995)",
        ]);
    }
}
