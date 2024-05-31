<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryBh;

/**
 * Class LocaleArBh
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleArBh extends LocaleAr
{
    public function territory()
    {
        return new TerritoryBh();
    }
}
