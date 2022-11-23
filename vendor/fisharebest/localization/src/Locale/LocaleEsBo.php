<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryBo;

/**
 * Class LocaleEsBo
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleEsBo extends LocaleEs
{
    public function territory()
    {
        return new TerritoryBo();
    }
}
