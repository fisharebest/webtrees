<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryZa;

/**
 * Class LocaleSsZa
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleSsZa extends LocaleSs
{
    public function territory()
    {
        return new TerritoryZa();
    }
}
