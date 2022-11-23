<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryEr;

/**
 * Class LocaleAaEr
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleAaEr extends LocaleAa
{
    public function territory()
    {
        return new TerritoryEr();
    }
}
