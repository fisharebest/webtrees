<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryHk;

/**
 * Class LocaleZhHantHk
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleZhHantHk extends LocaleZhHant
{
    public function territory()
    {
        return new TerritoryHk();
    }
}
