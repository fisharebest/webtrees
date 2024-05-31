<?php

namespace Fisharebest\Localization\PluralRule;

/**
 * Class PluralRule12 - Select a plural form for a specified number.
 * Families:
 * Semitic (Arabic)
 * nplurals=6; plural=(n==0 ? 0 : n==1 ? 1 : n==2 ? 2 : n%100>=3 && n%100<=10 ? 3 : n%100>=11 ? 4 : 5);
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class PluralRule12 implements PluralRuleInterface
{
    public function plurals()
    {
        return 6;
    }

    public function plural($number)
    {
        $number = abs($number);

        if ($number === 0) {
            return 0;
        }

        if ($number === 1) {
            return 1;
        }

        if ($number === 2) {
            return 2;
        }

        if ($number % 100 >= 3 && $number % 100 <= 10) {
            return 3;
        }

        if ($number % 100 >= 11) {
            return 4;
        }

        return 5;
    }
}
