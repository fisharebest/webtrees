<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryLu;

/**
 * Class LocalePtLu
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocalePtLu extends LocalePt
{
    public function territory()
    {
        return new TerritoryLu();
    }
}
