<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryCy;

/**
 * Class LocaleElCy
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleElCy extends LocaleEl
{
    public function territory()
    {
        return new TerritoryCy();
    }
}
