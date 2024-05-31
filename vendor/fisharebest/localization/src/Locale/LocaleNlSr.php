<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritorySr;

/**
 * Class LocaleNlSr
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleNlSr extends LocaleNl
{
    public function territory()
    {
        return new TerritorySr();
    }
}
