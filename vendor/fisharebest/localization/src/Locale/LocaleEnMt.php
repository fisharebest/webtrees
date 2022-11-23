<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryMt;

/**
 * Class LocaleEnMt
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleEnMt extends LocaleEn
{
    public function territory()
    {
        return new TerritoryMt();
    }
}
