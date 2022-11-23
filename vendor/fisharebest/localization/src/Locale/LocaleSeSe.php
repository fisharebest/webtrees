<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritorySe;

/**
 * Class LocaleSeSe
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleSeSe extends LocaleSe
{
    public function territory()
    {
        return new TerritorySe();
    }
}
