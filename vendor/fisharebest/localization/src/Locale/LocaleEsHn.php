<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryHn;

/**
 * Class LocaleEsHn
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleEsHn extends LocaleEs
{
    public function territory()
    {
        return new TerritoryHn();
    }
}
