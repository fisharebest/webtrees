<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryDj;

/**
 * Class LocaleSoDj
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleSoDj extends LocaleSo
{
    public function territory()
    {
        return new TerritoryDj();
    }
}
