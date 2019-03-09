<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryVa;

/**
 * Class LocaleItVa
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class LocaleItVa extends LocaleIt
{
    public function territory()
    {
        return new TerritoryVa();
    }
}
