<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryOm;

/**
 * Class LocaleArOm
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleArOm extends LocaleAr
{
    public function territory()
    {
        return new TerritoryOm();
    }
}
