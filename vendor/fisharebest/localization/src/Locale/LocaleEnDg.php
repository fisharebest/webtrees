<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryDg;

/**
 * Class LocaleEnDg
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleEnDg extends LocaleEn
{
    public function territory()
    {
        return new TerritoryDg();
    }
}
