<?php

namespace Fisharebest\Localization\PluralRule;

/**
 * Class PluralRule4 - Select a plural form for a specified number.
 * Families:
 * Celtic (Scottish Gaelic)
 * nplurals=4; plural=(n==1 || n==11) ? 0 : (n==2 || n==12) ? 1 : (n > 2 && n < 20) ? 2 : 3;
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class PluralRule4 implements PluralRuleInterface
{
    public function plurals()
    {
        return 4;
    }

    public function plural($number)
    {
        $number = abs($number);

        if ($number === 1 || $number === 11) {
            return 0;
        }

        if ($number === 2 || $number === 12) {
            return 1;
        }

        if ($number > 2 && $number < 20) {
            return 2;
        }

        return 3;
    }
}
