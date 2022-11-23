<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryKm;

/**
 * Class LocaleArKm
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleArKm extends LocaleAr
{
    public function territory()
    {
        return new TerritoryKm();
    }
}
