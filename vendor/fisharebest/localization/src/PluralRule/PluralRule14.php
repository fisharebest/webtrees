<?php

namespace Fisharebest\Localization\PluralRule;

/**
 * Class PluralRule14 - Select a plural form for a specified number.
 * Families:
 * Slavic (Macedonian)
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class PluralRule14 implements PluralRuleInterface
{
    public function plurals()
    {
        return 2;
    }

    public function plural($number)
    {
        $number = abs($number);

        if ($number % 10 === 1 && $number % 100 !== 11) {
            return 0;
        }

        return 1;
    }
}
