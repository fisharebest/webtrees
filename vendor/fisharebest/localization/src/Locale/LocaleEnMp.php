<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryMp;

/**
 * Class LocaleEnMp
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleEnMp extends LocaleEn
{
    public function territory()
    {
        return new TerritoryMp();
    }
}
