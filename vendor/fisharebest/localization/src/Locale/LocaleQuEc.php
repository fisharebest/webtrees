<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryEc;

/**
 * Class LocaleQuEc
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleQuEc extends LocaleQu
{
    public function territory()
    {
        return new TerritoryEc();
    }
}
