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
 * Class HistoryPrimeMinistersBritish
 */
class HistoryPrimeMinistersBritish extends AbstractModule implements ModuleHistoricEventsInterface
{
    use ModuleHistoricEventsTrait;

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        return 'British prime ministers 🇬🇧';
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
            "1 EVEN Sir Robert Walpole, 1st Earl of Orford (1676 — 1745), Whig\n2 TYPE Prime Minister of Great Britain\n2 DATE FROM 03 APR 1721 TO 15 MAY 1730\n2 NOTE Government: Walpole–Townshend\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Robert_Walpole)",
            "1 EVEN Sir Robert Walpole, 1st Earl of Orford (1676 — 1745), Whig\n2 TYPE Prime Minister of Great Britain\n2 DATE FROM 15 MAY 1730 TO 16 FEB 1743\n2 NOTE Government: Walpole\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Robert_Walpole)",
            "1 EVEN Spencer Compton, 1st Earl of Wilmington (1673 — 1743), Whig\n2 TYPE Prime Minister of Great Britain\n2 DATE FROM 16 FEB 1742 TO 27 AUG 1743\n2 NOTE Government: Carteret\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Spencer_Compton,_1st_Earl_of_Wilmington)",
            "1 EVEN Henry Pelham (1694 — 1754), Whig\n2 TYPE Prime Minister of Great Britain\n2 DATE FROM 27 AUG 1743 TO 24 NOV 1744\n2 NOTE Government: Carteret\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Henry_Pelham)",
            "1 EVEN Henry Pelham (1694 — 1754), Whig\n2 TYPE Prime Minister of Great Britain\n2 DATE FROM 24 NOV 1744 TO 10 FEB 1746\n2 NOTE Government: Broad Bottom I\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Henry_Pelham)",
            "1 EVEN Henry Pelham (1694 — 1754), Whig\n2 TYPE Prime Minister of Great Britain\n2 DATE FROM 12 FEB 1746 TO 16 MAR 1754\n2 NOTE Government: Broad Bottom II\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Henry_Pelham)",
            "1 EVEN Thomas Pelham-Holles, 1st Duke of Newcastle (1693 — 1768), Whig\n2 TYPE Prime Minister of Great Britain\n2 DATE FROM 16 MAR 1754 TO 16 NOV 1756\n2 NOTE Government: Newcastle I\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Thomas_Pelham-Holles,_1st_Duke_of_Newcastle)",
            "1 EVEN William Cavendish, 4th Duke of Devonshire (1720 — 1764), Whig\n2 TYPE Prime Minister of Great Britain\n2 DATE FROM 16 NOV 1756 TO APR 1757\n2 NOTE Government: Pitt–Devonshire\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/William_Cavendish,_4th_Duke_of_Devonshire)",
            "1 EVEN William Cavendish, 4th Duke of Devonshire (1720 — 1764), Whig\n2 TYPE Prime Minister of Great Britain\n2 DATE FROM APR 1757 TO 29 JUN 1757\n2 NOTE Government: 1757 Caretaker\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/William_Cavendish,_4th_Duke_of_Devonshire)",
            "1 EVEN Thomas Pelham-Holles, 1st Duke of Newcastle (1693 — 1768), Whig\n2 TYPE Prime Minister of Great Britain\n2 DATE FROM 29 JUN 1757 TO MAR 1761\n2 NOTE Government: Pitt–Newcastle\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Thomas_Pelham-Holles,_1st_Duke_of_Newcastle)",
            "1 EVEN Thomas Pelham-Holles, 1st Duke of Newcastle (1693 — 1768), Whig\n2 TYPE Prime Minister of Great Britain\n2 DATE FROM MAR 1761 TO 26 MAY 1762\n2 NOTE Government: Bute–Newcastle\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Thomas_Pelham-Holles,_1st_Duke_of_Newcastle)",
            "1 EVEN John Stuart, 3rd Earl of Bute (1713 — 1792), Tory\n2 TYPE Prime Minister of Great Britain\n2 DATE FROM 26 MAY 1762 TO 26 MAY 1762\n2 NOTE Government: Bute\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/John_Stuart,_3rd_Earl_of_Bute)",
            "1 EVEN George Grenville (1712 — 1770), Whig\n2 TYPE Prime Minister of Great Britain\n2 DATE FROM 26 MAY 1762 TO 13 JUL 1765\n2 NOTE Government:  Grenville\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/George_Grenville)",
            "1 EVEN Charles Watson-Wentworth, 2nd Marquess of Rockingham (1730 — 1782), Whig\n2 TYPE Prime Minister of Great Britain\n2 DATE FROM 13 JUL 1765 TO 30 JUL 1766\n2 NOTE Government:  Rockingham I\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Charles_Watson-Wentworth,_2nd_Marquess_of_Rockingham)",
            "1 EVEN William Pitt the Elder, 1st Earl of Chatham (1708 — 1778), Whig\n2 TYPE Prime Minister of Great Britain\n2 DATE FROM 30 JUL 1766 TO 14 OCT 1768\n2 NOTE Government: Chatham\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/William_Pitt,_1st_Earl_of_Chatham)",
            "1 EVEN Augustus FitzRoy, 3rd Duke of Grafton (1735 — 1811), Whig \n2 TYPE Prime Minister of Great Britain\n2 DATE FROM 14 OCT 1768 TO 28 JAN 1770\n2 NOTE Government: Grafton\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Augustus_FitzRoy,_3rd_Duke_of_Grafton)",
            "1 EVEN Lord Frederick North, 2nd Earl of Guilford (1732 — 1792), Tory\n2 TYPE Prime Minister of Great Britain\n2 DATE FROM 28 JAN 1770 TO 27 MAR 1782\n2 NOTE Government: North\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Frederick_North,_Lord_North)",
            "1 EVEN Charles Watson-Wentworth, 2nd Marquess of Rockingham (1730 — 1782), Whig\n2 TYPE Prime Minister of Great Britain\n2 DATE FROM 27 MAR 1782 TO 04 JUL 1782\n2 NOTE Government: Rockingham II\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Charles_Watson-Wentworth,_2nd_Marquess_of_Rockingham)",
            "1 EVEN William Petty, 2nd Earl of Shelburne (1737 — 1805), Whig\n2 TYPE Prime Minister of Great Britain\n2 DATE FROM 04 JUL 1782 TO 02 APR 1783\n2 NOTE Government: Shelburne\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/William_Petty,_2nd_Earl_of_Shelburne)",
            "1 EVEN William Cavendish-Bentinck, 3rd Duke of Portland (1738 — 1809), Whig\n2 TYPE Prime Minister of Great Britain\n2 DATE FROM 02 APR 1783 TO 19 DEC 1783\n2 NOTE Government: Fox–North\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/William_Cavendish-Bentinck,_3rd_Duke_of_Portland)",
            "1 EVEN William Pitt the Younger (1759 — 1806), Tory\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 19 DEC 1783 TO 17 MAR 1801\n2 NOTE Government: Pitt I\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/William_Pitt_the_Younger)",
            "1 EVEN Henry Addington, 1st Viscount Sidmouth (1757 — 1844), Tory\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 17 MAR 1801 TO 10 MAY 1804\n2 NOTE Government: Addington\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Henry_Addington)",
            "1 EVEN William Pitt the Younger (1759 — 1806), Tory\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 10 MAY 1804 TO 11 FEB 1806\n2 NOTE Government: Pitt II\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/William_Pitt_the_Younger)",
            "1 EVEN William Grenville, 1st Baron Grenville (1759 — 1834), Whig\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 11 FEB 1806 TO 31 MAR 1807\n2 NOTE Government: All the Talents\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/William_Grenville,_1st_Baron_Grenville)",
            "1 EVEN William Cavendish-Bentinck, 3rd Duke of Portland (1738 — 1809), Whig\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 31 MAR 1807 TO 04 OCT 1809\n2 NOTE Government: Portland II\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/William_Cavendish-Bentinck,_3rd_Duke_of_Portland)",
            "1 EVEN Spencer Perceval (1762 — 1812), Tory\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 04 OCT 1809 TO 08 JUN 1812\n2 NOTE Government: Perceval\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Spencer_Perceval)",
            "1 EVEN Robert Jenkinson, 2nd Earl of Liverpool (1770 — 1828), Tory\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 08 JUN 1812 TO 12 APR 1827\n2 NOTE Government: Liverpool\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Robert_Jenkinson,_2nd_Earl_of_Liverpool)",
            "1 EVEN George Canning (1770 — 1827), Tory\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 12 APR 1827 TO 31 AUG 1827\n2 NOTE Government: Canning\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/George_Canning)",
            "1 EVEN Frederick John Robinson, 1st Viscount Goderich (1782 — 1859), Tory\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 31 AUG 1827 TO 22 JAN 1828\n2 NOTE Government: Goderich\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/F._J._Robinson,_1st_Viscount_Goderich)",
            "1 EVEN Arthur Wellesley, 1st Duke of Wellington (1769 — 1852), Tory\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 22 JAN 1828 TO 22 NOV 1830\n2 NOTE Government: Wellington–Peel\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Arthur_Wellesley,_1st_Duke_of_Wellington)",
            "1 EVEN Charles Grey, 2nd Earl Grey (1764 — 1845), Whig\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 22 NOV 1830 TO 16 JUL 1834\n2 NOTE Government: Grey\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Charles_Grey,_2nd_Earl_Grey)",
            "1 EVEN William Lamb, 2nd Viscount Melbourne (1779 — 1848), Whig\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 16 JUL 1834 TO 17 NOV 1834\n2 NOTE Government: Melbourne I\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/William_Lamb,_2nd_Viscount_Melbourne)",
            "1 EVEN Arthur Wellesley, 1st Duke of Wellington (1769 — 1852), Tory\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 17 NOV 1834 TO 10 DEC 1834\n2 NOTE Government: Wellington Caretake\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Arthur_Wellesley,_1st_Duke_of_Wellington)",
            "1 EVEN Sir Robert Peel, 2nd Baronet (1788 — 1850), Conservative\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 10 DEC 1834 TO 18 APR 1835\n2 NOTE Government: Peel I\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Robert_Peel)",
            "1 EVEN William Lamb, 2nd Viscount Melbourne (1779 — 1848), Whig\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 18 APR 1835 TO 30 AUG 1841\n2 NOTE Government: Melbourne II\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/William_Lamb,_2nd_Viscount_Melbourne)",
            "1 EVEN Sir Robert Peel, 2nd Baronet (1788 — 1850), Conservative\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 30 AUG 1841 TO 30 JUN 1846\n2 NOTE Government: Peel II\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Robert_Peel)",
            "1 EVEN Lord John Russell, 1st Earl Russell (1792 — 1878), Whig\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 30 JUN 1846 TO 20 FEB 1852\n2 NOTE Government:  Russell I\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/John_Russell,_1st_Earl_Russell)",
            "1 EVEN Edward George Geoffrey Smith-Stanley, 14th Earl of Derby (1799 — 1869), Conservative\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 20 FEB 1852 TO 19 DEC 1852\n2 NOTE Government: Who? Who?\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Edward_Smith-Stanley,_14th_Earl_of_Derby)",
            "1 EVEN George Hamilton-Gordon, 4th Earl of Aberdeen, (1784 — 1860), Peelite\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 19 DEC 1852 TO 06 FEB 1855\n2 NOTE Government: Aberdeen\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/George_Hamilton-Gordon,_4th_Earl_of_Aberdeen)",
            "1 EVEN Henry John Temple, 3rd Viscount Palmerston (1784 — 1865), Liberal\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 06 FEB 1855 TO 20 FEB 1858\n2 NOTE Government: Palmerston I\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Henry_John_Temple,_3rd_Viscount_Palmerston)",
            "1 EVEN Edward George Geoffrey Smith-Stanley, 14th Earl of Derby (1799 — 1869), Conservative\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 20 FEB 1858 TO 12 JUN 1859\n2 NOTE Government: Derby–Disraeli II\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Edward_Smith-Stanley,_14th_Earl_of_Derby)",
            "1 EVEN Henry John Temple, 3rd Viscount Palmerston (1784 — 1865), Liberal\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 12 JUN 1859 TO 29 OCT 1865\n2 NOTE Government: Palmerston II\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Henry_John_Temple,_3rd_Viscount_Palmerston)",
            "1 EVEN Lord John Russell, 1st Earl Russell (1792 — 1878), Whig\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 29 OCT 1865 TO 28 JUN 1866\n2 NOTE Government: Russell II\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/John_Russell,_1st_Earl_Russell)",
            "1 EVEN Edward George Geoffrey Smith-Stanley, 14th Earl of Derby (1799 — 1869), Conservative\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 28 JUN 1866 TO 27 FEB 1868\n2 NOTE Government: Derby–Disraeli III\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Edward_Smith-Stanley,_14th_Earl_of_Derby)",
            "1 EVEN Benjamin Disraeli, 1st Earl of Beaconsfield (1804 — 1881), Conservative\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 27 FEB 1868 TO 03 DEC 1868\n2 NOTE Government: Derby–Disraeli III\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Benjamin_Disraeli)",
            "1 EVEN William Ewart Gladstone (1809 — 1898), Liberal\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 03 DEC 1868 TO 20 FEB 1874\n2 NOTE Government: Gladstone I\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/William_Ewart_Gladstone)",
            "1 EVEN Benjamin Disraeli, 1st Earl of Beaconsfield (1804 — 1881), Conservative\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 20 FEB 1874 TO 23 APR 1880\n2 NOTE Government: Disraeli II\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Benjamin_Disraeli)",
            "1 EVEN Robert Arthur Talbot Gascoyne-Cecil, 3rd Marquess of Salisbury (1830 — 1903), Conservative\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 23 APR 1880 TO 01 FEB 1886\n2 NOTE Government: Salisbury I\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Robert_Gascoyne-Cecil,_3rd_Marquess_of_Salisbury)",
            "1 EVEN William Ewart Gladstone (1809 — 1898), Liberal\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 01 FEB 1886 TO 25 JUL 1886\n2 NOTE Government: Gladstone II\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/William_Ewart_Gladstone)",
            "1 EVEN Robert Arthur Talbot Gascoyne-Cecil, 3rd Marquess of Salisbury (1830 — 1903), Conservative\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 25 JUL 1886 TO 15 AUG 1892\n2 NOTE Government: Salisbury II\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Robert_Gascoyne-Cecil,_3rd_Marquess_of_Salisbury)",
            "1 EVEN William Ewart Gladstone (1809 — 1898), Liberal\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 15 AUG 1892 TO 05 MAR 1894\n2 NOTE Government: Gladstone III\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/William_Ewart_Gladstone)",
            "1 EVEN Archibald Philip Primrose, 5th Earl of Rosebery, 1st Earl of Midlothian (1847 — 1929), Liberal\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 05 MAR 1894 TO 25 JUN 1895\n2 NOTE Government: Rosebery\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Archibald_Primrose,_5th_Earl_of_Rosebery)",
            "1 EVEN Robert Arthur Talbot Gascoyne-Cecil, 3rd Marquess of Salisbury (1830 — 1903), Conservative\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 25 JUN 1895 TO 12 JUL 1902\n2 NOTE Government: Salisbury III and IV\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Robert_Gascoyne-Cecil,_3rd_Marquess_of_Salisbury)",
            "1 EVEN Arthur James Balfour, 1st Earl of Balfour (1848 — 1930), Conservative\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 12 JUL 1902 TO 05 DEC 1905\n2 NOTE Government: Balfour\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Arthur_Balfour)",
            "1 EVEN Sir Henry Campbell-Bannerman (1836 — 1908), Liberal\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 05 DEC 1905 TO 05 APR 1908\n2 NOTE Government: Campbell-Bannerman\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Henry_Campbell-Bannerman)",
            "1 EVEN Herbert Henry Asquith, 1st Earl of Oxford and Asquith (1852 — 1928), Liberal\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 05 APR 1908 TO 06 DEC 1916\n2 NOTE Government: Asquith I, II, III and Asquith Coalition\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/H._H._Asquith)",
            "1 EVEN David Lloyd George, 1st Earl Lloyd-George of Dwyfor (1863 — 1945), Liberal\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 06 DEC 1916 TO 23 OCT 1922\n2 NOTE Government: Lloyd George War and Lloyd George II\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/David_Lloyd_George)",
            "1 EVEN Andrew Bonar Law (1858 — 1923), Conservative\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 23 OCT 1922 TO 22 MAY 1923\n2 NOTE Government: Law\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Bonar_Law)",
            "1 EVEN Stanley Baldwin, 1st Earl Baldwin of Bewdley (1867 — 1947), Conservative\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 22 MAY 1923 TO 22 JAN 1924\n2 NOTE Government: Baldwin I\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Stanley_Baldwin)",
            "1 EVEN James Ramsay MacDonald (1866 — 1937), Labour\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 22 JAN 1924 TO 04 NOV 1924\n2 NOTE Government: MacDonald I\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Ramsay_MacDonald)",
            "1 EVEN Stanley Baldwin, 1st Earl Baldwin of Bewdley (1867 — 1947), Conservative\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 04 NOV 1924 TO 04 JUN 1929\n2 NOTE Government: Baldwin II\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Stanley_Baldwin)",
            "1 EVEN James Ramsay MacDonald (1866 — 1937), Labour\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 05 JUN 1929 TO 07 JUN 1935\n2 NOTE Government: MacDonald II, National I and II\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Ramsay_MacDonald)",
            "1 EVEN Stanley Baldwin, 1st Earl Baldwin of Bewdley (1867 — 1947), Conservative\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 07 JUN 1935 TO 28 MAY 1937\n2 NOTE Government: National III\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Stanley_Baldwin)",
            "1 EVEN Arthur Neville Chamberlain (1869 — 1940), Conservative\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 28 MAY 1937 TO 10 MAY 1940\n2 NOTE Government: National IV and Chamberlain War\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Neville_Chamberlain)",
            "1 EVEN Sir Winston Leonard Spencer Churchill (1874 — 1965), Conservative\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 10 MAY 1940 TO 26 JUL 1945\n2 NOTE Government: Churchill War and Churchill Caretaker\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Winston_Churchill)",
            "1 EVEN Clement Richard Attlee, 1st Earl Attlee (1883 — 1967), Labour\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 26 JUL 1945 TO 26 OCT 1951\n2 NOTE Government: Attlee I and II\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Clement_Attlee)",
            "1 EVEN Sir Winston Leonard Spencer Churchill (1874 — 1965), Conservative\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 26 OCT 1951 TO 06 APR 1955\n2 NOTE Government: Churchill III\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Winston_Churchill)",
            "1 EVEN Sir Robert Anthony Eden, 1st Earl of Avon (1897 — 1977), Conservative\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 06 APR 1955 TO 10 JAN 1957\n2 NOTE Government: Eden\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Anthony_Eden)",
            "1 EVEN Maurice Harold Macmillan, 1st Earl of Stockton (1894 — 1986), Conservative\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 10 JAN 1957 TO 19 OCT 1963\n2 NOTE Government: Macmillan I and II\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Harold_Macmillan)",
            "1 EVEN Sir Alexander Frederick Douglas-Home, Baron Home of the Hirsel, (1903 — 1995), Conservative\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 19 OCT 1963 TO 16 OCT 1964\n2 NOTE Government: Douglas-Home\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Alec_Douglas-Home)",
            "1 EVEN James Harold Wilson, Baron Wilson of Rievaulx (1916 — 1995), Labour\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 16 OCT 1964 TO 19 JUN 1970\n2 NOTE Government: Wilson I and II\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Harold_Wilson)",
            "1 EVEN Sir Edward Richard George Heath (1916 — 2005), Conservative\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 19 JUN 1970 TO 04 MAR 1974\n2 NOTE Government: Heath\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Edward_Heath)",
            "1 EVEN James Harold Wilson, Baron Wilson of Rievaulx (1916 — 1995), Labour\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 04 MAR 1974 TO 05 APR 1976\n2 NOTE Government: Wilson III and IV\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Harold_Wilson)",
            "1 EVEN Leonard James Callaghan, Baron Callaghan of Cardiff (1912 — 2005), Labour\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 05 APR 1976 TO 04 MAY 1979\n2 NOTE Government: Callaghan\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/James_Callaghan)",
            "1 EVEN Lady Margaret Hilda Thatcher née Roberts, Baroness Thatcher (1925 — 2013), Conservative\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 04 MAY 1979 TO 28 NOV 1990\n2 NOTE Government: Thatcher I, II and III\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Margaret_Thatcher)",
            "1 EVEN Sir John Major (* 1943), Conservative\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 28 NOV 1990 TO 02 MAY 1997\n2 NOTE Government: Major I and II\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/John_Major)",
            "1 EVEN Anthony Charles Lynton Blair (* 1953), Labour\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 02 MAY 1997 TO 27 JUN 2007\n2 NOTE Government: Blair I, II and III\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Tony_Blair)",
            "1 EVEN James Gordon Brown (* 1951), Labour\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 27 JUN 2007 TO 11 MAY 2010\n2 NOTE Government: Brown\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Gordon_Brown)",
            "1 EVEN David William Donald Cameron (* 1966), Conservative\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 11 MAY 2010 TO 13 JUL 2016\n2 NOTE Government: Cameron–Clegg and Cameron II\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/David_Cameron)",
            "1 EVEN Lady Theresa Mary May née Brasier (* 1956), Conservative\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 13 JUL 2016 TO 24 JUL 2019\n2 NOTE Government: May I and II\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Theresa_May)",
            "1 EVEN Alexander Boris de Pfeffel Johnson (* 1964), Conservative\n2 TYPE Prime Minister of the United Kingdom\n2 DATE FROM 24 JUL 2019\n2 NOTE Government: Johnson I and II\n2 SOUR [Wikipedia: List of prime ministers of the United Kingdom](https://en.wikipedia.org/wiki/Boris_Johnson)",
        ]);
    }
}
