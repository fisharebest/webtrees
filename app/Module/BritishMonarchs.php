<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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
 * Class BritishMonarchs
 */
class BritishMonarchs extends AbstractModule implements ModuleHistoricEventsInterface
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
     * @return Collection<int,string>
     */
    public function historicEventsAll(): Collection
    {
        switch (I18N::languageTag()) {
            case 'en-AU':
            case 'en-GB':
            case 'en-US':
                return new Collection([
                    "1 EVEN William I, William the Conqueror\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 25 DEC 1066 TO @#DJULIAN@ 09 SEP 1087",
                    "1 EVEN William II\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 26 SEP 1087 TO @#DJULIAN@ 02 AUG 1100",
                    "1 EVEN Henry I\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 05 AUG 1100 TO @#DJULIAN@ 01 DEC 1135",
                    "1 EVEN Stephen\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 22 DEC 1135 TO @#DJULIAN@ 25 OCT 1154",
                    "1 EVEN Henry II\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 19 DEC 1154 TO @#DJULIAN@ 06 JUL 1189",
                    "1 EVEN Richard I, Richard the Lionheart\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 03 SEP 1189 TO @#DJULIAN@ 06 APR 1199",
                    "1 EVEN John\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 27 MAY 1199 TO @#DJULIAN@ 19 OCT 1216",
                    "1 EVEN Louis VIII\n2 TYPE Disputed English King\n2 DATE FROM @#DJULIAN@ NOV 1216 TO @#DJULIAN@ 22 SEP 1217",
                    "1 EVEN Henry III\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 28 OCT 1216 TO @#DJULIAN@ 16 NOV 1272",
                    "1 EVEN Edward I\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 20 NOV 1272 TO @#DJULIAN@ 07 JUL 1307",
                    "1 EVEN Edward II\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 08 JUL 1307 TO @#DJULIAN@ 20 JAN 1327",
                    "1 EVEN Edward III\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 25 JAN 1327 TO @#DJULIAN@ 21 JUN 1377",
                    "1 EVEN Richard II\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 22 JUN 1377 TO @#DJULIAN@ 29 SEP 1399",
                    "1 EVEN Henry IV\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 30 SEP 1399 TO @#DJULIAN@ 21 MAR 1413",
                    "1 EVEN Henry V\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 21 MAR 1413 TO @#DJULIAN@ 01 SEP 1422",
                    "1 EVEN Henry VI\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 01 SEP 1422 TO @#DJULIAN@ 04 MAR 1461",
                    "1 EVEN Edward IV\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 04 MAR 1461 TO @#DJULIAN@ 03 OCT 1470",
                    "1 EVEN Henry VI\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 03 OCT 1470 TO @#DJULIAN@ 11 APR 1471",
                    "1 EVEN Edward IV\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 11 APR 1471 TO @#DJULIAN@ 09 APR 1483",
                    "1 EVEN Richard III\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 09 APR 1483 TO @#DJULIAN@ 22 AUG 1485",
                    "1 EVEN Henry VI\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 22 AUG 1485 TO @#DJULIAN@ 22 APR 1509",
                    "1 EVEN Henry VII\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 22 APR 1509 TO @#DJULIAN@ 28 JAN 1547",
                    "1 EVEN Edward VI\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 28 JAN 1547 TO @#DJULIAN@ 10 JUL 1553",
                    "1 EVEN Lady Jane Grey\n2 TYPE Disputed English Queen\n2 DATE FROM @#DJULIAN@ 10 JUL 1553 TO @#DJULIAN@ 19 JUL 1553",
                    "1 EVEN Mary I\n2 TYPE English Queen\n2 DATE FROM @#DJULIAN@ 19 JUL 1553 TO @#DJULIAN@ 17 NOV 1558",
                    "1 EVEN Philip\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 25 JUL 1554 TO @#DJULIAN@ 17 NOV 1558",
                    "1 EVEN Elizabeth I\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 17 NOV 1558 TO @#DJULIAN@ 24 MAR 1603",
                    "1 EVEN James I\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 24 MAR 1603 TO @#DJULIAN@ 27 MAR 1625",
                    "1 EVEN Charles I\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 27 MAR 1625 TO @#DJULIAN@ 30 JAN 1649",
                    "1 EVEN Oliver Cromwell\n2 TYPE Lord Protector\n2 DATE FROM @#DJULIAN@ 16 DEC 1653 TO @#DJULIAN@ 03 SEP 1658",
                    "1 EVEN Richard Cromwell\n2 TYPE Lord Protector\n2 DATE FROM @#DJULIAN@ 03 SEP 1658 TO @#DJULIAN@ 07 MAY 1659",
                    "1 EVEN Charles II\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 29 MAY 1660 TO @#DJULIAN@ 06 FEB 1685",
                    "1 EVEN James II\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 06 FEB 1685 TO @#DJULIAN@ 13 FEB 1689",
                    "1 EVEN Mary II\n2 TYPE English Queen\n2 DATE FROM @#DJULIAN@ 13 FEB 1689 TO @#DJULIAN@ 13 FEB 1689",
                    "1 EVEN William II\n2 TYPE English King\n2 DATE FROM @#DJULIAN@ 13 FEB 1689 TO @#DJULIAN@ 08 MAR 1702",
                    "1 EVEN Anne\n2 TYPE English Queen\n2 DATE FROM @#DJULIAN@ 08 MAR 1702 TO @#DJULIAN@ 01 MAY 1707",
                    "1 EVEN Anne\n2 TYPE British Queen\n2 DATE FROM @#DJULIAN@ 01 MAY 1707 TO @#DJULIAN@ 01 AUG 1714",
                    "1 EVEN George I\n2 TYPE British King\n2 DATE FROM @#DJULIAN@ 01 AUG 1714 TO @#DJULIAN@ 11 JUN 1727",
                    "1 EVEN George II\n2 TYPE British King\n2 DATE FROM @#DJULIAN@ 11 JUN 1727 TO 25 OCT 1760",
                    "1 EVEN George III\n2 TYPE British King\n2 DATE FROM 25 OCT 1760 TO 29 JAN 1820",
                    "1 EVEN George IV\n2 TYPE British King\n2 DATE FROM 29 JAN 1820 TO 26 JUN 1830",
                    "1 EVEN William IV\n2 TYPE British King\n2 DATE FROM 26 JUN 1830 TO 20 JUN 1837",
                    "1 EVEN Victoria\n2 TYPE British Queen\n2 DATE FROM 20 JUN 1837 TO 22 JAN 1901",
                    "1 EVEN Edward VII\n2 TYPE British King\n2 DATE FROM 22 JAN 1901 TO 06 MAY 1910",
                    "1 EVEN George V\n2 TYPE British King\n2 DATE FROM 06 MAY 1910 TO 20 JAN 1936",
                    "1 EVEN Edward VII\n2 TYPE British King\n2 DATE FROM 20 JAN 1936 TO 11 DEC 1936",
                    "1 EVEN George VI\n2 TYPE British King\n2 DATE FROM 11 DEC 1936 TO 06 FEB 1952",
                    "1 EVEN Elizabeth II\n2 TYPE British Queen\n2 DATE FROM 06 FEB 1952",
                ]);

            default:
                return new Collection();
        }
    }
}
