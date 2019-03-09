<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryBz;

/**
 * Class LocaleEsBz
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class LocaleEsBz extends LocaleEs
{
    public function territory()
    {
        return new TerritoryBz();
    }
}
