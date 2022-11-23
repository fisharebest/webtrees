<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryNl;

/**
 * Class LocaleNdsNl - Low German
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleNdsNl extends LocaleNds
{
    public function territory()
    {
        return new TerritoryNl();
    }
}
