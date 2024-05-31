<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryTw;

/**
 * Class LocaleZhHantTw
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleZhHantTw extends LocaleZhHant
{
    public function territory()
    {
        return new TerritoryTw();
    }
}
