<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryKe;

/**
 * Class LocaleTeoKe
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleTeoKe extends LocaleTeo
{
    public function territory()
    {
        return new TerritoryKe();
    }
}
