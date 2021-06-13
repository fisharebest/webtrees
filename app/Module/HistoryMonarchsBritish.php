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
 * Class HistoryMonarchsBritish
 */
class HistoryMonarchsBritish extends AbstractModule implements ModuleHistoricEventsInterface
{
    use ModuleHistoricEventsTrait;

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        return 'British monarchs 🇬🇧';
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
            "1 EVEN William I, William the Conqueror (c. 1028 — 1087)\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 25 DEC 1066 TO @#DJULIAN@ 09 SEP 1087\n2 SOUR [Wikipedia: William the Conqueror](https://en.wikipedia.org/wiki/William_the_Conqueror)",
            "1 EVEN William II\n2 TYPE English King (c. 1056 — 1100)\n2 DATE FROM @#DJULIAN@ 26 SEP 1087 TO @#DJULIAN@ 02 AUG 1100\n2 SOUR [Wikipedia: William II of England](https://en.wikipedia.org/wiki/William_II_of_England)",
            "1 EVEN Henry I (c. 1068 — 1135)\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 05 AUG 1100 TO @#DJULIAN@ 01 DEC 1135\n2 SOUR [Wikipedia: Henry I of England](https://en.wikipedia.org/wiki/Henry_I_of_England)",
            "1 EVEN Stephen (1092 or 1096 — 1154)\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 22 DEC 1135 TO @#DJULIAN@ 25 OCT 1154\n2 SOUR [Wikipedia: Stephen, King of England](https://en.wikipedia.org/wiki/Stephen,_King_of_England)",
            "1 EVEN Henry II (1133 — 1189)\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 19 DEC 1154 TO @#DJULIAN@ 06 JUL 1189\n2 SOUR [Wikipedia: Henry II of England](https://en.wikipedia.org/wiki/Henry_II_of_England)",
            "1 EVEN Richard I, Richard the Lionheart (1157 — 1199)\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 03 SEP 1189 TO @#DJULIAN@ 06 APR 1199\n2 SOUR [Wikipedia: Richard I of England](https://en.wikipedia.org/wiki/Richard_I_of_England)",
            "1 EVEN John (1166 — 1216)\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 27 MAY 1199 TO @#DJULIAN@ 19 OCT 1216\n2 SOUR [Wikipedia: John, King of England](https://en.wikipedia.org/wiki/John,_King_of_England)",
            "1 EVEN Louis VIII, Louis the Lion (1187 — 1226)\n2 TYPE Disputed English King and King of France\n2 DATE FROM @#DJULIAN@ NOV 1216 TO @#DJULIAN@ 22 SEP 1217\n2 SOUR [Wikipedia: Louis VIII of France](https://en.wikipedia.org/wiki/Louis_VIII_of_France)",
            "1 EVEN Henry III (1207 — 1272)\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 28 OCT 1216 TO @#DJULIAN@ 16 NOV 1272\n2 SOUR [Wikipedia: Henry III of England](https://en.wikipedia.org/wiki/Henry_III_of_England)",
            "1 EVEN Edward I (1239 — 1307)\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 20 NOV 1272 TO @#DJULIAN@ 07 JUL 1307\n2 SOUR [Wikipedia: Edward I of England](https://en.wikipedia.org/wiki/Edward_I_of_England)",
            "1 EVEN Edward II (1284 — 1327)\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 08 JUL 1307 TO @#DJULIAN@ 20 JAN 1327\n2 SOUR [Wikipedia: Edward II of England](https://en.wikipedia.org/wiki/Edward_II_of_England)",
            "1 EVEN Edward III (1312 — 1377)\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 25 JAN 1327 TO @#DJULIAN@ 21 JUN 1377\n2 SOUR [Wikipedia: Edward III of England](https://en.wikipedia.org/wiki/Edward_III_of_England)",
            "1 EVEN Richard II (1367 — 1400)\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 22 JUN 1377 TO @#DJULIAN@ 29 SEP 1399\n2 SOUR [Wikipedia: Richard II of England](https://en.wikipedia.org/wiki/Richard_II_of_England)",
            "1 EVEN Henry IV (1367 — 1413)\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 30 SEP 1399 TO @#DJULIAN@ 21 MAR 1413\n2 SOUR [Wikipedia: Henry IV of England](https://en.wikipedia.org/wiki/Henry_IV_of_England)",
            "1 EVEN Henry V (1386 — 1422)\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 21 MAR 1413 TO @#DJULIAN@ 01 SEP 1422\n2 SOUR [Wikipedia: Henry V of England](https://en.wikipedia.org/wiki/Henry_V_of_England)",
            "1 EVEN Henry VI (1421 — 1471)\n2 TYPE English King and Disputed King of France\n2 DATE FROM @#DJULIAN@ 01 SEP 1422 TO @#DJULIAN@ 04 MAR 1461\n2 SOUR [Wikipedia: Henry VI of England](https://en.wikipedia.org/wiki/Henry_VI_of_England)",
            "1 EVEN Edward IV (1442 — 1483)\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 04 MAR 1461 TO @#DJULIAN@ 03 OCT 1470\n2 SOUR [Wikipedia: Edward IV of England](https://en.wikipedia.org/wiki/Edward_IV_of_England)",
            "1 EVEN Henry VI (1421 — 1471)\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 03 OCT 1470 TO @#DJULIAN@ 11 APR 1471\n2 SOUR [Wikipedia: Edward IV of England](https://en.wikipedia.org/wiki/Edward_IV_of_England)",
            "1 EVEN Edward IV (1442 — 1483)\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 11 APR 1471 TO @#DJULIAN@ 09 APR 1483\n2 SOUR [Wikipedia: Edward IV of England](https://en.wikipedia.org/wiki/Edward_IV_of_England)",
            "1 EVEN Edward V (1470 — 1483)\n2 TYPE Uncrowned English King\n2 DATE FROM @#DJULIAN@ 09 APR 1483 TO @#DJULIAN@ 26 JUN 1483\n2 SOUR [Wikipedia: Edward IV of England](https://en.wikipedia.org/wiki/Edward_IV_of_England)",
            "1 EVEN Richard III (1452 — 1485)\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 26 JUN 1483 TO @#DJULIAN@ 22 AUG 1485\n2 SOUR [Wikipedia: Richard III of England](https://en.wikipedia.org/wiki/Richard_III_of_England)",
            "1 EVEN Henry VII (1457 — 1509)\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 22 AUG 1485 TO @#DJULIAN@ 22 APR 1509\n2 SOUR [Wikipedia: Henry VII of England](https://en.wikipedia.org/wiki/Henry_VII_of_England)",
            "1 EVEN Henry VIII (1491 — 1547)\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 22 APR 1509 TO @#DJULIAN@ 28 JAN 1547\n2 SOUR [Wikipedia: Henry VIII](https://en.wikipedia.org/wiki/Henry_VIII)",
            "1 EVEN Edward VI (1537 — 1553)\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 28 JAN 1547 TO @#DJULIAN@ 10 JUL 1553\n2 SOUR [Wikipedia: Edward VI of England](https://en.wikipedia.org/wiki/Edward_VI_of_England)",
            "1 EVEN Lady Jane Grey (c. 1536 — 1554)\n2 TYPE Disputed English Queen\n2 DATE FROM @#DJULIAN@ 10 JUL 1553 TO @#DJULIAN@ 19 JUL 1553\n2 SOUR [Wikipedia: Lady Jane Grey](https://en.wikipedia.org/wiki/Lady_Jane_Grey)",
            "1 EVEN Mary I (1516 — 1558)\n2 TYPE English Queen\n2 DATE FROM @#DJULIAN@ 19 JUL 1553 TO @#DJULIAN@ 17 NOV 1558\n2 SOUR [Wikipedia: Mary I of England](https://en.wikipedia.org/wiki/Mary_I_of_England)",
            "1 EVEN Philip II (1527 — 1598), Philip the Prudent\n2 TYPE English King jure uxoris and King of Spain and Portugal\n2 DATE FROM @#DJULIAN@ 25 JUL 1554 TO @#DJULIAN@ 17 NOV 1558\n2 SOUR [Wikipedia: Philip II of Spain](https://en.wikipedia.org/wiki/Philip_II_of_Spain)",
            "1 EVEN Elizabeth I (1533 — 1603)\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 17 NOV 1558 TO @#DJULIAN@ 24 MAR 1603\n2 SOUR [Wikipedia: Elizabeth I](https://en.wikipedia.org/wiki/Elizabeth_I)",
            "1 EVEN James VI and I (1566 — 1625)\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 24 MAR 1603 TO @#DJULIAN@ 27 MAR 1625\n2 SOUR [Wikipedia: James VI and I](https://en.wikipedia.org/wiki/James_VI_and_I)",
            "1 EVEN Charles I (1600 — 1649)\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 27 MAR 1625 TO @#DJULIAN@ 30 JAN 1649\n2 SOUR [Wikipedia: Charles I of England](https://en.wikipedia.org/wiki/Charles_I_of_England)",
            "1 EVEN Charles II (1630 — 1685)\n2 TYPE English King de jure\n2 DATE FROM @#DJULIAN@ 05 FEB 1649 TO @#DJULIAN@ 15 OCT 1651\n2 SOUR [Wikipedia: Charles II of England](https://en.wikipedia.org/wiki/Charles_II_of_England)",
            "1 EVEN Charles II (1630 — 1685)\n2 TYPE English King in excile\n2 DATE FROM @#DJULIAN@ 15 OCT 1651 TO @#DJULIAN@ 29 MAY 1660\n2 SOUR [Wikipedia: Charles II of England](https://en.wikipedia.org/wiki/Charles_II_of_England)",
            "1 EVEN Oliver Cromwell (1599 — 1658)\n2 TYPE Lord Protector\n2 DATE FROM @#DJULIAN@ 16 DEC 1653 TO @#DJULIAN@ 03 SEP 1658\n2 SOUR [Wikipedia: Oliver Cromwell](https://en.wikipedia.org/wiki/Oliver_Cromwell)",
            "1 EVEN Richard Cromwell (1626 — 1712)\n2 TYPE Lord Protector\n2 DATE FROM @#DJULIAN@ 03 SEP 1658 TO @#DJULIAN@ 07 MAY 1659\n2 SOUR [Wikipedia: Richard Cromwell](https://en.wikipedia.org/wiki/Richard_Cromwell)",
            "1 EVEN Charles II (1630 — 1685)\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 29 MAY 1660 TO @#DJULIAN@ 06 FEB 1685\n2 SOUR [Wikipedia: Charles II of England](https://en.wikipedia.org/wiki/Charles_II_of_England)",
            "1 EVEN James II and VII (1633 — 1701)\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 06 FEB 1685 TO @#DJULIAN@ 13 FEB 1689\n2 SOUR [Wikipedia: James II of England](https://en.wikipedia.org/wiki/James_II_of_England)",
            "1 EVEN William III and II (1650 — 1702)\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 13 FEB 1689 TO @#DJULIAN@ 08 MAR 1702\n2 SOUR [Wikipedia: William III of England](https://en.wikipedia.org/wiki/William_III_of_England)",
            "1 EVEN Mary II (1662 — 1694)\n2 TYPE English Queen, co-reigning with William III\n2 DATE FROM @#DJULIAN@ 13 FEB 1689 TO @#DJULIAN@ 13 FEB 1689\n2 SOUR [Wikipedia: Mary II of England](https://en.wikipedia.org/wiki/Mary_II_of_England)",
            "1 EVEN Anne (1665 — 1714)\n2 TYPE English Queen\n2 DATE FROM @#DJULIAN@ 08 MAR 1702 TO @#DJULIAN@ 01 MAY 1707\n2 SOUR [Wikipedia: Anne, Queen of Great Britain](https://en.wikipedia.org/wiki/Anne,_Queen_of_Great_Britain)",
            "1 EVEN Anne (1665 — 1714)\n2 TYPE British Queen\n2 DATE FROM @#DJULIAN@ 01 MAY 1707 TO @#DJULIAN@ 01 AUG 1714\n2 SOUR [Wikipedia: Anne, Queen of Great Britain](https://en.wikipedia.org/wiki/Anne,_Queen_of_Great_Britain)",
            "1 EVEN George I (1660 — 1727)\n2 TYPE British King\n2 DATE FROM @#DJULIAN@ 01 AUG 1714 TO @#DJULIAN@ 11 JUN 1727\n2 SOUR [Wikipedia: George I of Great Britain](https://en.wikipedia.org/wiki/George_I_of_Great_Britain)",
            "1 EVEN George II (1683 — 1760)\n2 TYPE British King\n2 DATE FROM @#DJULIAN@ 11 JUN 1727 TO 25 OCT 1760\n2 SOUR [Wikipedia: George II of Great Britain](https://en.wikipedia.org/wiki/George_II_of_Great_Britain)",
            "1 EVEN George III (1738 — 1820)\n2 TYPE British King\n2 DATE FROM 25 OCT 1760 TO 29 JAN 1820\n2 SOUR [Wikipedia: George III](https://en.wikipedia.org/wiki/George_III)",
            "1 EVEN George IV (1762 — 1830)\n2 TYPE British King\n2 DATE FROM 29 JAN 1820 TO 26 JUN 1830\n2 SOUR [Wikipedia: George IV](https://en.wikipedia.org/wiki/George_IV)",
            "1 EVEN William IV (1765 — 1837)\n2 TYPE British King\n2 DATE FROM 26 JUN 1830 TO 20 JUN 1837\n2 SOUR [Wikipedia: William IV](https://en.wikipedia.org/wiki/William_IV)",
            "1 EVEN Victoria (1819 — 1901)\n2 TYPE British Queen\n2 DATE FROM 20 JUN 1837 TO 22 JAN 1901\n2 SOUR [Wikipedia: Queen Victoria](https://en.wikipedia.org/wiki/Queen_Victoria)",
            "1 EVEN Edward VII (1841 — 1910)\n2 TYPE British King\n2 DATE FROM 22 JAN 1901 TO 06 MAY 1910\n2 SOUR [Wikipedia: Edward VII](https://en.wikipedia.org/wiki/Edward_VII)",
            "1 EVEN George V (1865 — 1936)\n2 TYPE British King\n2 DATE FROM 06 MAY 1910 TO 20 JAN 1936\n2 SOUR [Wikipedia: George V](https://en.wikipedia.org/wiki/George_V)",
            "1 EVEN Edward VIII (1894 — 1972)\n2 TYPE British King\n2 DATE FROM 20 JAN 1936 TO 11 DEC 1936\n2 SOUR [Wikipedia: Edward VIII](https://en.wikipedia.org/wiki/Edward_VIII)",
            "1 EVEN George VI (1895 — 1952)\n2 TYPE British King\n2 DATE FROM 11 DEC 1936 TO 06 FEB 1952\n2 SOUR [Wikipedia: George VI](https://en.wikipedia.org/wiki/George_VI)",
            "1 EVEN Elizabeth II (* 1926)\n2 TYPE British Queen\n2 DATE FROM 06 FEB 1952\n2 SOUR [Wikipedia: Elizabeth II](https://en.wikipedia.org/wiki/Elizabeth_II)",
        ]);
    }
}
