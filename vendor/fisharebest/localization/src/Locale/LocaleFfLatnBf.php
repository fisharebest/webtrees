<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryBf;

/**
 * Class LocaleFfLatn - Fulah
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class LocaleFfLatnBf extends LocaleFfLatn
{
    public function territory()
    {
        return new TerritoryBf();
    }
}
