<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryDo;

/**
 * Class LocaleEsDo
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleEsDo extends LocaleEs
{
    public function territory()
    {
        return new TerritoryDo();
    }
}
