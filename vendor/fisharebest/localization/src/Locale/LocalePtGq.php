<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryGq;

/**
 * Class LocalePtGq
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class LocalePtGq extends LocalePt
{
    public function territory()
    {
        return new TerritoryGq();
    }
}
