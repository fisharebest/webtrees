<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryAd;

/**
 * Class LocaleCaAd
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleCaAd extends LocaleCa
{
    public function territory()
    {
        return new TerritoryAd();
    }
}
