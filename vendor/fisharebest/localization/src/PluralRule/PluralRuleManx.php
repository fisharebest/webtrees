<?php

namespace Fisharebest\Localization\PluralRule;

/**
 * Class PluralRuleManx - Select a plural form for a specified number.
 * nplurals=4; plural=(n % 10 == 1) ? 0 : ((n % 10 == 2) ? 1 : ((n % 20 == 0) ? 2 : 3));
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class PluralRuleManx implements PluralRuleInterface
{
    public function plurals()
    {
        return 4;
    }

    public function plural($number)
    {
        $number = abs($number);

        if ($number % 10 === 1) {
            return 0;
        }

        if ($number % 10 === 2) {
            return 1;
        }

        if ($number % 20 === 0) {
            return 2;
        }

        return 3;
    }
}
