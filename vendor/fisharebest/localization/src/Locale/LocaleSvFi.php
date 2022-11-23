<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryFi;

/**
 * Class LocaleSvFi
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleSvFi extends LocaleSv
{
    public function territory()
    {
        return new TerritoryFi();
    }
}
