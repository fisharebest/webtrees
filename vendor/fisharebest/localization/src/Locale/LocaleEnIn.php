<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryIn;

/**
 * Class LocaleEnIn
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleEnIn extends LocaleEn
{
    protected function digitsGroup()
    {
        return 2;
    }

    public function territory()
    {
        return new TerritoryIn();
    }
}
