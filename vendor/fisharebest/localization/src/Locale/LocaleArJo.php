<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryJo;

/**
 * Class LocaleArJo
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleArJo extends LocaleAr
{
    public function territory()
    {
        return new TerritoryJo();
    }
}
