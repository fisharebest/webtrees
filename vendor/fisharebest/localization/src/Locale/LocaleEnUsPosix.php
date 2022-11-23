<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Variant\VariantPosix;

/**
 * Class LocaleEnUsPosix
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleEnUsPosix extends LocaleEnUs
{
    public function numberSymbols()
    {
        return array(
            self::GROUP => '',
        );
    }

    public function variant()
    {
        return new VariantPosix();
    }
}
