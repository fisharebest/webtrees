<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritorySn;

/**
 * Class LocaleFfAdlmCm - Fulah
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleFfAdlmSn extends LocaleFfAdlm
{
    public function territory()
    {
        return new TerritorySn();
    }
}
