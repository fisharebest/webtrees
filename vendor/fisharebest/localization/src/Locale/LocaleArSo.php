<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritorySo;

/**
 * Class LocaleArSo
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleArSo extends LocaleAr
{
    public function territory()
    {
        return new TerritorySo();
    }
}
