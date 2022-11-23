<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryGb;

/**
 * Class LocaleGaGB
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleGaGB extends LocaleGa
{
    public function territory()
    {
        return new TerritoryGb();
    }
}
