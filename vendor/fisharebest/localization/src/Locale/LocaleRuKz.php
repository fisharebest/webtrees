<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryKz;

/**
 * Class LocaleRuKz
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleRuKz extends LocaleRu
{
    public function territory()
    {
        return new TerritoryKz();
    }
}
