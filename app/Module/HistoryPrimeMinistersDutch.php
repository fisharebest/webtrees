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
 * Class HistoryPrimeMinistersDutch
 */
class HistoryPrimeMinistersDutch extends AbstractModule implements ModuleHistoricEventsInterface
{
    use ModuleHistoricEventsTrait;

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        return 'Lijst van premiers en kabinetten van Nederland 🇳🇱';
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
            "1 EVEN Gerrit graaf Schimmelpenninck (1794 — 1863), partijloos-liberaal\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 25 MAR 1848 TO 21 NOV 1848\n2 NOTE kabinet-Schimmelpenninck\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Gerrit_Schimmelpenninck_(1794-1863))",
            "1 EVEN Jacob Mattheus de Kempenaer (1793 — 1870), partijloos-liberaal\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 21 NOV 1848 TO 01 NOV 1849\n2 NOTE kabinet-De Kempenaer-Donker Curtius\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Jacobus_Mattheüs_de_Kempenaer)",
            "1 EVEN Johan Rudolph Thorbecke (1798 — 1872), partijloos-liberaal\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 01 NOV 1849 TO 19 APR 1853\n2 NOTE kabinet-Thorbecke I\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Johan_Rudolph_Thorbecke)",
            "1 EVEN Floris Adriaan van Hall (1791 — 1866), partijloos-liberaal\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 19 APR 1853 TO 01 JUL 1856\n2 kabinet-Van Hall-Donker Curtius\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Floris_Adriaan_van_Hall_(politicus))",
            "1 EVEN Justinus Jacobus Leonard van der Brugghen (1804 — 1863), partijloos-antirevolutionair\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 01 JUL 1856 TO 18 MAR 1858\n2 kabinet-Van der Brugghen\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Justinus_van_der_Brugghen)",
            "1 EVEN Jan Jacob Rochussen (1797 — 1871), partijloos-conservatief\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 18 MAR 1858 TO 23 FEB 1860\n2 kabinet-Rochussen\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Jan_Jacob_Rochussen)",
            "1 EVEN Floris Adriaan van Hall (1791 — 1866), partijloos-liberaal\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 23 FEB 1860 TO 14 MAR 1861\n2 kabinet-Van Hall-Van Heemstra\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Floris_Adriaan_van_Hall_(politicus))",
            "1 EVEN Pieter Pompejus baron van Zuylen van Nijevelt (1816 — 1890), partijloos-liberaal\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 14 MAR 1861 TO 10 NOV 1861\n2 kabinet-Van Zuylen van Nijevelt-Van Heemstra\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Jacob_Pieter_Pompejus_van_Zuylen_van_Nijevelt)",
            "1 EVEN Schelto baron van Heemstra (1807 — 1864), partijloos-liberaal\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 10 NOV 1861 TO 01 FEB 1862\n2 kabinet-Van Zuylen van Nijevelt-Van Heemstra\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Schelto_van_Heemstra_(1807-1864))",
            "1 EVEN Johan Rudolph Thorbeckel (1798 — 1872), partijloos-liberaal\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 01 FEB 1862 TO 10 FEB 1866\n2 kabinet-Thorbecke II\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Johan_Rudolph_Thorbecke)",
            "1 EVEN Isaäc Dignus Fransen van de Puttel (1822 — 1902), partijloos-liberaal\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 10 FEB 1866 TO 01 JUN 1866\n2 kabinet-Fransen van de Putte\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Isa%C3%A4c_Dignus_Fransen_van_de_Putte)",
            "1 EVEN Julius Philip Jacob Adriaan graaf van Zuylen van Nijevelt (1819 — 1894), partijloos-conservatief\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 01 JUN 1866 TO 04 JUN 1868\n2 kabinet-Van Zuylen van Nijevelt\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Jules_van_Zuylen_van_Nijevelt)",
            "1 EVEN Pieter Philip van Bosse (1809 — 1879), partijloos-liberaal\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 04 JUN 1868 TO 04 JAN 1871\n2 kabinet-Van Bosse-Fock\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Pieter_Philip_van_Bosse)",
            "1 EVEN Johan Rudolph Thorbecke (1798 — 1872), partijloos-liberaal\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 04 JAN 1871 TO 04 JUN 1872\n2 kabinet-Thorbecke III\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Johan_Rudolph_Thorbecke)",
            "1 EVEN Gerrit de Vries Azn. (1818 — 1900), partijloos-liberaal\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 06 JUL 1872 TO 27 AUG 1874\n2 kabinet-De Vries-Fransen van de Putte\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Gerrit_de_Vries_Azn)",
            "1 EVEN Jan Heemskerk Azn. (1818 — 1897), partijloos-conservatief\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 27 AUG 1874 TO 03 NOV 1877\n2 kabinet-Heemskerk-Van Lynden van Sandenburg\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Jan_Heemskerk_Azn.)",
            "1 EVEN Johannes Kappeyne van de Coppello, (1822 — 1895), partijloos-liberaal\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 03 NOV 1877 TO 20 AUG 1879\n2 kabinet-Kappeyne van de Coppello\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Jan_Kappeyne_van_de_Coppello)",
            "1 EVEN Constant Théodore graaf van Lynden van Sandenburg (1822 — 1895), partijloos-antirevolutionair\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 20 AUG 1879 TO 23 APR 1883\n2 kabinet-Van Lynden van Sandenburg\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Constantijn_Theodoor_van_Lynden_van_Sandenburg)",
            "1 EVEN Jan Heemskerk Azn. (1818 — 1897), partijloos-conservatief\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 23 APR 1883 TO 30 MAR 1888\n2 kabinet-Heemskerk Azn.\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Jan_Heemskerk_Azn.)",
            "1 EVEN Æneas baron Mackay (1838 — 1909), Anti-Revolutionaire Partij\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 21 APR 1888 TO 21 AUG 1891\n2 kabinet-Mackay\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/%C3%86neas_Mackay_(1838-1909))",
            "1 EVEN Gijsbert van Tienhoven (1841 — 1914), partijloos-liberaal\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 21 AUG 1891 TO 09 MAY 1894\n2 kabinet-Van Tienhoven\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Gijsbert_van_Tienhoven)",
            "1 EVEN Jhr. Joan Röell (1844 — 1914), partijloos-liberaal\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 09 MAI 1894 TO 27 JUL 1897\n2 kabinet-Röell\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Joan_Röell)",
            "1 EVEN Nicolaas Gerard Pierson (1839 — 1909), Liberale Unie\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 27 JUL 1897 TO 01 AUG 1901\n2 kabinet-Pierson\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Nicolaas_Pierson_(politicus))",
            "1 EVEN Abraham Kuyperj (1837 — 1920), Anti-Revolutionaire Parti\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 01 AUG 1901 TO 17 AUG 1905\n2 kabinet-Kuyper\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Abraham_Kuyper)",
            "1 EVEN Theodoor Herman de Meester (1851 — 1919), Liberale Unie\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 17 AUG 1905 TO 12 FEB 1908\n2 kabinet-De Meester\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Theo_de_Meester)",
            "1 EVEN Theodorus Heemskerk (1852 — 1932), Anti-Revolutionaire Partij\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 12 FEB 1908 TO 29 AUG 1913\n2 kabinet-Heemskerk\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Theo_Heemskerk)",
            "1 EVEN Pieter Wilhelm Adrianus Cort van der Linden (1846 — 1935), partijloos-liberaal\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 29 AUG 1913 TO 09 SEP 1918\n2 kabinet-Cort van der Linden\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Pieter_Cort_van_der_Linden)",
            "1 EVEN Jhr. Charles Joseph Marie Ruijs de Beerenbrouck (1873 — 1936), Roomsch-Katholieke Staatspartij\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 09 SEP 1918 TO 18 SEP 1922\n2 kabinet-Ruijs de Beerenbrouck I\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Charles_Ruijs_de_Beerenbrouck)",
            "1 EVEN Jhr. Charles Joseph Marie Ruijs de Beerenbrouck (1873 — 1936), Roomsch-Katholieke Staatspartij\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 18 SEP 1922 TO 04 AUG 1925\n2 kabinet-Ruijs de Beerenbrouck II\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Charles_Ruijs_de_Beerenbrouck)",
            "1 EVEN Hendrikus Colijn (1869 — 1944), Anti-Revolutionaire Partij\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 04 AUG 1925 TO 08 MAR 1926\n2 kabinet-Colijn I\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Hendrikus_Colijn)",
            "1 EVEN Jhr. Dirk Jan de Geer (1870 — 1960), Christelijk-Historische Unie\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 08 MAR 1926 TO 10 AUG 1929\n2 kabinet-De Geer I\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Dirk_Jan_de_Geer)",
            "1 EVEN Jhr. Charles Joseph Marie Ruijs de Beerenbrouck (1873 — 1936), Roomsch-Katholieke Staatspartij\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 10 AUG 1929 TO 26 MAY 1933\n2 kabinet-Ruijs de Beerenbrouck III\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Charles_Ruijs_de_Beerenbrouck)",
            "1 EVEN Hendrikus Colijn (1869 — 1944), Anti-Revolutionaire Partij\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 26 MAY 1933 TO 31 JUL 1935\n2 kabinet-Colijn II\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Hendrikus_Colijn)",
            "1 EVEN Hendrikus Colijn (1869 — 1944), Anti-Revolutionaire Partij\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 31 JUL 1935 TO 24 JUN 1937\n2 kabinet-Colijn III\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Hendrikus_Colijn)",
            "1 EVEN Hendrikus Colijn (1869 — 1944), Anti-Revolutionaire Partij\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 24 JUN 1937 TO 25 JUL 1939\n2 kabinet-Colijn IV\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Hendrikus_Colijn)",
            "1 EVEN Hendrikus Colijn (1869 — 1944), Anti-Revolutionaire Partij\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 25 JUL 1939 TO 10 AUG 1939\n2 kabinet-Colijn V\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Hendrikus_Colijn)",
            "1 EVEN Jhr. Dirk Jan de Geer (1870 — 1960), Anti-Revolutionaire Partij, Christelijk-Historische Unie\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 10 AUG 1939 TO 03 SEP 1940\n2 kabinet-De Geer II (Londens kabinet in ballingschap)\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Charles_Ruijs_de_Beerenbrouck)",
            "1 EVEN Pieter Sjoerds Gerbrandy (1885 — 1961), Anti-Revolutionaire Partij\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 03 SEP 1940 TO 27 JUL 1941\n2 kabinet-Gerbrandy I (Londens kabinet in ballingschap)\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Pieter_Sjoerds_Gerbrandy)",
            "1 EVEN Pieter Sjoerds Gerbrandy (1885 — 1961), Anti-Revolutionaire Partij\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 27 JUL 1941 TO 23 FEB 1945\n2 kabinet-Gerbrandy II (Londens kabinet in ballingschap\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Pieter_Sjoerds_Gerbrandy))",
            "1 EVEN Pieter Sjoerds Gerbrandy (1885 — 1961), Anti-Revolutionaire Partij\n2 TYPE Voorzitter van de ministerraad\n2 DATE FROM 23 FEB 1945 TO 25 JUN 1945\n2 kabinet-Gerbrandy III (Londens kabinet in ballingschap)\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Pieter_Sjoerds_Gerbrandy)",
            "1 EVEN Willem Schermerhorn (1894 — 1977), Vrijzinnig-Democratische Bond / Partij van de Arbeid\n2 TYPE Minister-President\n2 DATE FROM 25 JUN 1945 TO 03 JUL 1946\n2 kabinet-Schermerhorn-Drees\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Wim_Schermerhorn)",
            "1 EVEN Louis Joseph Maria Beel (1902 — 1977), Katholieke Volkspartij\n2 TYPE Minister-President\n2 DATE FROM 03 JUL 1946 TO 07 AUG 1948\n2 kabinet-Beel I\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Louis_Beel)",
            "1 EVEN Willem Drees (1886 — 1988), Partij van de Arbeid \n2 TYPE Minister-President\n2 DATE FROM 07 AUG 1948 TO 15 MAR 1951\n2 kabinet-Drees Van Schaik\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Willem_Drees)",
            "1 EVEN Willem Drees (1886 — 1988), Partij van de Arbeid \n2 TYPE Minister-President\n2 DATE FROM 15 MAR 1951 TO 02 SEP 1952\n2 kabinet-Drees I\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Willem_Drees)",
            "1 EVEN Willem Drees (1886 — 1988), Partij van de Arbeid \n2 TYPE Minister-President\n2 DATE FROM 02 SEP 1952 TO 13 OCT 1956\n2 kabinet-Drees II\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Willem_Drees)",
            "1 EVEN Willem Drees (1886 — 1988), Partij van de Arbeid \n2 TYPE Minister-President\n2 DATE FROM 13 OCT 1956 TO 22 DEC 1958\n2 kabinet-Drees III\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Willem_Drees)",
            "1 EVEN Louis Joseph Maria Beel (1902 — 1977, Katholieke Volkspartij)\n2 TYPE Minister-President\n2 DATE FROM 22 DEC 1958 TO 19 MAY 1959\n2 kabinet-Beel II\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Louis_Beel)",
            "1 EVEN Jan Eduard de Quay, (1901 — 1985), Katholieke Volkspartij\n2 TYPE Minister-President\n2 DATE FROM 19 MAY 1959 TO 24 JUL 1963\n2 kabinet-De Quay\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Jan_de_Quay)",
            "1 EVEN Victor Gerard Marie Marijnen (1917 — 1975), Katholieke Volkspartij\n2 TYPE Minister-President\n2 DATE FROM 24 JUL 1963 TO 14 APR 1965\n2 kabinet-Marijnen\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Victor_Marijnen)",
            "1 EVEN Jozef Maria Laurens Theo Cals (1914 — 1971), Katholieke Volkspartij\n2 TYPE Minister-President\n2 DATE FROM 14 APR 1965 TO 22 NOV 1966\n2 kabinet-Cals\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Jo_Cals)",
            "1 EVEN Jelle Zijlstra (1918 — 2001), Anti-Revolutionaire Partij\n2 TYPE Minister-President\n2 DATE FROM 22 NOV 1966 TO 05 APR 1967\n2 kabinet-Zijlstra\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Jelle_Zijlstra)",
            "1 EVEN Petrus Jozef Sietse de Jong (1915 — 2016), Katholieke Volkspartij\n2 TYPE Minister-President\n2 DATE FROM 05 APR 1967 TO 06 JUL 1971\n2 kabinet-De Jong\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Piet_de_Jong_(politicus))",
            "1 EVEN Barend Willem Biesheuvel (1920 — 2001), Anti-Revolutionaire Partij\n2 TYPE Minister-President\n2 DATE FROM 06 JUL 1971 TO 09 AUG 1972\n2 kabinet-Biesheuvel I\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Barend_Biesheuvel)",
            "1 EVEN Barend Willem Biesheuvel (1920 — 2001), Anti-Revolutionaire Partij\n2 TYPE Minister-President\n2 DATE FROM 09 AUG 1972 TO 11 MAY 1973\n2 kabinet-Biesheuvel II\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Barend_Biesheuvel)",
            "1 EVEN Johannes Marten den Uyl (1919 — 1987), Partij van de Arbeid\n2 TYPE Minister-President\n2 DATE FROM 11 MAY 1973 TO 19 DEC 1977\n2 kabinet-Den Uyl\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Joop_den_Uyl)",
            "1 EVEN Andreas Antonius Maria van Agt (* 1931), Christen-Democratisch Appèl\n2 TYPE Minister-President\n2 DATE FROM 19 DEC 1977 TO 11 SEP 1981\n2 kabinet-Van Agt I\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Dries_van_Agt)",
            "1 EVEN Andreas Antonius Maria van Agt (* 1931), Christen-Democratisch Appèl\n2 TYPE Minister-President\n2 DATE FROM 11 SEP 1981 TO 29 MAY 1982\n2 kabinet-Van Agt II\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Dries_van_Agt)",
            "1 EVEN Andreas Antonius Maria van Agt (* 1931), Christen-Democratisch Appèl\n2 TYPE Minister-President\n2 DATE FROM 29 MAY 1982 TO 04 NÒV 1982\n2 kabinet-Van Agt III\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Dries_van_Agt)",
            "1 EVEN Rudolphus Franciscus Marie Lubbers (1939 — 2018), Christen-Democratisch Appèl\n2 TYPE Minister-President\n2 DATE FROM 04 NÒV 1982 TO 14 JUL 1986\n2 kabinet-Lubbers I\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Ruud_Lubbers)",
            "1 EVEN Rudolphus Franciscus Marie Lubbers (1939 — 2018), Christen-Democratisch Appèl\n2 TYPE Minister-President\n2 DATE FROM 14 JUL 1986 TO 07 NOV 1989\n2 kabinet-Lubbers II\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Ruud_Lubbers)",
            "1 EVEN Rudolphus Franciscus Marie Lubbers (1939 — 2018), Christen-Democratisch Appèl\n2 TYPE Minister-President\n2 DATE FROM 07 NOV 1989 TO 22 AUG 1994\n2 kabinet-Lubbers III\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Ruud_Lubbers)",
            "1 EVEN Willem Kok (1938 — 2018), Partij van de Arbeid\n2 TYPE Minister-President\n2 DATE FROM 22 AUG 1994 TO 03 AUG 1998\n2 kabinet-Kok I\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Wim_Kok)",
            "1 EVEN Willem Kok (1938 — 2018), Partij van de Arbeid\n2 TYPE Minister-President\n2 DATE FROM 03 AUG 1998 TO 22 JUL 2002\n2 kabinet-Kok II\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Wim_Kok)",
            "1 EVEN Jan Pieter Balkenende (* 1956), Christen-Democratisch Appèl\n2 TYPE Minister-President\n2 DATE FROM 22 JUL 2002 TO 27 MAY 2003\n2 kabinet-Balkenende I\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Jan_Peter_Balkenende)",
            "1 EVEN Jan Pieter Balkenende (* 1956), Christen-Democratisch Appèl\n2 TYPE Minister-President\n2 DATE FROM 27 MAY 2003 TO 07 JUL 2006\n2 kabinet-Balkenende II\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Jan_Peter_Balkenende)",
            "1 EVEN Jan Pieter Balkenende (* 1956), Christen-Democratisch Appèl\n2 TYPE Minister-President\n2 DATE FROM 07 JUL 2006\ TO 22 FEB 2007\n2 kabinet-Balkenende III\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Jan_Peter_Balkenende)",
            "1 EVEN Jan Pieter Balkenende (* 1956), Christen-Democratisch Appèl\n2 TYPE Minister-President\n2 DATE FROM 22 FEB 2007 TO 26 OCT 2010\n2 kabinet-Balkenende IV\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Jan_Peter_Balkenende)",
            "1 EVEN Mark Rutte (* 1967), Volkspartij voor Vrijheid en Democratie\n2 TYPE Minister-President\n2 DATE FROM 26 OCT 2010 TO 05 NOV 2012\n2 kabinet-Rutte I\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Mark_Rutte)",
            "1 EVEN Mark Rutte (* 1967), Volkspartij voor Vrijheid en Democratie\n2 TYPE Minister-President\n2 DATE FROM 05 NOV 2012 TO 26 OCT 2017\n2 kabinet-Rutte II\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Mark_Rutte)",
            "1 EVEN Mark Rutte (* 1967), Volkspartij voor Vrijheid en Democratie\n2 TYPE Minister-President\n2 DATE FROM 26 OCT 2017\n2 kabinet-Rutte III\n2 SOUR [Wikipedia: Lijst van premiers van Nederland](https://nl.wikipedia.org/wiki/Mark_Rutte)",
        ]);
    }
}
