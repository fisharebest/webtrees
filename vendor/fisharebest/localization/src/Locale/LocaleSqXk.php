<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryXk;

/**
 * Class LocaleSqXk
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleSqXk extends LocaleSq
{
    public function territory()
    {
        return new TerritoryXk();
    }
}
