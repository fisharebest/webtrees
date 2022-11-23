<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryIn;

/**
 * Class LocaleSdDevaIn - Sindhi
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleSdDevaIn extends LocaleSdDeva
{
    public function territory()
    {
        return new TerritoryIn();
    }
}
