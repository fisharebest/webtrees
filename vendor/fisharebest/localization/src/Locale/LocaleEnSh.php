<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritorySh;

/**
 * Class LocaleEnSh
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleEnSh extends LocaleEn
{
    public function territory()
    {
        return new TerritorySh();
    }
}
