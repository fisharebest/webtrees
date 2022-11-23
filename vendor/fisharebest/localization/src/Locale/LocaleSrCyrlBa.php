<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryBa;

/**
 * Class LocaleSrCyrlBa
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleSrCyrlBa extends LocaleSrCyrl
{
    public function territory()
    {
        return new TerritoryBa();
    }
}
