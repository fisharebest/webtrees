<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryNl;

/**
 * Class LocaleNdsNl - Low German
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class LocaleNdsNl extends LocaleNds
{
    public function territory()
    {
        return new TerritoryNl();
    }
}
