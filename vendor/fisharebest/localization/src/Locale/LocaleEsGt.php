<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryGt;

/**
 * Class LocaleEsGt
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleEsGt extends LocaleEs
{
    public function territory()
    {
        return new TerritoryGt();
    }
}
