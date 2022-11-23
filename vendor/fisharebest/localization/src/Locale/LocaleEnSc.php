<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritorySc;

/**
 * Class LocaleEnSc
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleEnSc extends LocaleEn
{
    public function territory()
    {
        return new TerritorySc();
    }
}
