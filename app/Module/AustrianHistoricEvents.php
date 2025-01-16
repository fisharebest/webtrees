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

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\I18N;
use Illuminate\Support\Collection;

/**
 * Class AustrianHistoricEvents
 */
class AustrianHistoricEvents extends AbstractModule implements ModuleHistoricEventsInterface
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
     * @return Collection<int,string>
     */
    public function historicEventsAll(): Collection
    {
        switch (I18N::languageTag()) {
            case 'de':
                return new Collection([
                    "1 EVEN Feldzug Karls des Großen gegen die Awaren\n2 DATE 791\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia:Geschichte Österreichs](https://de.wikipedia.org/wiki/Geschichte_%C3%96sterreichs#Fr%C3%BChmittelalter_(bis_976))\n3 CONT Im Jahr 791 führte Karl der Große einen ersten misslungenen Feldzug gegen die Awaren, konnte sie aber dennoch bis zum Wienerwald zurückdrängen und fränkische Stützpunkte in Comagena (Tulln) und Aelium Cetium (St. Pölten) errichten.",
                    "1 EVEN Belehnung Luitpolds aus dem Geschlecht der Babenberger mit der Marcha orientalis\n2 DATE 976\n2 TYPE Österreichische Geschichte\n3 PA\n2 SOUR [Wikipedia:Geschichte Österreichs](https://de.wikipedia.org/wiki/Geschichte_%C3%96sterreichs#Markgrafschaft_%C3%96sterreich_(976%E2%80%931156)), [Wikipedia:Babenberger](https://de.wikipedia.org/wiki/Babenberger#Markgrafen)\n3 CONT Der römisch-deutsche Kaiser Otto II. belehnte 976 Luitpold aus dem Geschlecht der Babenberger mit der „Marcha orientalis“. Diese östliche Mark war Teil des Bayrischen Stammesherzogtums und gilt als Keimzelle des späteren Herzogtums Österreich.",
                    "1 EVEN Älteste bekannte schriftliche Nennung des Namens Ostarrichi\n2 DATE 996\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia:Ostarrichi](https://de.wikipedia.org/wiki/Ostarrichi#Geschichte)\n3 CONT Die älteste bekannte schriftliche Nennung des Namens „Ostarrichi“, aus dem der spätere Name für Österreich entstanden ist, stammt vom 1. November 996 aus der in Bruchsal ausgefertigten Schenkungs-Urkunde des römisch-deutschen Kaisers Ottos III. gerichtet an den Bischof von Freising Gottschalk von Hagenau, der Ostarrichi-Urkunde.",
                    "1 EVEN Erhebung Österreichs zum Herzogtum\n2 DATE 1156\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia:Geschichte Österreichs](https://de.wikipedia.org/wiki/Geschichte_%C3%96sterreichs#Herzogtum_%C3%96sterreich_unter_den_Babenbergern_(1156%E2%80%931246))\n3 CONT Im Zuge des Konfliktes zwischen den Staufern und den Welfen kam 1139 das Herzogtum Bayern an die Babenberger. Als Friedrich I. Barbarossa diesen Streit beenden wollte, gab er den Welfen das Herzogtum Bayern zurück – gleichsam als Entschädigung wurde Österreich mit dem Privilegium minus von 1156 zum Herzogtum des Heiligen Römischen Reiches erhoben.",
                    "1 EVEN Tod des letzten Babenbergers\n2 DATE 1246\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia:Geschichte Österreichs](https://de.wikipedia.org/wiki/Geschichte_%C3%96sterreichs#Herzogtum_%C3%96sterreich_unter_den_Babenbergern_(1156%E2%80%931246))\n3 CONT Im Kapf gegen Ungarn starb Friedrich II. in der Schlacht an der Leitha. Mit ihm starben die Babenberger in männlicher Linie aus. Es begann die als „österreichisches Interregnum“ bezeichnete Periode, während der die Länder Friedrichs II. in ein länger andauerndes Kräftespiel rivalisierender Mächte gerieten.",
                    "1 EVEN Habsburger werden Herzöge von Österreich und der Steiermark\n2 DATE 1278\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia:Geschichte Österreichs](https://de.wikipedia.org/wiki/Geschichte_%C3%96sterreichs#Herzogtum_%C3%96sterreich_als_Streitobjekt_(1246%E2%80%931282))\n3 CONT Rudolf von Habsburg besiegte den König von Böhmen, Ottokar II. Přemysl, in der Schlacht auf dem Marchfeld. Die Habsburger konnten sich daraufhin als Herzöge von Österreich und der Steiermark etablieren und sollten hier bis 1918, also 640 Jahre lang herrschen.",
                    "1 EVEN Kärnten und Krain fällt an die Habsburger\n2 DATE 1335\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia:Geschichte Österreichs](hhttps://de.wikipedia.org/wiki/Geschichte_%C3%96sterreichs#Herzogtum_%C3%96sterreich_unter_den_Habsburgern_(1282%E2%80%931452))",
                    "1 EVEN Tirol fällt an die Habsburger\n2 DATE 1363\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia:Geschichte Österreichs](hhttps://de.wikipedia.org/wiki/Geschichte_%C3%96sterreichs#Herzogtum_%C3%96sterreich_unter_den_Habsburgern_(1282%E2%80%931452))",
                    "1 EVEN Friedrich III. aus dem Hause Habsburg wird zum Kaiser des Heiligen Römischen Reiches gekrönt\n2 DATE 1452\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia:Geschichte Österreichs](hhttps://de.wikipedia.org/wiki/Geschichte_%C3%96sterreichs#Herzogtum_%C3%96sterreich_unter_den_Habsburgern_(1282%E2%80%931452))",
                    "1 EVEN Erste Wiener Türkenbelagerung\n2 DATE 27 SEP 1529\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia:Geschichte Österreichs](https://de.wikipedia.org/wiki/Geschichte_%C3%96sterreichs#Das_Habsburgerreich_und_die_osmanische_Bedrohung), [Wikipedia:Erste Wiener Türkenbelagerung](https://de.wikipedia.org/wiki/Erste_Wiener_T%C3%BCrkenbelagerung)\n3 CONT Die Erste Wiener Türkenbelagerung war ein Höhepunkt der Türkenkriege zwischen dem Osmanischen Reich und den christlichen Staaten Europas. Nur die Tatsache, dass die Angreifer wegen der späten Jahreszeit zum Abbruch der Belagerung gezwungen waren, konnte die Stadt damals retten.",
                    "1 EVEN Prager Fenstersturz\n2 DATE 23 MAY 1618\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia:Zweiter Prager Fenstersturz](https://de.wikipedia.org/wiki/Zweiter_Prager_Fenstersturz), [Wikipedia:Dreißigjähriger Krieg](https://de.wikipedia.org/wiki/Drei%C3%9Figj%C3%A4hriger_Krieg)\n3 CONT Als Auslöser des 30-jährigen Krieges gilt der Prager Fenstersturz vom 23. Mai 1618, mit dem der Aufstand der protestantischen böhmischen Stände offen ausbrach.",
                    "1 EVEN Westfälischer Friede\n2 DATE 24 OCT 1648\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia:Westfälischer Friede](https://de.wikipedia.org/wiki/Westf%C3%A4lischer_Friede), [Wikipedia:Dreißigjähriger Krieg](https://de.wikipedia.org/wiki/Drei%C3%9Figj%C3%A4hriger_Krieg)\n3 CONT Die Unterzeichnung zweier Friedensverträge am 24. Oktober 1648 zu Münster markiert das Ende des 30-jährigen Krieges.",
                    "1 EVEN Zweite Wiener Türkenbelagerung und Beginn des Großen Türkenkrieges\n2 DATE 14 JUL 1683\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia:Zweite Wiener Türkenbelagerung](https://de.wikipedia.org/wiki/Zweite_Wiener_T%C3%BCrkenbelagerung)\n3 CONT Unter dem Stadtkommandanten Ernst Rüdiger von Starhemberg wurde Wien, damals Residenzstadt des römisch-deutschen Kaisers, zwei Monate lang gegen ein rund 120.000 Mann starkes Belagerungsheer verteidigt. Zum Entsatz der Stadt verbündeten sich erstmals Truppen des Heiligen Römischen Reiches mit solchen aus Polen-Litauen. Weitere Unterstützung leisteten die Republik Venedig und der Kirchenstaat.",
                    "1 EVEN Der Friede von Karlowitz beendet den Großen Türkenkrieg\n2 DATE 26 JAN 1699\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia:Friede von Karlowitz](https://de.wikipedia.org/wiki/Friede_von_Karlowitz)",
                    "1 EVEN Tod Karls II., des letzten spanischen Habsburgers;\n2 DATE 1 NOV 1700\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia:Geschichte Österreichs](https://de.wikipedia.org/wiki/Geschichte_%C3%96sterreichs#Erbfolgekriege)\n3 CONT Nach dem Aussterben der spanischen Habsburger 1700 kämpften die österreichischen Habsburger gegen Ludwig XIV. im Spanischen Erbfolgekrieg um das dortige Erbe an der Monarchie. Im Frieden von Utrecht 1713 wurden die französischen Bourbonen als spanische Herrscher eingesetzt; den Habsburgern blieben die Spanischen Niederlande, Neapel und die Lombardei.",
                    "1 EVEN Kaiser Karl VI. erlässt die Pragmatische Sanktion\n2 DATE 19 APR 1713\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia:Pragmatische Sanktion](https://de.wikipedia.org/wiki/Pragmatische_Sanktion)\n3 CONT Die Pragmatische Sanktion ist eine Urkunde, die die Unteilbarkeit und Untrennbarkeit aller habsburgischen Erbkönigreiche und Länder festlegte und zu diesem Zweck eine einheitliche Erbfolgeordnung vorsah. Diese ermöglichte später Maria Theresia die Thronfolge in den habsburgischen Ländern.",
                    "1 EVEN Der Venezianisch-Österreichische Türkenkrieg beginnt\n2 DATE 1714\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia:Venezianisch-Österreichischer Türkenkrieg](https://de.wikipedia.org/wiki/Venezianisch-%C3%96sterreichischer_T%C3%BCrkenkrieg)",
                    "1 EVEN Der Friede von Passarowitz beendet den Venezianisch-Österreichischen Türkenkrieg\n2 DATE 21 JUL 1718\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia:Friede von Passarowitz](https://de.wikipedia.org/wiki/Friede_von_Passarowitz)\n3 CONT Die Habsburger erhalten Nordbosnien, Nordserbien, das Banat und die kleine Walachei. Durch die sogenannten Schwabenzüge erfolgte die organisierte An- und Besiedlung dieser infolge der Türkenkriege fast menschenleeren Gebiete mit vornehmlich deutschstämmigen katholischen Untertanen",
                    "1 EVEN Der Russisch-Österreichischer Türkenkrieg beginnt\n2 DATE 1736\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia:Russisch-Österreichischer Türkenkrieg](https://de.wikipedia.org/wiki/Russisch-%C3%96sterreichischer_T%C3%BCrkenkrieg_(1736%E2%80%931739))",
                    "1 EVEN Der Frieden von Belgrad beendet den Russisch-Österreichischen Türkenkrieg\n2 DATE 18 SEP 1739\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia:Frieden von Belgrad](https://de.wikipedia.org/wiki/Frieden_von_Belgrad)",
                    "1 EVEN Tod Karls IV.; Maria Theresia kommt an die Macht; Beginn des Österreichischen Erbfolgekriegs\n2 DATE 1740\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia:Geschichte Österreichs](https://de.wikipedia.org/wiki/Geschichte_%C3%96sterreichs#Erbfolgekriege)\n3 CONT Mit dem Tod Karls VI. 1740 waren die Habsburger im Mannesstamm ausgestorben. Daher trat auf Grund der Pragmatischen Sanktion seine Tochter Maria Theresia die Herrschaft in den österreichischen Ländern an. Mit ihrem Ehemann Franz Stephan von Lothringen wurde sie Begründerin der neuen Dynastie Habsburg-Lothringen. Ihr Erbe konnte sie im Österreichischen Erbfolgekrieg (1740–1748) weitgehend verteidigen.",
                    "1 EVEN Beginn der Französischen Revolution\n2 DATE 1789\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia:Französische Revolution](https://de.wikipedia.org/wiki/Franz%C3%B6sische_Revolution)\n3 CONT Die Französische Revolution von 1789 bis 1799 gehört zu den folgenreichsten Ereignissen der neuzeitlichen europäischen Geschichte. Die Abschaffung des feudal-absolutistischen Ständestaats sowie die Propagierung und Umsetzung grundlegender Werte und Ideen der Aufklärung als Ziele der Französischen Revolution waren mitursächlich für tiefgreifende macht- und gesellschaftspolitische Veränderungen in ganz Europa.",
                    "1 EVEN Ausrufung des Kaisertums Österreich durch Franz II.\n2 DATE 11 AUG 1804\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia:Kaisertum Österreich](https://de.wikipedia.org/wiki/Kaisertum_%C3%96sterreich)",
                    "1 EVEN Beginn des Wiener Kongresses\n2 DATE 18 SEP 1814\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia]()\n3 CONT Der Wiener Kongress ordnete nach der Niederlage Napoleon Bonapartes in den Koalitionskriegen Europa neu. Nachdem sich die politische Landkarte des Kontinentes als Nachwirkung der Französischen Revolution erheblich verändert hatte, legte der Kongress wiederum zahlreiche Grenzen neu fest und schuf neue Staaten.",
                    "1 EVEN Revolution von 1848\n2 DATE 1848\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia:Geschichte Österreichs](https://de.wikipedia.org/wiki/Geschichte_%C3%96sterreichs#Kaisertum_%C3%96sterreich_(1804%E2%80%931866))\n3 CONT In der ersten Hälfte des 19. Jahrhunderts kam es zu einem Erstarken nationalistischer Bewegungen. Verschiedene Nationalitäten im Vielvölkerstaat Österreich arbeiteten vehement gegeneinander und konnten vom Kaiserhaus gegeneinander ausgespielt werden. Diese Uneinigkeit der Nationalitäten und die Hilfe Russlands retteten in der Revolution von 1848 das Kaisertum vor dem Auseinanderfallen.",
                    "1 EVEN Niederlage von Königgrätz; Verlust Venetiens\n2 DATE 1866\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia:Geschichte Österreichs](https://de.wikipedia.org/wiki/Geschichte_%C3%96sterreichs#Kaisertum_%C3%96sterreich_(1804%E2%80%931866))\n3 CONT Hintergrund des „Deutschen Krieges“ war, dass Bismarck ein deutsches Bündnissystem unter der Hegemonie Preußens anstrebte. Eine solche Hegemonie war nach Einschätzung Bismarcks nur ohne Österreich im Rahmen der „kleindeutschen Lösung“ möglich, da Österreich als bisherige Hegemonialmacht des Deutschen Bundes wirtschaftlich und militärisch zu bedeutend war.",
                    "1 EVEN Gründung der österreichisch-ungarischen Monarchie\n2 DATE 1867\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia:Geschichte Österreichs](https://de.wikipedia.org/wiki/Geschichte_%C3%96sterreichs#%C3%96sterreich_in_der_Doppelmonarchie_%C3%96sterreich-Ungarn_(1867%E2%80%931918))\n3 CONT Die österreichisch-ungarischen Monarchie wurde als Resultat des sogenannten Ausgleichs mit dem Königreich Ungarn gegründet. Ungarn schied damit aus dem bisherigen Einheitsstaat aus und erhielt eine eigene königliche Regierung.",
                    "1 EVEN Attentat von Sarajevo\n2 DATE 28 JUN 1914\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia:Attentat von Sarajevo](https://de.wikipedia.org/wiki/Attentat_von_Sarajevo)\n3 CONT Beim Attentat von Sarajevo wurden der Thronfolger Österreich-Ungarns Erzherzog Franz Ferdinand und seine Gemahlin Sophie Chotek, von dem serbischen Nationalisten Gavrilo Princip ermordet. Das Attentat löste die Julikrise aus, die schließlich zum Ersten Weltkrieg führte.",
                    "1 EVEN Tod Franz Josephs I. und Thronbesteigung von Karl I.\n2 DATE NOV 1916\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia:Geschichte Österreichs](https://de.wikipedia.org/wiki/Geschichte_%C3%96sterreichs#%C3%96sterreich_im_Ersten_Weltkrieg)",
                    "1 EVEN Ende des ersten Weltkriegs\n2 DATE 1918\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia:Geschichte Österreichs](https://de.wikipedia.org/wiki/Geschichte_%C3%96sterreichs#%C3%96sterreich_im_Ersten_Weltkrieg)",
                    "1 EVEN Dollfuß verkündet die „Selbstausschaltung des Parlaments“\n2 DATE 4 MAR 1933\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia:Selbstausschaltung des Parlaments](https://de.wikipedia.org/wiki/Selbstausschaltung_des_Parlaments)\n3 CONT Eine patt ausgegangene Abstimmung über die Eisenbahnergehälter und taktisch bedingte Rücktritte der drei Parlamentspräsidenten nutzte der christlichsoziale Bundeskanzler Engelbert Dollfuß, um die „Selbstausschaltung des Parlaments“ zu verkünden. Den Wiederzusammentritt des Nationalrates am 15. März verhinderte Polizei, die das Parlamentsgebäude umstellt hatte.",
                    "1 EVEN „Anschluss“ an das Deutsche Reich\n2 DATE 12 MAR 1938\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia:Anschluss Österreichs](https://de.wikipedia.org/wiki/Anschluss_%C3%96sterreichs)",
                    "1 EVEN Beginn von Luftangriffen in Österreich\n2 DATE AUG 1943\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia:Geschichte Österreichs](https://de.wikipedia.org/wiki/Geschichte_%C3%96sterreichs#%C3%96sterreich_im_Deutschen_Reich_(1938%E2%80%931945))\n3 CONT Luftangriffe fanden in Österreich erst ab August 1943 statt, da es bis dahin teilweise außerhalb der Reichweite alliierter Bomber beziehungsweise deren Begleitjäger lag. Im Vergleich zum Altreich wurden in Österreich durch Luftangriffe weit weniger zivile Ziele, sondern Rüstungsindustrie und Verkehrsknotenpunkte getroffen, womit die alte Bausubstanz weitgehend erhalten blieb.",
                    "1 EVEN Schlacht um Wien\n2 DATE 13 APR 1945\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia:Geschichte Österreichs](https://de.wikipedia.org/wiki/Geschichte_%C3%96sterreichs#%C3%96sterreich_im_Deutschen_Reich_(1938%E2%80%931945))\n3 CONT Der Zweite Weltkrieg war in Wien nach der Schlacht um Wien am 13. April 1945 zu Ende; tags darauf trafen sich Politiker der Zweiten Republik zu ersten Besprechungen, während im Umland der Stadt noch gekämpft wurde. Am 27. April wurde Österreichs Unabhängigkeit verkündet.",
                    "1 EVEN Ende des zweiten Weltkriegs\n2 DATE 8 MAY 1945\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia:Geschichte Österreichs](https://de.wikipedia.org/wiki/Geschichte_%C3%96sterreichs#%C3%96sterreich_unter_alliierter_Besatzung_(1945%E2%80%931955))",
                    "1 EVEN Unterzeichnung des Staatsvertrags\n2 DATE 15 MAY 1955\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia:Geschichte Österreichs](https://de.wikipedia.org/wiki/Geschichte_%C3%96sterreichs#%C3%96sterreich_vom_Staatsvertrag_bis_zum_EU-Beitritt_(1955%E2%80%931995))\n3 CONT 1955 erhielt die Republik Österreich durch den Staatsvertrag mit den vier Besatzungsmächten ihre volle staatliche Souveränität zurück. Als Gegenleistung dafür musste die Zweite Republik ihre „immerwährende Neutralität“ erklären und per Verfassungsgesetz festschreiben.",
                    "1 EVEN Gipfeltreffen in Wien zwischen Kennedy und Chruschtschow\n2 DATE 3 JUN 1961\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia:Gipfeltreffen in Wien](https://de.wikipedia.org/wiki/Gipfeltreffen_in_Wien)\n3 CONT Das in Wien im neutralen Österreich abgehaltete Treffen sollte dazu dienen, aktuelle Spannungen zwischen den beiden einander im Kalten Krieg gegenüberstehenden Supermächten zu verringern.",
                    "1 EVEN Fall des Eisernen Vorhangs\n2 DATE 1989\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia:Geschichte Österreichs](https://de.wikipedia.org/wiki/Geschichte_%C3%96sterreichs#%C3%96sterreich_vom_Staatsvertrag_bis_zum_EU-Beitritt_(1955%E2%80%931995))",
                    "1 EVEN Volksabstimmung über das Kernkraftwerk Zwentendorf\n2 DATE 1978\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia:Volksabstimmung in Österreich über die Inbetriebnahme des Kernkraftwerkes Zwentendorf](https://de.wikipedia.org/wiki/Volksabstimmung_in_%C3%96sterreich_%C3%BCber_die_Inbetriebnahme_des_Kernkraftwerkes_Zwentendorf)",
                    "1 EVEN Beitritt zur Europäischen Union\n2 DATE 1995\n2 TYPE Österreichische Geschichte\n2 SOUR [Wikipedia:Erweiterung der Europäischen Union](https://de.wikipedia.org/wiki/Erweiterung_der_Europ%C3%A4ischen_Union#Vierte_Erweiterung_(EFTA-Erweiterung)_EU_1995)",
                ]);

            default:
                return new Collection();
        }
    }
}
