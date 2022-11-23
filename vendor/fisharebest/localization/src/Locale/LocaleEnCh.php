<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryCh;

/**
 * Class LocaleEnCh
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleEnCh extends LocaleEn
{
    public function numberSymbols()
    {
        return array(
            self::GROUP => self::APOSTROPHE,
        );
    }

    public function territory()
    {
        return new TerritoryCh();
    }
}
