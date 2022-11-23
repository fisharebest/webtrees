<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryTg;

/**
 * Class LocaleEeTg
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleEeTg extends LocaleEe
{
    public function territory()
    {
        return new TerritoryTg();
    }
}
