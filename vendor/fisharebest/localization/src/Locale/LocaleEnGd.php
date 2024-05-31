<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryGd;

/**
 * Class LocaleEnGd
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleEnGd extends LocaleEn
{
    public function territory()
    {
        return new TerritoryGd();
    }
}
