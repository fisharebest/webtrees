<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritorySg;

/**
 * Class LocaleMsSg
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleMsSg extends LocaleMs
{
    public function territory()
    {
        return new TerritorySg();
    }
}
