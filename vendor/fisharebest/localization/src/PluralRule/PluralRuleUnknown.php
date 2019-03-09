<?php

namespace Fisharebest\Localization\PluralRule;

use DomainException;

/**
 * Class PluralRuleUnknown - used by languages for which the plural rules are not known.
 *
 * We cannot use this language for translation, but we can use its other attributes.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
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
