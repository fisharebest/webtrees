<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryGe;

/**
 * Class LocaleOsGe
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class LocaleOsGe extends LocaleOs
{
    public function territory()
    {
        return new TerritoryGe();
    }

}
