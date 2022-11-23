<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryCi;

/**
 * Class LocaleFrCi
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleFrCi extends LocaleFr
{
    public function territory()
    {
        return new TerritoryCi();
    }
}
