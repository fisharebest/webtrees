<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryMs;

/**
 * Class LocaleEnMs
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleEnMs extends LocaleEn
{
    public function territory()
    {
        return new TerritoryMs();
    }
}
