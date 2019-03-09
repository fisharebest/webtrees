<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryBr;

/**
 * Class LocaleEsBr
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class LocaleEsBr extends LocaleEs
{
    public function territory()
    {
        return new TerritoryBr();
    }
}
