<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryMe;

/**
 * Class LocaleSrCyrlMe
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleSrCyrlMe extends LocaleSrCyrl
{
    public function territory()
    {
        return new TerritoryMe();
    }
}
