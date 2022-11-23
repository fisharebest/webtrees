<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryIq;

/**
 * Class LocaleArIq
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleArIq extends LocaleAr
{
    public function territory()
    {
        return new TerritoryIq();
    }
}
