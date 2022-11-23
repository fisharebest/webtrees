<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryPa;

/**
 * Class LocaleEsPa
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleEsPa extends LocaleEs
{
    public function territory()
    {
        return new TerritoryPa();
    }
}
