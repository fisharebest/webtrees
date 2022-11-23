<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryUg;

/**
 * Class LocaleEnUg
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleEnUg extends LocaleEn
{
    public function territory()
    {
        return new TerritoryUg();
    }
}
