<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryCl;

/**
 * Class LocaleEsCl
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleEsCl extends LocaleEs
{
    public function territory()
    {
        return new TerritoryCl();
    }
}
