<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryGq;

/**
 * Class LocalePtGq
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocalePtGq extends LocalePt
{
    public function territory()
    {
        return new TerritoryGq();
    }
}
