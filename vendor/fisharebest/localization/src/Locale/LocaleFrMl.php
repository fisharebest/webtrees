<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryMl;

/**
 * Class LocaleFrMl
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleFrMl extends LocaleFr
{
    public function territory()
    {
        return new TerritoryMl();
    }
}
