<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryMv;

/**
 * Class LocaleEnMg
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleEnMv extends LocaleEn
{
    public function territory()
    {
        return new TerritoryMv();
    }
}
