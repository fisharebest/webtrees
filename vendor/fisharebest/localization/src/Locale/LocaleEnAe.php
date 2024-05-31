<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryAe;

/**
 * Class LocaleEnAi
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleEnAe extends LocaleEn
{
    public function territory()
    {
        return new TerritoryAe();
    }
}
