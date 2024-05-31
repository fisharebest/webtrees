<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryCv;

/**
 * Class LocalePtCv
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocalePtCv extends LocalePt
{
    public function territory()
    {
        return new TerritoryCv();
    }
}
