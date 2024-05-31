<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryBb;

/**
 * Class LocaleEnBb
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleEnBb extends LocaleEn
{
    public function territory()
    {
        return new TerritoryBb();
    }
}
