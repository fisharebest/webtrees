<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryKp;

/**
 * Class LocaleKoKp
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleKoKp extends LocaleKo
{
    public function territory()
    {
        return new TerritoryKp();
    }
}
