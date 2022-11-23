<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryMk;

/**
 * Class LocaleSqMk
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleSqMk extends LocaleSq
{
    public function territory()
    {
        return new TerritoryMk();
    }
}
