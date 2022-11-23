<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryWs;

/**
 * Class LocaleEnWs
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleEnWs extends LocaleEn
{
    public function territory()
    {
        return new TerritoryWs();
    }
}
