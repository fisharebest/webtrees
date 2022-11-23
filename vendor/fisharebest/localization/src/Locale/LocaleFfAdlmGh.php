<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryGh;

/**
 * Class LocaleFfAdlmGh - Fulah
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleFfAdlmGh extends LocaleFfAdlm
{
    public function territory()
    {
        return new TerritoryGh();
    }
}
