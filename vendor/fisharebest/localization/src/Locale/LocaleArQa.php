<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryQa;

/**
 * Class LocaleArQa
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleArQa extends LocaleAr
{
    public function territory()
    {
        return new TerritoryQa();
    }
}
