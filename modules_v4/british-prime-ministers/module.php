<?php
declare(strict_types=1);

use Fisharebest\Webtrees\Module\AbstractModule;
use Fisharebest\Webtrees\Module\ModuleCustomInterface;
use Fisharebest\Webtrees\Module\ModuleCustomTrait;
use Fisharebest\Webtrees\Module\ModuleHistoricEventsInterface;
use Fisharebest\Webtrees\Module\ModuleHistoricEventsTrait;
use Illuminate\Support\Collection;

return new class extends AbstractModule implements ModuleCustomInterface, ModuleHistoricEventsInterface {
    use ModuleCustomTrait;
    use ModuleHistoricEventsTrait;

    /**
     * How should this module be labelled on tabs, menus, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        return 'British Prime Ministers';
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
     * @return Collection|string[]
     */
    public function historicEventsAll(): Collection
    {
        return new Collection([
            "1 EVEN Robert Walpole\n2 TYPE Prime Minister\n2 DATE 03 APR 1721",
            "1 EVEN Spencer Compton\n2 TYPE Prime Minister\n2 DATE 16 FEB 1742",
            "1 EVEN Henry Pelham\n2 TYPE Prime Minister\n2 DATE 27 AUG 1743",
            "1 EVEN Thomas Pelham-Holles\n2 TYPE Prime Minister\n2 DATE 16 MAR 1754",
            "1 EVEN William Cavendish\n2 TYPE Prime Minister\n2 DATE 16 NOV 1756",
            "1 EVEN Thomas Pelham-Holles\n2 TYPE Prime Minister\n2 DATE 29 JUN 1757",
            "1 EVEN John Stuart\n2 TYPE Prime Minister\n2 DATE 26 MAY 1762",
            "1 EVEN George Grenville\n2 TYPE Prime Minister\n2 DATE 16 APR 1763",
            "1 EVEN Charles Watson-Wentworth\n2 TYPE Prime Minister\n2 DATE 13 JUL 1765",
            "1 EVEN William Pitt\n2 TYPE Prime Minister\n2 DATE 30 JUL 1766",
            "1 EVEN Augustus FitzRoy\n2 TYPE Prime Minister\n2 DATE 14 OCT 1768",
            "1 EVEN Frederick North\n2 TYPE Prime Minister\n2 DATE 28 JAN 1770",
            "1 EVEN Charles Watson-Wentworth\n2 TYPE Prime Minister\n2 DATE 27 MAR 1782",
            "1 EVEN William Petty\n2 TYPE Prime Minister\n2 DATE 04 JUL 1782",
            "1 EVEN William Cavendish-Bentinck\n2 TYPE Prime Minister\n2 DATE 02 APR 1873",
            "1 EVEN William Pitt the Younger\n2 TYPE Prime Minister\n2 DATE 19 DEC 1783",
            "1 EVEN Henry Addington\n2 TYPE Prime Minister\n2 DATE 17 MAR 1801",
            "1 EVEN William Pitt the Younger\n2 TYPE Prime Minister\n2 DATE 10 MAY 1804",
            "1 EVEN William Grenville\n2 TYPE Prime Minister\n2 DATE 11 FEB 1806",
            "1 EVEN William Cavendish-Bentinck\n2 TYPE Prime Minister\n2 DATE 31 MAR 1807",
            "1 EVEN Spencer Perceval\n2 TYPE Prime Minister\n2 DATE 04 OCT 1809",
            "1 EVEN Robert Jenkinson\n2 TYPE Prime Minister\n2 DATE 08 JUN 1812",
            "1 EVEN George Canning\n2 TYPE Prime Minister\n2 DATE 12 APR 1827",
            "1 EVEN Frederick John Robinson\n2 TYPE Prime Minister\n2 DATE 31 AUG 1827",
            "1 EVEN Arthur Wellesley, Duke of Wellington\n2 TYPE Prime Minister\n2 DATE 22 JAN 1828",
            "1 EVEN Charles Grey\n2 TYPE Prime Minister\n2 DATE 22 NOV 1830",
            "1 EVEN William Lamb\n2 TYPE Prime Minister\n2 DATE 16 JUL 1834",
            "1 EVEN Arthur Wellesley, Duke of Wellington\n2 TYPE Prime Minister\n2 DATE 17 NOV 1834",
            "1 EVEN Robert Peel\n2 TYPE Prime Minister\n2 DATE 10 DEC 1834",
            "1 EVEN William Lamb\n2 TYPE Prime Minister\n2 DATE 18 APR 1835",
            "1 EVEN Robert Peel\n2 TYPE Prime Minister\n2 DATE 30 AUG 1841",
            "1 EVEN John Russell\n2 TYPE Prime Minister\n2 DATE 30 JUN 1846",
            "1 EVEN Edward Smith-Stanley\n2 TYPE Prime Minister\n2 DATE 20 FEB 1852",
            "1 EVEN George Hamilton-Gordon\n2 TYPE Prime Minister\n2 DATE 19 DEC 1852",
            "1 EVEN Henry John Temple\n2 TYPE Prime Minister\n2 DATE 06 FEB 1855",
            "1 EVEN Edward Smith-Stanley\n2 TYPE Prime Minister\n2 DATE 20 FEB 1858",
            "1 EVEN Henry John Temple\n2 TYPE Prime Minister\n2 DATE 12 JUN 1859",
            "1 EVEN John Russell\n2 TYPE Prime Minister\n2 DATE 29 OCT 1865",
            "1 EVEN Edward Smith-Stanley\n2 TYPE Prime Minister\n2 DATE 28 JUN 1866",
            "1 EVEN Benjamin Disraeli\n2 TYPE Prime Minister\n2 DATE 27 FEB 1868",
            "1 EVEN William Ewart Gladstone\n2 TYPE Prime Minister\n2 DATE 03 DEC 1868",
            "1 EVEN Benjamin Disraeli\n2 TYPE Prime Minister\n2 DATE 20 FEB 1874",
            "1 EVEN Robert Gascoyne-Cecil\n2 TYPE Prime Minister\n2 DATE 23 APR 1880",
            "1 EVEN William Ewart Gladstone\n2 TYPE Prime Minister\n2 DATE 01 FEB 1886",
            "1 EVEN Robert Gascoyne-Cecil\n2 TYPE Prime Minister\n2 DATE 25 JUL 1886",
            "1 EVEN William Ewart Gladstone\n2 TYPE Prime Minister\n2 DATE 15 AUG 1892",
            "1 EVEN Archibald Primrose\n2 TYPE Prime Minister\n2 DATE 05 MAR 1894",
            "1 EVEN Robert Gascoyne-Cecil\n2 TYPE Prime Minister\n2 DATE 25 JUN 1895",
            "1 EVEN Arthur Balfour\n2 TYPE Prime Minister\n2 DATE 12 JUL 1902",
            "1 EVEN Henry Campbell-Bannerman\n2 TYPE Prime Minister\n2 DATE 05 DEC 1905",
            "1 EVEN Herbert Henry Asquith\n2 TYPE Prime Minister\n2 DATE 05 APR 1908",
            "1 EVEN David Lloyd George\n2 TYPE Prime Minister\n2 DATE 06 DEC 1916",
            "1 EVEN Bonar Law\n2 TYPE Prime Minister\n2 DATE 23 OCT 1922",
            "1 EVEN Stanley Baldwin\n2 TYPE Prime Minister\n2 DATE 22 MAY 1923",
            "1 EVEN Ramsey MacDonald\n2 TYPE Prime Minister\n2 DATE 22 JAN 1924",
            "1 EVEN Stanley Baldwin\n2 TYPE Prime Minister\n2 DATE 07 JUN 1935",
            "1 EVEN Neville Chamberlain\n2 TYPE Prime Minister\n2 DATE 28 MAY 1937",
            "1 EVEN Winston Churchill\n2 TYPE Prime Minister\n2 DATE 10 MAY 1940",
            "1 EVEN Clement Atlee\n2 TYPE Prime Minister\n2 DATE 26 JUL 1945",
            "1 EVEN Winston Churchill\n2 TYPE Prime Minister\n2 DATE 26 OCT 1951",
            "1 EVEN Anthony Eden\n2 TYPE Prime Minister\n2 DATE 06 APR 1955",
            "1 EVEN Harold Macmillan\n2 TYPE Prime Minister\n2 DATE 10 JAN 1957",
            "1 EVEN Alex Douglas-Home\n2 TYPE Prime Minister\n2 DATE 19 OCT 1963",
            "1 EVEN Harold Wilson\n2 TYPE Prime Minister\n2 DATE 16 OCT 1964",
            "1 EVEN Edward Heath\n2 TYPE Prime Minister\n2 DATE 19 JUN 1970",
            "1 EVEN Harold Wilson\n2 TYPE Prime Minister\n2 DATE 04 MAR 1974",
            "1 EVEN James Callaghan\n2 TYPE Prime Minister\n2 DATE 05 PAR 1976",
            "1 EVEN Margaret Thatcher\n2 TYPE Prime Minister\n2 DATE 04 MAY 1979",
            "1 EVEN John Major\n2 TYPE Prime Minister\n2 DATE 28 NOV 1990",
            "1 EVEN Tony Blair\n2 TYPE Prime Minister\n2 DATE 02 MAY 1997",
            "1 EVEN Gordon Brown\n2 TYPE Prime Minister\n2 DATE 27 JUN 2007",
            "1 EVEN David Cameron\n2 TYPE Prime Minister\n2 DATE 11 MAY 2010",
            "1 EVEN Theresa May\n2 TYPE Prime Minister\n2 DATE 13 JUL 2016",
        ]);
    }
};
