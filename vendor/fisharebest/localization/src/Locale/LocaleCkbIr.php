<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryIr;

/**
 * Class LocaleCkbIr - Sorani
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleCkbIr extends LocaleCkb
{
    public function territory()
    {
        return new TerritoryIr();
    }
}
