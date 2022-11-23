<?php

namespace Fisharebest\Localization\PluralRule;

/**
 * Interface PluralRuleInterface - Select a plural form for a specified number.
 *
 * @link          https://developer.mozilla.org/en-US/docs/Mozilla/Localization/Localization_and_Plurals
 * @link          http://docs.translatehouse.org/projects/localization-guide/en/latest/l10n/pluralforms.html
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
interface PluralRuleInterface
{
    /**
     * How many plural forms exist.
     *
     * @return int
     */
    public function plurals();

    /**
     * Which plural form to use for a specified number.
     *
     * @param int $number
     *
     * @return int
     */
    public function plural($number);
}
