<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryBa;

/**
 * Class LocaleSrLatnBa
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleSrLatnBa extends LocaleSrLatn
{
    public function territory()
    {
        return new TerritoryBa();
    }
}
