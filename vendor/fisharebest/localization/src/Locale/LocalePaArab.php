<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Script\ScriptArab;

/**
 * Class LocalePaArab
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocalePaArab extends LocalePa
{
    public function numberSymbols()
    {
        return array(
            self::DECIMAL => self::ARAB_DECIMAL,
        );
    }

    protected function percentFormat()
    {
        return self::PLACEHOLDER . self::ARAB_PERCENT;
    }

    public function script()
    {
        return new ScriptArab();
    }
}
