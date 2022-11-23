<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryKe;

/**
 * Class LocaleSwKe
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleSwKe extends LocaleSw
{
    public function territory()
    {
        return new TerritoryKe();
    }
}
