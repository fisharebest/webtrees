<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryLk;

/**
 * Class LocaleTaLk
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleTaLk extends LocaleTa
{
    public function territory()
    {
        return new TerritoryLk();
    }
}
