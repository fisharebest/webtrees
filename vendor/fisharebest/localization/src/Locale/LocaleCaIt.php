<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryIt;

/**
 * Class LocaleCaIt
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleCaIt extends LocaleCa
{
    public function territory()
    {
        return new TerritoryIt();
    }
}
