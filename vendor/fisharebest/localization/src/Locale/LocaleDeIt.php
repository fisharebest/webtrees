<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryIt;

/**
 * Class LocaleDeCh - Swiss High German
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleDeIt extends LocaleDe
{
    public function territory()
    {
        return new TerritoryIt();
    }
}
