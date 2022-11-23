<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryMo;

/**
 * Class LocaleZhHantMo
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleZhHantMo extends LocaleZhHant
{
    public function territory()
    {
        return new TerritoryMo();
    }
}
