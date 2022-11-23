<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryAi;

/**
 * Class LocaleEnAi
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleEnAi extends LocaleEn
{
    public function territory()
    {
        return new TerritoryAi();
    }
}
