<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryUy;

/**
 * Class LocaleEsUy
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleEsUy extends LocaleEs
{
    public function territory()
    {
        return new TerritoryUy();
    }
}
