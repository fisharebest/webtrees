<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryLi;

/**
 * Class LocaleGswLi
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleGswLi extends LocaleGsw
{
    public function territory()
    {
        return new TerritoryLi();
    }
}
