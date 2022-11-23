<?php

namespace Fisharebest\Localization\PluralRule;

/**
 * Class PluralRule3 - Select a plural form for a specified number.
 * Families:
 * Baltic (Latvian)
 * nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n != 0 ? 1 : 2);
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class PluralRule3 implements PluralRuleInterface
{
    public function plurals()
    {
        return 3;
    }

    public function plural($number)
    {
        $number = abs($number);

        if ($number % 10 === 1 && $number % 100 !== 11) {
            return 0;
        }

        if ($number !== 0) {
            return 1;
        }

        return 2;
    }
}
