<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryMd;

/**
 * Class LocaleRuMd
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleRuMd extends LocaleRu
{
    public function territory()
    {
        return new TerritoryMd();
    }
}
