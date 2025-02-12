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
 * Class CzechMonarchsAndPresidents
 */
class CzechMonarchsAndPresidents extends AbstractModule implements ModuleHistoricEventsInterface
{
    use ModuleHistoricEventsTrait;

    public function title(): string
    {
        return 'Čeští panovníci a prezidenti 🇨🇿';
    }

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
            case 'cs':
                return new Collection([
                    /** @link https://cs.wikipedia.org/wiki/Seznam_představitelů_českého_státu */
                    "1 EVEN Mojmír I.\n2 TYPE Kníže velkomoravský\n2 DATE FROM 830 TO 846\n2 NOTE První historicky známý moravský vládce, zakladatel dynastie Mojmírovců.",
                    "1 EVEN Rostislav\n2 TYPE Kníže velkomoravský\n2 DATE FROM 846 TO 870\n2 NOTE Synovec Mojmíra I. V roce 846, pravděpodobně po Mojmírově smrti, byl na velkomoravský stolec dosazen Ludvíkem Němcem, panovníkem východofranské říše.",
                    "1 EVEN Svatopluk I.\n2 TYPE Kníže velkomoravský\n2 DATE FROM 870 TO 871\n2 NOTE Synovec Rostislava. Třetí a nejvýznamnější panovník Velkomoravské říše (ve středověku označován za krále).",
                    "1 EVEN Wilhelm II. a Engelšalk\n2 TYPE Místodržící\n2 DATE FROM 871 TO 872\n2 NOTE Franští místodržící v době Svatopluka.",
                    "1 EVEN Slavomír\n2 TYPE Kníže velkomoravský\n2 DATE FROM 872 TO 873\n2 NOTE Kněz, vůdce vzpoury proti franské nadvládě.",
                    "1 EVEN Svatopluk I.\n2 TYPE Kníže velkomoravský\n2 DATE FROM 873 TO 894\n2 NOTE Znovu na trůně.",
                    "1 EVEN Mojmír II.\n2 TYPE Kníže velkomoravský\n2 DATE FROM 894 TO 906\n2 NOTE Syn Svatopluka I. Po roce 906 o Mojmírovi II. historické prameny mlčí, předpokládá se tedy, že zemřel právě toho roku.",
                    "1 EVEN Svatopluk II.\n2 TYPE Kníže velkomoravský\n2 DATE 906\n2 NOTE Údělník v Nitře. Na krátku dobu se stal spoluvládcem říše svého bratra.",
                    "1 EVEN Bořivoj I.\n2 TYPE Kníže\n2 DATE FROM 872 TO 883\n2 NOTE Zakladatel dynastie Přemyslovců, podle Kosmase syn bájného Hostivíta, jeho původ nelze spolehlivě prokázat. První historicky doložený Přemyslovec.",
                    "1 EVEN Strojmír\n2 TYPE Kníže\n2 DATE FROM 883 TO 885\n2 NOTE Původ neznámý. Podle Kristiánovy legendy svrhl knížete Bořivoje, jenž uprchl na Moravu.",
                    "1 EVEN Bořivoj I.\n2 TYPE Kníže\n2 DATE FROM 885 TO 889\n2 NOTE Podruhé na trůně.",
                    "1 EVEN Svatopluk I.\n2 TYPE Kníže\n2 DATE FROM 890 TO 894\n2 NOTE Mojmírovec. Po smrti svého chráněnce Bořivoje I. na krátkou dobu opět převzal vládu (nárok na české území byl potvrzen Arnulfem Korutanským v roce 890).",
                    "1 EVEN Spytihněv I.\n2 TYPE Kníže\n2 DATE FROM 894 TO 915\n2 NOTE Syn Bořivoje. Vlády se ujal poté, co se Čechy po smrti Svatopluka I. vymanily z vlivu Velké Moravy a přiklonily se k Bavorsku.",
                    "1 EVEN Vratislav I.\n2 TYPE Kníže\n2 DATE FROM 915 TO 921\n2 NOTE Syn Bořivoje I., bratr Spytihněva I.",
                    "1 EVEN svatý Václav\n2 TYPE Kníže\n2 DATE FROM 921 TO 935\n2 NOTE Syn Vratislava I., patron přemyslovské dynastie a české země, v některých seznamech bývá uveden jako kníže Václav I. Ve starší literatuře se často udává jako rok jeho smrti 929, většina soudobých vědců se ale již kloní k roku 935.",
                    "1 EVEN Boleslav I.\n2 TYPE Kníže\n2 DATE FROM 935 TO 972\n2 NOTE Syn Vratislava I., bratr svatého Václava. Boleslav I. je někdy považován za zakladatele české státnosti: ražba mince, hradská soustava, územní expanze – severní část Moravy, Horní Slezsko, Krakovsko a Červené hrady (Halič) – a centralizace.",
                    "1 EVEN Boleslav II.\n2 TYPE Kníže\n2 DATE FROM 972 TO 999\n2 NOTE Syn Boleslava I. Ještě rozšířil území zděděná po svém otci, ale ke sklonku vlády nedokázal zabránit jejich ztrátě ve prospěch Polska. Vyvražděni Slavníkovci, čímž dokončeno sjednocení Čech. Po jeho smrti přichází státní úpadek a boj o trůn mezi jeho třemi syny.",
                    "1 EVEN Boleslav III.\n2 TYPE Kníže\n2 DATE FROM 999 TO 1002\n2 NOTE Syn Boleslava II. O trůn přišel povstáním předáků a družiníků.",
                    "1 EVEN Vladivoj\n2 TYPE Kníže\n2 DATE FROM 1002 TO 1003\n2 NOTE Snad Piastovec, možná syn polského knížete Měška I. a Přemyslovny Doubravky. Na knížecí stolec dosazen polským knížetem Boleslavem Chrabrým.",
                    "1 EVEN Jaromír\n2 TYPE Kníže\n2 DATE 1003\n2 NOTE Syn Boleslava II. Po smrti Vladivoje byl díky vojenské pomoci Jindřicha II. krátce dosazen na český knížecí stolec ",
                    "1 EVEN Boleslav III.\n2 TYPE Kníže\n2 DATE 1003\n2 NOTE Podruhé na trůně, roku 1003 Boleslavem Chrabrým znovu dosazen. Nařídil vraždění Vršovců; to vyvolalo povstání. Boleslav Chrabrý povolal svého chráněnce do Polska, oslepil jej a uvěznil.",
                    "1 EVEN Boleslav Chrabrý\n2 TYPE Kníže\n2 DATE FROM 1003 TO 1004\n2 NOTE Piastovec, polský kníže. Syn polského knížete Měška I. a Přemyslovny Doubravky, dcery Boleslava I. Na jaře 1003 vpadl do Čech, které ovládal do podzimu 1004.",
                    "1 EVEN Jaromír\n2 TYPE Kníže\n2 DATE FROM 1004 TO 1012\n2 NOTE Podruhé na trůně. Na podzim 1004 s podporou Jindřicha II. byli z Čech vypuzeni Poláci.",
                    "1 EVEN Oldřich\n2 TYPE Kníže\n2 DATE FROM 1012 TO 1033\n2 NOTE Nejmladší syn Boleslava II. Na jaře 1012 se zmocnil vlády v Čechách, Jaromír uprchl. V letech 1019/1029 (datace je nejistá) postupně dobyl na Polácích Moravu, která od té doby byla trvalou součástí českého státu.",
                    "1 EVEN Jaromír\n2 TYPE Kníže\n2 DATE FROM 1033 TO 1034\n2 NOTE Potřetí na trůně. Když Oldřicha roku 1033 obvinil císař Konrád II. z úkladů proti své osobě a sesadil jej, ustavilo říšské vojsko na podzim 1033 znovu Jaromíra českým knížetem.",
                    "1 EVEN Oldřich\n2 TYPE Kníže\n2 DATE 1034\n2 NOTE Podruhé na trůně. Na jaře 1034 Konrád II. Oldřicha propustil a vrátil mu vládu v knížectví. Jaromíra dal jeho bratr zajmout a oslepit.",
                    "1 EVEN Břetislav I.\n2 TYPE Kníže\n2 DATE FROM 1034 TO 1055\n2 NOTE Syn knížete Oldřicha. Roku 1054 vyřešil otázku nástupnictví prosazením seniorátu – knížetem měl být nejstarší mužský člen přemyslovské dynastie.",
                    "1 EVEN Spytihněv II.\n2 TYPE Kníže\n2 DATE FROM 1055 TO 1061\n2 NOTE Syn knížete Břetislava. Své bratry zbavil držav a podřídil tak Moravu své přímé vládě.",
                    "1 EVEN Vratislav II\n2 TYPE Kníže a král\n2 DATE FROM 1061 TO 1092\n2 NOTE Syn Břetislava I. Od roku 1085 byl prvním českým králem. Nedědičný titul ad personam mu propůjčil císař Jindřich IV. V letech 1085–1092 byl také titulárním polským králem.",
                    "1 EVEN Konrád I. Brněnský\n2 TYPE Kníže\n2 DATE 1092\n2 NOTE Syn Břetislava I. Na knížecí stolec usedl 20. ledna 1092. Stihl se pouze pokusit opět spojit pražské a olomoucké biskupství.",
                    "1 EVEN Břetislav II.\n2 TYPE Kníže\n2 DATE FROM 1092 TO 1100\n2 NOTE Syn Vratislava II. Roku 1099 Břetislav II. vojensky obsadil Moravu a svěřil ji do správy svého bratra Bořivoje II., kterého rovněž určil jako svého nástupce.",
                    "1 EVEN Bořivoj II.\n2 TYPE Kníže\n2 DATE FROM 1100 TO 1107\n2 NOTE Syn Vratislava II. Jeho nástup na trůn znamenal porušení zásad stařešinského řádu a počátek dlouhotrvajících rozbrojů mezi Přemyslovci.",
                    "1 EVEN Svatopluk Olomoucký\n2 TYPE Kníže\n2 DATE FROM 1107 TO 1109\n2 NOTE Syn Oty Olomouckého. V roce 1107 poslal k Bořivoji II. vyslance, který pomluvil u knížete jeho spojence, Bořivoj ztratil oporu své moci a byl nucen uprchnout do Německa. Svatopluk se tak stal knížetem.",
                    "1 EVEN Vladislav I.\n2 TYPE Kníže\n2 DATE FROM 1109 TO 1117\n2 NOTE Syn Vratislava II. Jeho moc byla ohrožena ze strany příbuzných, císař Jindřich V. ho ovšem coby knížete potvrdil a Vladislav se tvrdě vypořádal se svými odpůrci.",
                    "1 EVEN Bořivoj II.\n2 TYPE Kníže\n2 DATE FROM 1117 TO 1120\n2 NOTE Podruhé na trůně. Roku 1117 postoupil Vladislav I. svému bratru Bořivojovi II. vládu, ale již za tři roky se z nejasných příčin opět vystřídali.",
                    "1 EVEN Vladislav I.\n2 TYPE Kníže\n2 DATE FROM 1120 TO 1125\n2 NOTE Podruhé na trůně.",
                    "1 EVEN Soběslav I.\n2 TYPE Kníže\n2 DATE FROM 1125 TO 1140\n2 NOTE Syn Vratislava II. Vládu si nárokoval i Ota II. Olomoucký a získal si podporu německého krále Lothara III. Říšská vojenská výprava skončila porážkou v bitvě u Chlumce v únoru 1126.",
                    "1 EVEN Vladislav II.\n2 TYPE Kníže a král\n2 DATE FROM 1140 TO 1172\n2 NOTE Syn Vladislava I. a druhý český král. Zřejmě nedědičný královský titul mu propůjčil císař Fridrich I. Barbarossa v roce 1158.",
                    "1 EVEN Bedřich\n2 TYPE Kníže\n2 DATE FROM 1172 TO 1173\n2 NOTE Syn Vladislava II., který mu roku 1172 předal vládu podle principu primogenitury. Dosud ovšem platilo právo seniorátu. O knížecí hodnost se tak hlásili synové Soběslava I.",
                    "1 EVEN Soběslav II.\n2 TYPE Kníže\n2 DATE FROM 1173 TO 1178\n2 NOTE Syn Soběslava I. Získal podporu císaře Barbarossy, který za knížete určil Soběslavova bratra Oldřicha. Oldřich však neměl podporu české šlechty a brzy předal vládu Soběslavovi.",
                    "1 EVEN Bedřich\n2 TYPE Kníže\n2 DATE FROM 1178 TO 1189\n2 NOTE Syn Vladislava II. V létě 1178 ztratil Soběslav II. podporu Fridricha Barbarossy, čehož využil Bedřich, uplatil císaře a obdržel od něj Čechy v léno.",
                    "1 EVEN Konrád II. Ota\n2 TYPE Kníže\n2 DATE FROM 1189 TO 1191\n2 NOTE Syn Konráda II. Znojemského. Po smrti Bedřicha nastoupil na knížecí stolec, Čechy a Morava se tak spojily do jednoho státního celku, protože kníže rezignoval na titul markraběte moravského.",
                    "1 EVEN Václav (II.)\n2 TYPE Kníže\n2 DATE FROM 1191 TO 1192\n2 NOTE Syn Soběslava I. Po smrti Konráda Oty se zmocnil Čech. Proti němu ale vystoupil biskup Jindřich Břetislav a vymohl u císaře Jindřicha VI., aby udělil Čechy v léno Přemyslu Otakarovi I. V některých seznamech je uváděn jako Václav II.",
                    "1 EVEN Přemysl Otakar I.\n2 TYPE Kníže\n2 DATE FROM 1192 TO 1193\n2 NOTE Syn Vladislava II. Knížetem se s podporou biskupa Jindřicha Břetislava a císaře Jindřicha VI. stal roku 1192. O rok později ho šlechta na trůně vystřídala Jindřichem Břetislavem.",
                    "1 EVEN Jindřich Břetislav\n2 TYPE Kníže\n2 DATE FROM 1193 TO 1197\n2 NOTE Roku 1193 mu císař propůjčil hodnost českého knížete. Přemysl I. proti němu táhl s vojskem, v rozhodující chvíli se však od něho odvrátila šlechta.",
                    "1 EVEN Vladislav Jindřich\n2 TYPE Kníže\n2 DATE 1197\n2 NOTE Syn Vladislava II. Titul si nárokoval i jeho starší bratr Přemysl I. Aby zabránil vzestupu politické nestability, vzdal se titulu a dědických nároků ve prospěch svého bratra.",
                    "1 EVEN Přemysl Otakar I.\n2 TYPE Král český\n2 DATE FROM 1198 TO 1230\n2 NOTE Podruhé na trůně, korunován 1203. Přemysl si postupně nechal potvrdit královskou hodnost od obou římských králů i od papeže. Dědičný titul zaručila Zlatá bula sicilská (1212).",
                    "1 EVEN Václav I.\n2 TYPE Král český\n2 DATE FROM 1230 TO 1253\n2 NOTE Syn Přemysla I., čtvrtý český král. Korunován byl už v roce 1228. Přispěl k odražení mongolského vpádu do Evropy a společně se synem Přemyslem získal Rakousko.",
                    "1 EVEN Přemysl Otakar II.\n2 TYPE Král český\n2 DATE FROM 1253 TO 1278\n2 NOTE Syn Václava I., pátý český král (korunován 1261). Také vévoda rakouský, štýrský, korutanský a kraňský. Podcenil ale vývoj v římskoněmecké říši a politiku papežské kurie.",
                    "1 EVEN Václav II.\n2 TYPE Král český\n2 DATE FROM 1278 TO 1305\n2 NOTE Syn Přemysla II., šestý český král (korunován 1297). Také polský král (korunován roku 1300). Pro syna získal i uherský trůn.",
                    "1 EVEN Václav III.\n2 TYPE Král český\n2 DATE FROM 1526 TO 1564\n2 NOTE Syn Václava II, sedmý český král. V letech 1301–1304 byl uherským králem jako Ladislav V. Po smrti otce se snažil udržet unii s Polskem, byl však zavražděn. Skončilo tak více než 400 let vlády Přemyslovců v Čechách.",
                    "1 EVEN Jindřich Korutanský\n2 TYPE Král český\n2 DATE 1306\n2 NOTE Menhardovec. Manžel Anny Přemyslovny. Měsíc po jeho volbě za krále se objevila vojska Rudolfa, syna římského krále Albrechta I., a Jindřich s Annou uprchli.",
                    "1 EVEN Rudolf I. Habsburský\n2 TYPE Král český\n2 DATE FROM 1306 TO 1307\n2 NOTE Syn Albrechta I. Dokázal si získat podporu části panstva sliby (i úplatky), ale zemřel při obléhání hradu Bavora III. ze Strakonic.",
                    "1 EVEN Jindřich Korutanský\n2 TYPE Král český\n2 DATE FROM 1307 TO 1310\n2 NOTE Podruhé na trůně. Jindřich nebyl schopný panovník a část panstva požádala o pomoc římského krále Jindřicha VII., jehož syn Jan Lucemburský se měl oženit s princeznou Eliškou Přemyslovnou.",
                    "1 EVEN Jan Lucemburský\n2 TYPE Král český\n2 DATE FROM 1310 TO 1346\n2 NOTE Syn Jindřicha VII. V Janovi se protnuly tři nástupnické principy: Uznala ho česká šlechta. Císař mu udělil Čechy v léno. Oženil se s Eliškou Přemyslovnou.",
                    "1 EVEN Karel IV.\n2 TYPE Král český\n2 DATE FROM 1346 TO 1378\n2 NOTE Syn Jana Lucemburského. Také římský císař. S jeho jménem je spojen největší politický i kulturní rozkvět českých zemí.",
                    "1 EVEN Václav IV.\n2 TYPE Král český\n2 DATE FROM 1378 TO 1419\n2 NOTE Syn Karla IV. Také římský král. Českým králem byl korunován už ve dvou letech. Syna neměl, a tak byl jeho dědicem jeho bratr Zikmund.",
                    "1 EVEN Zikmund\n2 TYPE Král český\n2 DATE FROM 1419 TO 1437\n2 NOTE Syn Karla IV. Také římský císař a uherský král. Roku 1421 ho český zemský sněm jako krále odmítl; plně respektovaným králem byl až poslední dva roky života.",
                    "1 EVEN Albrecht II. Habsburský (Albrecht I.)\n2 TYPE Král český\n2 DATE FROM 1437 TO 1439\n2 NOTE Zikmundův zeť a dědic. Také římský a uherský král. Svých dědických práv se domáhal obtížně, české koruny dosáhl i přes odpor husitů.",
                    "1 EVEN Interregnum\n2 TYPE mezivládí\n2 DATE FROM 1439 TO 1453\n2 NOTE Po Albrechtově smrti byla koruna nabídnuta Albrechtu III. Bavorskému, který ji odmítl. Vznikly šlechtické spolky – sněmíky, landfrídy. Spolupráci landfrídů zajišťovaly zemské sněmy.",
                    "1 EVEN Ladislav Pohrobek\n2 TYPE Král český\n2 DATE FROM 1453 TO 1457\n2 NOTE Syn Albrechta II., narozen až po jeho smrti (= pohrobek). Také uherský král. V Čechách sídlil v letech 1453–1454 a potom už jen dva měsíce před svou smrtí.",
                    "1 EVEN Jiří z Poděbrad\n2 TYPE Král český\n2 DATE FROM 1458 TO 1471\n2 NOTE Zemský správce z doby Ladislava Pohrobka, po jehož smrti se stal králem. Ačkoliv měl potomky, korunu odkázal Jagelloncům.",
                    "1 EVEN Vladislav Jagellonský (Vladislav II.)\n2 TYPE Král český\n2 DATE FROM 1471 TO 1516\n2 NOTE Syn Kazimíra IV. Zvolen českým králem byl podle přání Jiřího z Poděbrad, musel ale až do r. 1478 bojovat s Matyášem Korvínem – vládu v zemích Koruny si nakonec rozdělili. Od roku 1490 také král uherský. V roce 1500 vydal Vladislavské zřízení zemské.",
                    "1 EVEN Ludvík Jagellonský\n2 TYPE Král český\n2 DATE FROM 1516 TO 1526\n2 NOTE Syn Vladislava Jagellonského. Otec ho nechal korunovat již roku 1508 za uherského krále a o rok později za krále českého. Zemřel v bitvě u Moháče, čímž vymřela česko-uherská větev rodu.",
                    "1 EVEN Ferdinand I.\n2 TYPE Král český\n2 DATE FROM 1526 TO 1564\n2 NOTE Vnuk císaře Maxmiliána I., manžel Anny Jagellonské. Také římskoněmecký císař a uherský král.",
                    "1 EVEN Maxmilián I./II.\n2 TYPE Král český\n2 DATE FROM 1564 TO 1576\n2 NOTE Syn Ferdinanda I. Také římskoněmecký císař a uherský král.",
                    "1 EVEN Rudolf II.\n2 TYPE Král český\n2 DATE FROM 1576 TO 1611\n2 NOTE Syn Maxmiliána II. Zároveň král uherský a císař římský. Od r. 1608 vládl jen v Čechách a ve Slezsku. Císař do své smrti v roce 1612.",
                    "1 EVEN Matyáš II.\n2 TYPE Král český\n2 DATE FROM 1611 TO 1619\n2 NOTE Syn Maxmiliána II. Reálně vládl do r. 1618. Současně král uherský a císař římský od r. 1612. Jako král uherský Matyáš II.",
                    "1 EVEN Ferdinand II.\n2 TYPE Král český\n2 DATE 1619\n2 NOTE Matyášův bratranec. Jeho bratranec neměl potomky, a tak byl Ferdinand ještě za jeho života v roce 1617 korunován českým králem.",
                    "1 EVEN Fridrich Falcký\n2 TYPE Král český\n2 DATE FROM 1619 TO 1620\n2 NOTE Wittelsbach. Falcký kurfiřt, který projevoval sympatie k českým protestanstským stavům. Zvolen králem byl ale až v době, kdy bylo stavovské povstání v defenzivě.",
                    "1 EVEN Ferdinand II. Štýrský\n2 TYPE Král český\n2 DATE FROM 1620 TO 1637\n2 NOTE Současně král uherský a císař římský. Reálně vládl od r. 1620. Potlačil české stavovské povstání.",
                    "1 EVEN Ferdinand III.\n2 TYPE Král český\n2 DATE FROM 1637 TO 1657\n2 NOTE Syn Ferdinanda II. Také císař římský a král uherský. Pokračoval v českých zemích v rekatolizaci a posilování absolutistické moci, ovšem už mírnějšími prostředky.",
                    "1 EVEN Ferdinand IV.\n2 TYPE Král český\n2 DATE FROM 1646 TO 1654\n2 NOTE spoluvladař Ferdinanda III., svého otce. Od r. 1647 také král uherský. Reálně nevládl, zemřel za života svého otce.",
                    "1 EVEN Leopold I.\n2 TYPE Král český\n2 DATE FROM 1657 TO 1705\n2 NOTE Syn Ferdinanda III. Také římskoněmecký císař a uherský král.",
                    "1 EVEN Josef I.\n2 TYPE Král\n2 DATE FROM 1705 TO 1711\n2 NOTE Syn Leopolda I. Již v dětských letech byl korunován uherským (v roce 1687) a římskoněmeckým králem (r. 1690). Českým králem Josef ovšem korunován nebyl.",
                    "1 EVEN Karel II./VI.\n2 TYPE Král český\n2 DATE FROM 1711 TO 1740\n2 NOTE Syn Leopolda I. Jako císař římský Karel VI. Současně král uherský. Snažil se pragmatickou sankcí zajistit dědictví nejstarší dceři Marii Terezii.",
                    "1 EVEN Vymření habsburské dynastie po meči\n2 TYPE Dynastie\n2 DATE 1740",
                    "1 EVEN Marie Terezie\n2 TYPE Královna česká\n2 DATE FROM 1740 TO 1780\n2 NOTE Dcera Karla VI. Jediná vládnoucí česká královna. Také královna uherská. O dědictví musela bojovat s Karlem VII. (kurfiřtem bavorským), roku 1743 se nechala korunovat českou královnou. Ženská linie habsburské dynastie.",
                    "1 EVEN Karel Albrecht Bavorský / Karel III.\n2 TYPE Panovník\n2 DATE FROM 1741 TO 1743\n2 NOTE Protikrál, římský císař. Po smrti Karla VI. neuznal Pragmatickou sankci, 9. prosince 1741 se nechal provolat českými stavy králem jako Karel III.",
                    "1 EVEN Josef II.\n2 TYPE Král český\n2 DATE FROM 1780 TO 1790\n2 NOTE Syn Marie Terezie. Také římský císař a uherský král, reformátor.",
                    "1 EVEN Leopold II.\n2 TYPE Král český\n2 DATE FROM 1790 TO 1792\n2 NOTE Syn Marie Terezie. Také římský císař a uherský král. Na rozdíl od bratra se nechal českým králem korunovat.",
                    "1 EVEN František I. Rakouský\n2 TYPE Král český\n2 DATE FROM 1792 TO 1835\n2 NOTE Syn Leopolda II. Také uherský král, římský císař (do r. 1806), poté rakouský císař (od r. 1804).",
                    "1 EVEN Ferdinand I. Dobrotivý (Ferdinand V.)\n2 TYPE Král český\n2 DATE FROM 1835 TO 1848\n2 NOTE Syn Františka I. Také rakouský císař a uherský král. V roce 1848 byl odstaven od českého trůnu a abdikoval ve prospěch synovce Františka Josefa I. Poslední korunovaný český král.",
                    "1 EVEN František Josef I.\n2 TYPE Král a císař\n2 DATE FROM 1848 TO 1916\n2 NOTE Synovec Ferdinanda V. Také rakouský císař a uherský král. Českým zemím vládl více jak 60 let, z vnitropolitických důvodů se však nedal korunovat českým králem. (Ztroskotaly i myšlenky na trialistickou monarchii).",
                    "1 EVEN Karel I. (Karel III.)\n2 TYPE Král český\n2 DATE FROM 1916 TO 1918\n2 NOTE Rakouský císař Karel I., král uherský jako Karel IV. a král český Karel III. (Z časových a politických důvodů nebyl korunován českým králem). Rakousko-Uhersko se mu už zachránit nepodařilo, ačkoliv nabídl federaci.",
                    // Prezidenti:
                    "1 EVEN Tomáš Garrigue Masaryk\n2 TYPE Prezident ČSR\n2 DATE FROM 14 NOV 1918 TO 14 DEC 1935\n2 NOTE První prezident Československa, o jehož vznik se zasloužil, do funkce byl zvolen celkem čtyřikrát.",
                    "1 EVEN Edvard Beneš\n2 TYPE Prezident ČSR\n2 DATE FROM 18 DEC 1935 TO 5 OCT 1938\n2 NOTE Po Mnichovské dohodě abdikoval, v době války vedl exilovou vládu.",
                    "1 EVEN Emil Hácha\n2 TYPE Státní prezident\n2 DATE FROM 30 NOV 1938 TO 9 MAY 1945\n2 NOTE Prezidentem druhé republiky (1938–1939) a státní prezident Protektorátu Čechy a Morava (1939–1945). Wikipedie [Emil Hácha](https://cs.wikipedia.org/wiki/Emil_H%C3%A1cha)",
                    "1 EVEN Edvard Beneš\n2 TYPE Prezident ČSR\n2 DATE FROM 02 APR 1945 TO 7 JUN 1948",
                    "1 EVEN Klement Gottwald\n2 TYPE Prezident ČSR\n2 DATE FROM 14 JUN 1948 TO 14 MAR 1953\n2 NOTE Poválečný premiér, po komunistickém převratu v únoru 1948 se stal prezidentem. První dělnický prezident, jak říkali komunisti",
                    "1 EVEN Antonín Zápotocký\n2 TYPE Prezident ČSR\n2 DATE FROM 21 MAR 1953 TO 13 NOV 1957\n2 NOTE Druhý komunistický prezident. Rezignoval na počáteční reformní snahy.",
                    "1 EVEN Antonín Novotný\n2 TYPE Prezident ČSR/ČSSR\n2 DATE FROM 19 NOV 1957 TO 28 MAR 1968\n2 NOTE V době jeho vlády došlo k jakémusi uvolnění a k částečné rehabilitaci některých nespravedlivě odsouzených v 50. letech.",
                    "1 EVEN Ludvík Svoboda\n2 TYPE Prezident ČSSR\n2 DATE FROM 30 MAR 1968 TO 28 MAY 1975\n2 NOTE Zvolen v březnu 1968. Po srpnové invazi odmítl kolaborantskou vládu, poté byl ale jedním z hlavních normalizátorů.",
                    "1 EVEN Gustáv Husák\n2 TYPE Prezident ČSSR\n2 DATE FROM 29 MAY 1975 TO 10 DEC 1989\n2 NOTE Na moskevských jednáních v srpnu roku 1968 Husák „změnil kurs“. V roce 1969 se dostal do čela KSČ.",
                    "1 EVEN Václav Havel\n2 TYPE Prezident ČSFR\n2 DATE FROM 29 DEC 1989 TO 20 JUL 1992\n2 NOTE Disident, mluvčí Charty 77 a jedna z vůdčích osobností Sametové revoluce.",
                    "1 EVEN Václav Havel\n2 TYPE Prezident ČR\n2 DATE FROM 2 FEB 1993 TO 2 FEB 2003\n2 NOTE Poslední československý prezident a první český prezident.",
                    "1 EVEN Václav Klaus\n2 TYPE Prezident ČR\n2 DATE FROM 07 MAR 2003 TO 4 MAR 2013\n2 NOTE Federální ministr financí, premiér a předseda Poslanecké sněmovny z 90. let.",
                    "1 EVEN Miloš Zeman\n2 TYPE Prezident ČR\n2 DATE FROM 08 MAR 2013\n2 NOTE Premiér a předseda Poslanecké sněmovny z 90. let, historicky první přímo zvolený prezident České republiky.",
                ]);

            default:
                return new Collection();
        }
    }
}
