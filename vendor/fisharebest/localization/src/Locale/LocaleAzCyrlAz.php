<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryAz;

/**
 * Class LocaleAzCyrlAz
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleAzCyrlAz extends LocaleAzCyrl
{
    public function territory()
    {
        return new TerritoryAz();
    }
}
