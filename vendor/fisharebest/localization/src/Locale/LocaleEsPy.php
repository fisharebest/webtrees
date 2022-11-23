<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryPy;

/**
 * Class LocaleEsPy
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleEsPy extends LocaleEs
{
    public function territory()
    {
        return new TerritoryPy();
    }
}
