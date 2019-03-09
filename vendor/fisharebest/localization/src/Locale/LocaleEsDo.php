<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryDo;

/**
 * Class LocaleEsDo
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class LocaleEsDo extends LocaleEs
{
    public function territory()
    {
        return new TerritoryDo();
    }
}
