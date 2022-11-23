<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritorySv;

/**
 * Class LocaleEsSv
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleEsSv extends LocaleEs
{
    public function territory()
    {
        return new TerritorySv();
    }
}
