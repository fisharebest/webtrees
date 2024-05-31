<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryPw;

/**
 * Class LocaleEnPw
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleEnPw extends LocaleEn
{
    public function territory()
    {
        return new TerritoryPw();
    }
}
