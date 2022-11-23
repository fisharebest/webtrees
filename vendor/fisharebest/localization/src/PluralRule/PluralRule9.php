<?php

namespace Fisharebest\Localization\PluralRule;

/**
 * Class PluralRule9 - Select a plural form for a specified number.
 * Families:
 * Slavic (Polish)
 * nplurals=3; plural=(n==1 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class PluralRule9 implements PluralRuleInterface
{
    public function plurals()
    {
        return 3;
    }

    public function plural($number)
    {
        $number = abs($number);

        if ($number === 1) {
            return 0;
        }

        if ($number % 10 >= 2 && $number % 10 <= 4 && ($number % 100 < 10 || $number % 100 >= 20)) {
            return 1;
        }

        return 2;
    }
}
