<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryGl;

/**
 * Class LocaleDaGl
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleDaGl extends LocaleDa
{
    public function territory()
    {
        return new TerritoryGl();
    }
}
