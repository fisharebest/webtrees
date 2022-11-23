<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryAx;

/**
 * Class LocaleSvAx
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleSvAx extends LocaleSv
{
    public function territory()
    {
        return new TerritoryAx();
    }
}
