<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryUg;

/**
 * Class LocaleSwUg
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleSwUg extends LocaleSw
{
    public function territory()
    {
        return new TerritoryUg();
    }
}
