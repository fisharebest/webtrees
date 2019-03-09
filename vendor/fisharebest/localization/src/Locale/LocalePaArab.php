<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Script\ScriptArab;

/**
 * Class LocalePaArab
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class LocalePaArab extends LocalePa
{
    protected function digitsGroup()
    {
        return 2;
    }

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
