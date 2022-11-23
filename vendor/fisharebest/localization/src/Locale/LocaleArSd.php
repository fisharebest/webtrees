<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritorySd;

/**
 * Class LocaleArSd
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleArSd extends LocaleAr
{
    public function territory()
    {
        return new TerritorySd();
    }
}
