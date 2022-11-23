<?php

namespace Fisharebest\Localization\PluralRule;

use DomainException;

/**
 * Class PluralRuleUnknown - used by languages for which the plural rules are not known.
 *
 * We cannot use this language for translation, but we can use its other attributes.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class PluralRuleUnknown implements PluralRuleInterface
{
    public function plurals()
    {
        throw new DomainException('No plural rule defined for this language');
    }

    public function plural($number)
    {
        throw new DomainException('No plural rule defined for this language');
    }
}
