<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryIq;

/**
 * Class LocaleLrc - Luri
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleLrcIq extends LocaleLrc
{
    public function territory()
    {
        return new TerritoryIq();
    }
}
