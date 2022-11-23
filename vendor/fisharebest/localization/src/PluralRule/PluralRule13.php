<?php

namespace Fisharebest\Localization\PluralRule;

/**
 * Class PluralRule13 - Select a plural form for a specified number.
 * Families:
 * Semitic (Maltese)
 * nplurals=4; plural=(n==1 ? 0 : n==0 || ( n%100>1 && n%100<11) ? 1 : (n%100>10 && n%100<20 ) ? 2 : 3);
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class PluralRule13 implements PluralRuleInterface
{
    public function plurals()
    {
        return 5;
    }

    public function plural($number)
    {
        $number = abs($number);

        if ($number === 1) {
            return 0;
        }

        if ($number === 2) {
            return 1;
        }

        if ($number === 0 || ($number % 100 > 2 && $number % 100 < 11)) {
            return 2;
        }

        if ($number % 100 > 10 && $number % 100 < 20) {
            return 3;
        }

        return 4;
    }
}
