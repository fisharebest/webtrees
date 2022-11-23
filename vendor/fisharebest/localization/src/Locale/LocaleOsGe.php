<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryGe;

/**
 * Class LocaleOsGe
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleOsGe extends LocaleOs
{
    public function territory()
    {
        return new TerritoryGe();
    }
}
