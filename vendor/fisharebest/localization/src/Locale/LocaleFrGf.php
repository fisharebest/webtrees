<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryGf;

/**
 * Class LocaleFrGf
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleFrGf extends LocaleFr
{
    public function territory()
    {
        return new TerritoryGf();
    }
}
